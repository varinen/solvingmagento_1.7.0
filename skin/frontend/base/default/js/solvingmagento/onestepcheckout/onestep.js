var failureUrl = '/checkout/cart/',
    Checkout   = Class.create(),
//externally set variables:
    switchToMethod,
    currentPaymentMethod;

Checkout.prototype = {
    checkoutContainer: null,
    columnLeft: null,
    columnCenter: null,
    columnRight: null,
    steps: {},
    stepNames: ['login','billing', 'shipping', 'shipping_method', 'payment', 'review'],
    initialize: function (steps) {

        this.steps         = steps || {};
        this.columnLeft    = $('osc-column-left');
        this.columnCenter  = $('osc-column-center');
        this.columnRight   = $('osc-column-right');
        this.columnUp      = $('osc-column-up');
        this.columnBottom  = $('osc-column-bottom');

        this.moveTo(this.steps['login'], 'up');
        this.moveTo(this.steps['billing'], 'left');
        this.moveTo(this.steps['shipping'], 'center');
        this.moveTo(this.steps['review'], 'bottom');

        if (this.steps['shipping']
            && this.steps['shipping'].stepContainer
            && this.steps['shipping'].stepContainer.visible()
        ) {
            this.moveTo(this.steps['shipping_method'], 'right');
        } else {
            this.moveTo(this.steps['shipping_method'], 'center');
        }

        this.moveTo(this.steps['payment'], 'right');
    },

    moveTo: function(element, id) {
        var destination = this['column' + (id.charAt(0).toUpperCase() + id.slice(1))];
        if (element && element.stepContainer && destination) {
            var parent = element.stepContainer.up();
            if (destination !== parent) {
                destination.insert(element.stepContainer);
                parent.remove(element.stepContainer);
            }
        }

    },

    /**
     * Action in case of a failed (e.g., 404) ajax request
     */
    ajaxFailure: function(){
        location.href = failureUrl;
    },

    /**
     * Validates the form data in an address step
     *
     * @param type billing or shipping
     *
     * @returns {boolean}
     */
    validateAddress: function(type) {

        if (type  !== 'billing' && type !== 'shipping') {
            return false;
        }
        var validationResult         = false,
            newAddressFormValidation = false,
            validator                = new Validation('co-' + type + '-form');

        newAddressFormValidation = validator.validate();

        $$('div.advice-required-entry-' + type + '-address-id').each(
            function(element) {
                $(element).hide();
            }
        );
        if ($$('input[name="' + type+ '_address_id"]')
            && $$('input[name="' + type+ '_address_id"]').length > 0
        ) {
            $$('input[name="' + type + '_address_id"]').each(
                function(element) {
                    if ($(element).checked) {
                        validationResult = true;
                    }
                }
            );
            if (!validationResult) {
                $$('div.advice-required-entry-' + type + '-address-id').each(
                    function(element) {
                        $(element).show();
                    }
                );
            }
        } else {
            validationResult = true;
        }
        return (newAddressFormValidation && validationResult);
    },

    /**
     * Checks if the checkout method is selected, when the selection is there
     *
     * @returns {boolean}
     */
    validateCheckoutMethod: function() {
        var valid = true;
        $$('div.advice-required-entry-checkout_method').each(
            function(element) {
                $(element).hide();
            }
        )
        if ($$('input[name="checkout_method"]').length > 0) {
            valid = false;
            $$('input[name="checkout_method"]').each(
                function(element) {
                    if ($(element).checked) {
                        valid = true;
                    }
                }
            );

            if (!valid) {
                $$('div.advice-required-entry-checkout_method').each(
                    function(element) {
                        $(element).show();
                    }
                )
            }
        }

        return valid;
    },

    /**
     * Shipping Method step validation
     *
     * @todo condition validation on non-virtual quotes
     *
     * @returns {boolean}
     */
    validateShippingMethod: function() {
        var valid = true;
        $$('li div.advice-required-entry-shipping_method').each(
            function(element) {
                $(element).hide();
            }
        );

        if ($$('input[name="shipping_method"]').length > 0) {
            valid = false;
            $$('input[name="shipping_method"]').each(
                function(element) {
                    if ($(element).checked) {
                        valid = true;
                    }
                }
            );

            if (!valid) {
                $$('li div.advice-required-entry-shipping_method').each(
                    function(element) {
                        $(element).show();
                    }
                );
            }
        }

        return valid;
    },

    /**
     * Payment Method step validation
     *
     * @returns {boolean}
     */
    validatePaymentMethod: function() {
        return true;
    },

    /**
     * Shipping Address step validation
     *
     * @returns {boolean}
     */
    validateBillingAddress: function() {
        return this.validateAddress('billing');
    },

    /**
     * Billing Address step validation
     *
     * @returns {boolean}
     */
    validateShippingAddress: function() {
        return this.validateAddress('shipping');
    },

    /**
     * Validates the checkout steps
     *
     * @param steps an array with elements comprising the checkout step names.
     *              word capitalized: e.g. BillingAddress, or CheckoutMethod
     *
     * @returns {boolean}
     */
    validateCheckoutSteps: function(steps) {
        var step, result = true;

        for (step in steps) {
            if (steps.hasOwnProperty(step)) {
                if (this['validate' + steps[step]]) {
                    result = this['validate' + steps[step]]() && result;
                }
            }
        }

        return result;
    },

    /**
     * Toggles the display state of loading elements
     *
     * @param element id of the target loader
     * @param mode    flag indicating hiding or showing of the element
     */
    toggleLoading: function(element, mode) {
        if ($(element) && mode) {
            Element.show($(element));
        } else if ($(element)) {
            Element.hide($(element));
        }
    },

    setResponse: function(response) {
        var step;
        if (response.error){
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                if (window.billingRegionUpdater) {
                    billingRegionUpdater.update();
                }
                alert(response.message.join("\n"));
            }
            return false;
        }
        for (step in response.update_step) {
            if (response.update_step.hasOwnProperty(step) && ($('checkout-load-' + step))) {
                $('checkout-load-' + step).update(response.update_step[step]);
            }
        }
    }
}

