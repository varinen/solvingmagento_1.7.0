/**
 * Login step class
 *
 * @type {*}
 */
var Login = Class.create();

Login.prototype = {
    stepContainer: null,
    stepId: 'checkout_method',
    /**
     * Required initialization
     *
     * @param id step id
     */
    initialize: function(id, saveMethodUrl) {
        this.saveMethodsUrl = saveMethodUrl || '/checkout/onestep/saveMethod';
        this.onSave         = this.methodSaved.bindAsEventListener(this);
        this.stepContainer  = $('checkout-step-' + id);

        /**
         * Observe the customer choice regarding an existing address
         */
        $$('input[name="checkout_method"]').each(
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
     * Saves the checkout method to the quote
     *
     * @param event
     */
    saveMethod: function(event) {
        var value = Event.element(event).value;
        this.postData(
            this.saveMethodsUrl,
            {checkout_method: value},
            'div.advice-required-entry-' + this.stepId
        );
    }
}