var Review = Class.create();

Review.prototype = {
    readyToSave: false,
    getStepUpdateUrl: null,
    stepId: 'review',
    initialize: function(id, getStepUpdateUrl, submitOrderUrl) {
        this.stepContainer    = $('checkout-step-' + id);
        this.getStepUpdateUrl = getStepUpdateUrl || '/checkout/onestep/updateOrderReview';
        this.submitOrderUrl   = submitOrderUrl  || '/checkout/onestep/submitOrder';
        this.onUpdate         = this.reviewUpdated.bindAsEventListener(this);
        this.readyToSave      = false;

        // update the review without validating previous steps
        Event.observe($('checkout-review-update'), 'click', this.updateReview.bindAsEventListener(this, false));

        //update with validating before submitting the order
        Event.observe($('order_submit_button'), 'click', this.submit.bindAsEventListener(this));
    },

    submit: function(event) {
        var parameters = {},
            postUrl    = this.getStepUpdateUrl,
            onSuccess  = this.onUpdate;

        /**
         * Submit order instead of upating only
         */
        if (this.readyToSave) {
            postUrl   = this.submitOrderUrl;
            onSuccess = this.onSuccess;
        }

        if (checkout && checkout.validateReview(true)) {
            this.startLoader();

            this.readyToSave = true;

            parameters = Form.serialize('co-billing-form')
                + '&' + Form.serialize('co-shipping-form')
                + '&' + Form.serialize('co-shipping-method-form')
                + '&' + Form.serialize('co-payment-form');

            var request = new Ajax.Request(
                postUrl,
                {
                    method:     'post',
                    onComplete: this.stopLoader.bind(this),
                    onSuccess:  onSuccess,
                    onFailure:  checkout.ajaxFailure.bind(checkout),
                    parameters: parameters
                }
            );
        }
    },

    updateReview: function(event, noValidation) {
        var parameters = {},
            valid      = false;

        noValidation = !!noValidation;

        valid = (checkout && checkout.validateReview(!noValidation));

        if (valid) {
            this.startLoader();

            parameters =  Form.serialize('co-billing-form')
                + '&' + Form.serialize('co-shipping-form')
                + '&' + Form.serialize('co-shipping-method-form')
                + '&' + Form.serialize('co-payment-form');

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
     * Updates the HTMl of the review step
     *
     * @param transport
     *
     * @returns {boolean}
     */
    reviewUpdated: function(transport){
        var response = {};
        if (transport && transport.responseText){
            response = JSON.parse(transport.responseText);
        }

        if (!response.error && this.readyToSave) {
            if ($('order_submit_button')) {
                $('order_submit_button').title = checkout.buttonSaveText;
                $('order_submit_button').down().down().update(checkout.buttonSaveText);
            }
        } else {
            this.readyToSave = false;
            if ($('order_submit_button')) {
                $('order_submit_button').title = checkout.buttonUpdateText;
                $('order_submit_button').down().down().update(checkout.buttonUpdateText);
            }
        }
        //the response is extected to contain the update HTMl for the payment step
        if (checkout) {
            checkout.setResponse(response);
        }
    }
}
