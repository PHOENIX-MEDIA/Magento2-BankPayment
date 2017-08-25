define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Phoenix_BankPayment/payment/phoenix_bankpayment'
            },
            getFormCmsUrl: function () {
                return window.checkoutConfig.payment.phoenix_bankpayment.formcmsurl;
            },
            showAccounts: function () {
                return !window.checkoutConfig.payment.phoenix_bankpayment.formcmsurl && window.checkoutConfig.payment.phoenix_bankpayment.accounts.length > 0;
            },
            getInstructions: function () {
                return window.checkoutConfig.payment.phoenix_bankpayment.instructions;
            },
            getAccounts: function () {
                return window.checkoutConfig.payment.phoenix_bankpayment.accounts;
            },
            getCustomText: function () {
                return window.checkoutConfig.payment.phoenix_bankpayment.customtext;
            },
        });
    }
);