/**
 * Step class
 * @type {*}
 */
var Step = Class.create();

Step.prototype = {
    stepContainer: null,
    initialize: function(id) {
        this.stepContainer = $('checkout-step-' + id);
        this.init();
    },
    init: function() {
        return;
    }
}

/**
 * Login step class
 *
 * @type {*}
 */
var Login = Class.create();

Login.prototype = {
    stepContainer: null,
    /**
     * Required initialization
     *
     * @param id step id
     */
    initialize: function(id, saveMethodUrl) {
        this.saveMethodsUrl = saveMethodUrl || '/checkout/onestep/saveMethod';
        this.stepContainer = $('checkout-step-' + id);

        /**
         * Observe the customer choice regarding an existing address
         */
        $$('input[name="checkout_method"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'click',
                    this.setMethod.bindAsEventListener(this)
                );
            }.bind(this)
        );
    },

    /**
     * Saves the checkout method to the quote
     *
     * @param event
     */
    setMethod: function(event) {
        var value = Event.element(event).value;
        $$('div.advice-required-entry-checkout_method').each(
            function(element) {
                $(element).hide();
            }
        )
        this.startLoader();
        var request = new Ajax.Request(
            this.saveMethodsUrl,
            {
                method:     'post',
                onComplete: this.stopLoader.bind(this),
                onFailure:  checkout.ajaxFailure.bind(checkout),
                parameters: {checkout_method: value}
            }
        );
    },

    /**
     * Hides the login step loader
     */
    stopLoader: function () {
        if (checkout) {
            checkout.toggleLoading('login-please-wait', false);
        }

    },

    /**
     * Shows the loging step loader
     */
    startLoader: function () {
        if (checkout) {
            checkout.toggleLoading('login-please-wait', true);
        }

    }

}


