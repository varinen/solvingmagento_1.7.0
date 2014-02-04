var Checkout = Class.create();

Checkout.prototype = {
    checkoutContainer: null,
    columnLeft: null,
    columnCenter: null,
    columnRight: null,
    steps: {},
    stepNames: ['login','billing', 'shipping', 'shipping_method', 'payment', 'review'],
    initialize: function (steps) {

        this.steps        = steps || {};
        this.columnLeft   = $('osc-column-left');
        this.columnCenter = $('osc-column-center');
        this.columnRight  = $('osc-column-right');


        this.moveTo(this.steps['login'], 'left');
        this.moveTo(this.steps['billing'], 'left');
        this.moveTo(this.steps['shipping'], 'center');

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

    }
}
var Step = Class.create();

Step.prototype = {
    stepContainer: null,
    initialize: function(id) {
        this.stepContainer = $('checkout-step-' + id);
    }
}

var Login          = Class.create(Step);
var Billing        = Class.create(Step);
var Shipping       = Class.create(Step);
var ShippingMethod = Class.create(Step);
var Payment        = Class.create(Step);
var Review         = Class.create(Step);
