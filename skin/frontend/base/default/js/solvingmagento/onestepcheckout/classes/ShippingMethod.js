var ShippingMethod = Class.create();

ShippingMethod.prototype = {
    stepContainer: null,
    stepId: 'shipping_method',
    initialize: function(id, getStepUpdateUrl, saveStepData) {
        this.stepContainer         = $('checkout-step-' + id);
        if (!this.stepContainer) {
            return;
        }
        this.getStepUpdateUrl      = getStepUpdateUrl || '/checkout/onestep/updateShippingMethods';
        this.saveShippingMethodUrl = saveStepData || '/checkout/onestep/saveShippingMethod';
        this.onUpdate              = this.updateMethods.bindAsEventListener(this);
        this.onSave                = this.methodSaved.bindAsEventListener(this);

        /**
         * Load methods when user clicks this element
         */
        Event.observe(
            $('reload-shipping-method-button'),
            'click',
            this.getStepUpdate.bindAsEventListener(this)
        );

        this.addValidationAdvice();

        /**
         * Post the selected method to the controller
         */
        $$('input[name="shipping_method"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'click',
                    this.saveMethod.bindAsEventListener(this)
                );
            }.bind(this)
        );
    },

    /**
     * Sets the shipping method and posts it to the quote
     */
    saveMethod: function () {
        var parameters = Form.serialize('co-shipping-method-form');

        if ($('shipping:same_as_billing').checked && shipping) {
            shipping.setSameAsBilling(true);
        }

        if (checkout
            && checkout.validateCheckoutSteps(
            ['CheckoutMethod', 'BillingAddress', 'ShippingAddress', 'ShippingMethod']
        )
            ) {
            this.postData(
                this.saveShippingMethodUrl,
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
        //This will update the shipping method selection - available shipping methods
        // depend on the selected shipping address
        if (checkout) {
            checkout.setResponse(response);
        }
    },

    /**
     * Saves the billing and shipping addresses and gets a valid selection of shipping methods
     */
    getStepUpdate: function() {
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
                this.getStepUpdateUrl,
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
     * Updates the method step with html represeting a selection of available methods
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
        //the response is extected to contain the update HTMl for the payment step
        if (checkout) {
            checkout.setResponse(response);
        }
    },

    methodsUpdated: function () {
        this.addValidationAdvice();

        /**
         * Post the selected method to the controller
         */
        $$('input[name="shipping_method"]').each(
            function(element) {
                Event.observe(
                    $(element),
                    'click',
                    this.saveMethod.bindAsEventListener(this)
                );
            }.bind(this)
        );
    }
};



/**
 * Extend *_method step object prototypes with shared properties
 */
for (var property in MethodStep) {
    if (!ShippingMethod.prototype[property]) {
        ShippingMethod.prototype[property] = MethodStep[property];
    }
}