var Billing = Class.create();

Billing.prototype = {
    stepContainer: null,

    /**
     * Required initialization
     *
     * @param id step id
     */
    initialize: function(id) {
        this.stepContainer = $('checkout-step-' + id);

        /**
         * Observe the customer choice regarding an existing address
         */
        $$('input[name="billing_address_id"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'change',
                    this.newBillingAddress.bindAsEventListener(this)
                );
            }.bind(this)
        );

        /**
         * Observe changes in the checkout method,
         */
        $$('input[name="checkout_method"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'change',
                    this.togglePassword.bindAsEventListener(this)
                );
            }.bind(this)
        );
    },

    /**
     * Toggles the new billing address form display depending on customer's
     * decision to use an existing address or to enter a new one.
     * Works for logged in customers only
     *
     * @param event
     */
    newBillingAddress: function(event) {
        var value;
        $$('input[name="billing_address_id"]').each(
            function(element) {
                if (!!element.checked) {
                    value = !!parseInt(element.value);
                }
            }
        );
        if (!value) {
            Element.show('billing-new-address-form');
        } else {
            Element.hide('billing-new-address-form');
        }
    },


    /**
     * Shows or hides the password field depending on the chosen checkout method
     *
     * @param event
     */
    togglePassword: function(event) {
        if (!$('register-customer-password')) {
            return;
        }
        if ($('billing-new-address-form').visible) {
            if ($('login:register') && $('login:register').checked) {
                Element.show('register-customer-password');
                return;
            }
        }

        Element.hide('register-customer-password');
    }

}
var Shipping = Class.create();

Shipping.prototype = {
    stepContainer: null,

    /**
     * Required initialization
     *
     * @param id step id
     */
    initialize: function(id) {
        this.stepContainer = $('checkout-step-' + id);
        /**
         * Observe the customer choice regarding an existing address
         */
        $$('input[name="shipping_address_id"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'change',
                    this.newShippingAddress.bindAsEventListener(this)
                );
            }.bind(this)
        );

        /**
         * Observe the state of the "use billing address for shipping" option
         * when initializing the shipping step
         */
        $$('input[name="billing[use_for_shipping]"]').each(
            function(element) {
                if (!!element.checked) {
                    $('shipping:same_as_billing').checked = !!element.value;
                }
            }
        );

        /**
         * Start observing the change of the "use billing address for shipping" option
         */
        $$('input[name="billing[use_for_shipping]"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'change',
                    this.toggleSameAsBilling.bindAsEventListener(this)
                );
            }.bind(this)
        );

        /**
         * Set the shipping form to the data of the billing one in case the customer
         * select the "use billing address" checkbox
         */
        Event.observe(
            $('shipping:same_as_billing'),
            'change',
            function(event) {
                if (Event.element(event).checked) {
                    this.setSameAsBilling(true);
                }
            }.bind(this)
        );
    },

    /**
     * Toggles the new shipping address form display depending on customer's
     * decision to use an existing address or to enter a new one.
     * Works for logged in customers only
     *
     * @param event
     */
    newShippingAddress: function(event) {
        var value;
        $$('input[name="shipping_address_id"]').each(
            function(element) {
                if (!!element.checked) {
                    value = !!parseInt(element.value);
                    var billingId = element.id.replace(/^shipping/, 'billing');
                    if (!$(billingId).checked) {
                        $('shipping:same_as_billing').checked = false;
                    }
                }
            }
        );
        if (!value) {
         //   $('shipping:same_as_billing').checked = false;
            Element.show('shipping-new-address-form');
        } else {
            Element.hide('shipping-new-address-form');
        }
    },

    /**
     * Responds to the customer's selecting the option "use billing address for shipping".
     * Copies the content of the billing address form into the shipping form if yes.
     *
     * Resets the shipping address form if no.
     *
     * @param event
     */
    toggleSameAsBilling: function(event) {
        var value = false;
        $$('input[name="billing[use_for_shipping]"]').each(
            function(element) {
                if (!!element.checked) {
                    value = !!parseInt(element.value);
                }
            }
        );

        //value === true : same as billing
        //value === false : different shipping address

        if (value) {
            this.setSameAsBilling(true);
        } else {
            /**
             * @todo Is really necessary?
             */
            this.resetAddress();
        }

    },

    /**
     * Copies the data from the billing form into the shipping one
     *
     * @param flag flag to copy the data or not
     */
    setSameAsBilling: function(flag) {
        if (flag) {
            var arrElements = Form.getElements($('co-shipping-form'));
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var billingId = arrElements[elemIndex].id.replace(/^shipping/, 'billing');
                    if ((billingId === 'billing:region_id') && (shippingRegionUpdater)) {
                        shippingRegionUpdater.update();
                    }

                    arrElements[elemIndex].value = ($(billingId) && $(billingId).value) ? $(billingId).value : '';
                    if ($(billingId) && !!$(billingId).checked) {
                        arrElements[elemIndex].checked = true;
                    }

                    if ($(billingId)
                        && ($(billingId).name == 'billing_address_id')
                        && (!$(billingId).value)
                        && ($(billingId).checked)
                    ) {
                        this.newShippingAddress();
                    }

                }
            }
        } else {
            $('shipping:same_as_billing').checked = false;
        }
    },

    /**
     * Sets shipping form input values to nothing (except shipping_address_id radio options)
     */
    resetAddress: function() {
        var arrElements = Form.getElements($('co-shipping-form'));
        for (var elemIndex in arrElements) {
            if (!!arrElements[elemIndex].value) {
                if ((arrElements[elemIndex].name !== 'shipping_address_id')
                    && (arrElements[elemIndex].name !== 'shipping[address_id]')
                ) {
                    arrElements[elemIndex].value = '';
                }
            }
        }
    }
}
var ShippingMethod = Class.create();

