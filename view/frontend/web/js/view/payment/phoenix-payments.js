define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'phoenix_bankpayment',
                component: 'Phoenix_BankPayment/js/view/payment/method-renderer/phoenix_bankpayment-method'
            }
        );
        return Component.extend({});
    }
);