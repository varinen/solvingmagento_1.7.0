var Checkout,
    switchToPaymentMethod,
    currentPaymentMethod,
    login          = new Login('login'),
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


/**
 * Extend *_method step object prototypes with shared properties
 */
for (var property in MethodStep) {
    if (!Payment.prototype[property]) {
        Payment.prototype[property] = MethodStep[property];
    }
    if (!ShippingMethod.prototype[property]) {
        ShippingMethod.prototype[property] = MethodStep[property];
    }
    if (!Login.prototype[property]) {
        Login.prototype[property] = MethodStep[property];
    }

    if (!Review.prototype[property]) {
        Review.prototype[property] = MethodStep[property];
    }
}



if (currentPaymentMethod) {
    payment.currentMethod = currentPaymentMethod;
}

payment.init();

if (switchToPaymentMethod) {
    payment.switchMethod(switchToPamentMethod);
}

review.updateReview(this, true);