ShippingMethod.prototype = {
    stepContainer: null,
    initialize: function(id, saveAddressesUrl, saveShippingMethodUrl) {
        this.stepContainer         = $('checkout-step-' + id);
        this.saveAddressesUrl      = saveAddressesUrl || '/checkout/onestep/saveAddresses';
        this.saveShippingMethodUrl = saveShippingMethodUrl || '/checkout/onestep/saveShippingMethod';
        this.onUpdate              = this.updateMethods.bindAsEventListener(this);
        this.onSave                = this.saveMethod.bindAsEventListener(this);

        /**
         * Load methods when user clicks this element
         */
        Event.observe(
            $('reload-shipping-method-button'),
            'click',
            this.loadMethods.bindAsEventListener(this)
        );
        this.addValidationAdvice();

        /**
         * Observe the customer choice regarding an existing address
         */
        $$('input[name="shipping_method"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'click',
                    this.setMethod.bindAsEventListener(this)
                );
            }.bind(this)
        );
    },

    /**
     * Sets the shipping method and posts it to the quote
     */
    setMethod: function () {

        var parameters = Form.serialize('co-shipping-method-form');

        $$('li div.advice-required-entry-shipping_method').each(
            function(element) {
                $(element).hide();
            }
        );

        if (checkout.validateCheckoutSteps(['CheckoutMethod'])) {
            this.startLoader();
            var request = new Ajax.Request(
                this.saveShippingMethodUrl,
                {
                    method:     'post',
                    onComplete: this.stopLoader.bind(this),
                    onSuccess:  this.onSave,
                    onFailure:  checkout.ajaxFailure.bind(checkout),
                    parameters: parameters
                }
            );
        }
    },

    /**
     * Actions after a shipping method is successfully posted to the quote
     *
     * @param transport response from the controller
     */
    saveMethod: function(transport){
        var response = {};
        if (transport && transport.responseText){
            response = JSON.parse(transport.responseText);
        }
        if (checkout) {
            checkout.setResponse(response);
        }
    },

    /**
     * Updates the available shipping method selection
     */
    loadMethods: function() {
        this.saveAddresses();
        this.addValidationAdvice();
    },

    /**
     * Saves the billing and shipping addresses and gets a valid selection of shipping methods
     */
    saveAddresses: function() {
        var parameters = {},
            valid      = false;

        if ($('shipping:same_as_billing').checked && shipping) {
            shipping.setSameAsBilling(true);
        }

        /**
         * Validate previous steps, excluding shipping method and payment method
         */
        if (checkout) {
            valid = checkout.validateCheckoutSteps(['CheckoutMethod', 'BillingAddress', 'ShippingAddress']);
        }

        if (valid) {
            this.startLoader();

            parameters =  Form.serialize('co-billing-form') + '&' + Form.serialize('co-shipping-form');

            var request = new Ajax.Request(
                this.saveAddressesUrl,
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

    /**
     * Updates the shipping method step with html represeting a selection of available shipping methods
     *
     * @param transport
     *
     * @returns {boolean}
     */
    updateMethods: function(transport){
        var response = {};
        if (transport && transport.responseText){
            response = JSON.parse(transport.responseText);
        }

        if (checkout) {
            checkout.setResponse(response);
        }
    },

    /**
     * Hides the login step loader
     */
    stopLoader: function () {
        if (checkout) {
            checkout.toggleLoading('shipping_method-please-wait', false);
        }

    },

    /**
     * Shows the loging step loader
     */
    startLoader: function () {
        if (checkout) {
            checkout.toggleLoading('shipping_method-please-wait', true);
        }

    },

    addValidationAdvice: function() {
        var advice, clone;
        //destroy already existing elements
        $$('li div.advice-required-entry-shipping_method').each(
            function(element) {
                Element.remove(element);
            }
        );
        if ($('shipping_method-advice-source')) {
            advice = $('shipping_method-advice-source').firstDescendant();
            if (advice) {

                $$('input[name="shipping_method"]').each(
                    function(element) {
                        clone = Element.clone(advice, true);
                        $(element).up().appendChild(clone);
                    }
                );
            }
        }

    }
}
var Payment = Class.create();

Payment.prototype = {
    beforeInitFunc:     $H({}),
    afterInitFunc:      $H({}),
    beforeValidateFunc: $H({}),
    afterValidateFunc:  $H({}),
    stepContainer:      null,
    currentMethod:      null,
    form:               null,

    /**
     * Required initialization
     *
     * @param id
     * @param saveAddressesUrl
     */
    initialize: function(id, saveAddressesUrl) {
        this.stepContainer = $('checkout-step-' + id);
        this.form = 'co-payment-form';

        /**
         * Load methods when user clicks this element
         */
        Event.observe(
            $('reload-payment-method-button'),
            'click',
            this.loadMethods.bindAsEventListener(this)
        );

    },

    loadMethods: function() {
        this.postCheckoutData();
    },

    postCheckoutData: function() {
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
     * Initializaes the payment method selection
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
    }

}

var Review         = Class.create(Step);

var login          = new Login('login'),
    billing        = new Billing('billing'),
    shipping       = new Shipping('shipping'),
    shippingMethod = new ShippingMethod('shipping_method'),
    payment        = new Payment('payment'),
    review         = new Review('review'),
    checkout       = new Checkout(
        {
            'login': login,
            'billing': billing,
            'shipping': shipping,
            'shipping_method': shippingMethod,
            'payment': payment,
            'review': review
        }
    );
if (currentPaymentMethod) {
    payment.currentMethod = currentPaymentMethod;
}

payment.init();

if (switchToMethod) {
    payment.switchMethod(switchToMethod);
}
