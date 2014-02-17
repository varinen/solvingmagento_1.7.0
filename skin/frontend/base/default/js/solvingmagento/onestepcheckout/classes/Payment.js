var Payment = Class.create();

Payment.prototype = {
    beforeInitFunc:       $H({}),
    afterInitFunc:        $H({}),
    beforeValidateFunc:   $H({}),
    afterValidateFunc:    $H({}),
    getPaymentMethodsUrl: null,
    savePaymentMethodUrl: null,
    stepContainer:        null,
    currentMethod:        null,
    form:                 null,
    getPaymentMethodsUrl: null,
    stepId: 'payment_method',

    /**
     * Required initialization
     *
     * @param id
     * @param saveAddressesUrl
     */
    initialize: function(id, getPaymentMethodsUrl, savePaymentMethodUrl) {
        this.stepContainer        = $('checkout-step-' + id);
        this.form                 = 'co-payment-form';
        this.getPaymentMethodsUrl = getPaymentMethodsUrl || '/checkout/onestep/updatePaymentMethods';
        this.savePaymentMethodUrl = savePaymentMethodUrl || '/checkout/onestep/savePaymentMethod';
        this.onUpdate             = this.updateMethods.bindAsEventListener(this);
        this.onSave               = this.methodSaved.bindAsEventListener(this);

        /**
         * Load methods when user clicks this element
         */
        Event.observe(
            $('reload-payment-method-button'),
            'click',
            this.getMethods.bindAsEventListener(this)
        );

        this.addValidationAdvice();

    },

    getMethods: function() {
        var parameters = {},
            valid      = false;

        if ($('shipping:same_as_billing').checked && shipping) {
            shipping.setSameAsBilling(true);
        }

        /**
         * Validate previous steps, excluding shipping method and payment method
         */
        if (checkout) {
            valid = checkout.validateCheckoutSteps(
                ['CheckoutMethod', 'BillingAddress', 'ShippingAddress', 'ShippingMethod']
            );
        }

        if (valid) {
            this.startLoader();

            parameters =  Form.serialize('co-billing-form') +
                '&' + Form.serialize('co-shipping-form') +
                '&' + Form.serialize('co-shipping-method-form');

            var request = new Ajax.Request(
                this.getPaymentMethodsUrl,
                {
                    method:     'post',
                    onComplete: this.stopLoader.bind(this),
                    onSuccess:  this.onUpdate,
                    onFailure:  checkout.ajaxFailure.bind(checkout),
                    parameters: parameters
                }
            );
        }
    },

    saveMethod: function() {
        var parameters = Form.serialize('co-payment-method-form');
        if (checkout
            && checkout.validateCheckoutSteps(
            ['CheckoutMethod', 'BillingAddress', 'ShippingAddress', 'ShippingMethod', 'PaymentMethod']
        )
            ) {
            this.postData(
                this.savePaymentMethodUrl,
                parameters,
                'li div.advice-required-entry-' + this.stepId
            );
        }
    },

    /**
     * Actions after a shipping method is successfully posted to the quote
     *
     * @param transport response from the controller
     */
    methodSaved: function(transport){
        var response = {};
        if (transport && transport.responseText){
            response = JSON.parse(transport.responseText);
        }

        /*
         * if there is an error in payment, need to show error message
         */
        if (response.error) {
            if (response.fields) {
                var fields = response.fields.split(',');
                for (var i=0;i<fields.length;i++) {
                    var field = null;
                    if (field = $(fields[i])) {
                        Validation.ajaxError(field, response.error);
                    }
                }
                return;
            }
            alert(response.error);
            return;
        }

        //This will update the payment method selection - available payment methods
        // depend on the selected shipping method
        if (checkout) {
            checkout.setResponse(response);
        }
    },

    /**
     * Adds a function to the before init hash
     *
     * @param code function name
     * @param func function itself
     */
    addBeforeInitFunction : function(code, func) {
        this.beforeInitFunc.set(code, func);
    },

    /**
     * Invokes the before init functions
     */
    beforeInit : function() {
        (this.beforeInitFunc).each(
            function(init) {
                (init.value)();
            }
        );
    },

    /**
     * Initializes the payment method selection
     */
    init : function () {
        this.beforeInit();

        var elements = Form.getElements(this.form), method = null;

        for (var i = 0; i < elements.length; i++) {
            if (elements[i].name === 'payment[method]') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete','off');
        }
        if (method) {
            this.switchMethod(method);
        }
        this.afterInit();
    },

    /**
     * Adds a function to the after init hash
     *
     * @param code function name
     * @param func function itself
     */
    addAfterInitFunction : function(code, func) {
        this.afterInitFunc.set(code, func);
    },

    /**
     * Invokes the after init functions
     */
    afterInit : function() {
        (this.afterInitFunc).each(
            function(init) {
                (init.value)();
            }
        );
    },

    /**
     * Switches on the selected method, toggles the method form visibility
     *
     * @param method method name
     */
    switchMethod: function(method) {
        if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            this.changeVisible(this.currentMethod, true);
            $('payment_form_'+this.currentMethod).fire(
                'payment-method:switched-off',
                {method_code : this.currentMethod}
            );
        }
        if ($('payment_form_'+method)) {
            this.changeVisible(method, false);
            $('payment_form_'+method).fire('payment-method:switched', {method_code : method});
        } else {
            //Event fix for payment methods without form like "Check / Money order"
            document.body.fire('payment-method:switched', {method_code : method});
        }
        if (method) {
            this.lastUsedMethod = method;
        }
        this.currentMethod = method;
    },

    /**
     * Toggles visibility of the method's form
     *
     * @param method method name
     * @param mode   toggle flag
     */
    changeVisible: function(method, mode) {
        var block = 'payment_form_' + method;
        [block + '_before', block, block + '_after'].each(function(el) {
            element = $(el);
            if (element) {
                element.style.display = (mode) ? 'none' : '';
                element.select('input', 'select', 'textarea', 'button').each(function(field) {
                    field.disabled = mode;
                });
            }
        });
    },

    /**
     * Adds a function to the before validation hash
     *
     * @param code function name
     * @param func function itself
     */
    addBeforeValidateFunction : function(code, func) {
        this.beforeValidateFunc.set(code, func);
    },

    /**
     * Invokes the before validation functions
     *
     * @returns {boolean}
     */
    beforeValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    methodSaved: function() {

    },

    /**
     * Adds validation advice DOM elements to radio buttons
     */
    addValidationAdvice: function() {
        var advice, clone;
        //destroy already existing elements
        $$('dt div.advice-required-entry-' + this.stepId).each(
            function(element) {
                Element.remove(element);
            }
        );
        if ($(this.stepId + '-advice-source')) {
            advice = $(this.stepId + '-advice-source').firstDescendant();
            if (advice) {

                $$('input[name="payment[method]"]').each(
                    function(element) {
                        clone = Element.clone(advice, true);
                        $(element).up().appendChild(clone);
                    }
                );
            }
        }

    }
};


/**
 * Extend *_method step object prototypes with shared properties
 */
for (var property in MethodStep) {
    if (!Payment.prototype[property]) {
        Payment.prototype[property] = MethodStep[property];
    }
}
