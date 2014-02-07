var failureUrl = '/checkout/cart/';
var Checkout   = Class.create();

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
        if ($$('input[name="' + type+ '_address_id"]')) {
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
        }
        return (newAddressFormValidation && validationResult);
    }
}
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

var Login          = Class.create(Step);


var Billing        = Class.create();

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
     * Set shipping form input values to nothing (except shipping_address_id radio options)
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
    initialize: function(id, saveAddressesUrl) {
        this.stepContainer = $('checkout-step-' + id);
        this.saveAddressesUrl = saveAddressesUrl || 'checkout/onestep/saveAddresses';
        this.onSave = this.updateMethods.bindAsEventListener(this);

        /**
         * Load methods when user clicks this element
         */
        Event.observe(
            $('reload-shipping-method-button'),
            'click',
            this.loadMethods.bindAsEventListener(this)
        );
    },

    loadMethods: function() {
        this.saveAddresses();
    },

    saveAddresses: function() {
        var parameters = {},
            validB     = false,
            validS     = false,
            valid      = false;

        if ($('shipping:same_as_billing').checked && shipping) {
            shipping.setSameAsBilling(true);
        }

        if (checkout) {
            validB = checkout.validateAddress('billing');
            validS = checkout.validateAddress('shipping');
            //needed to invoke both validations, hence two extra variables:
            valid = validB && validS
        }

        if (valid) {
            this.toggleLoading(true);

            parameters.billing  = Form.serialize('co-billing-form');
            parameters.shipping = Form.serialize('co-shipping-form');

            var request = new Ajax.Request(
                this.saveAddressesUrl,
                {
                    method:     'post',
                    onComplete: this.toggleLoading(false),
                    onSuccess:  this.onSave,
                    onFailure:  checkout.ajaxFailure.bind(checkout),
                    parameters: JSON.stringify(parameters)
                }
            );
        }
    },

    toggleLoading: function(flag) {

    },

    updateMethods: function(transport){
        var response = {};
        if (transport && transport.responseText){
            response = JSON.parse(transport.responseText);
        }

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
        if (response.update_step.shipping_method) {
            $('checkout-shipping-method-load').update(response.update_step.shipping_method);
        }
        //payment.initWhatIsCvvListeners();
    }
}
var Payment        = Class.create(Step);
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