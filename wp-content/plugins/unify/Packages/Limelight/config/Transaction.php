<?php

return [
    'apiRules' => [
        'subscriptionUpdate'      => [
            'values' => 'required',
            'action' => 'required',
        ],
        'upsellStopRecurring'     => [
            'values' => 'required|numeric',
            'action' => 'required|numeric',
        ],
        'repostToFulfillment'     => [
            'order_Id' => 'required|numeric',
        ],
        'getAlternativeProvider'  => [
            'alt_pay_type'  => 'in:amazon,paypal,icepay',
            'campaign_id'   => 'required|numeric',
            'return_url'    => 'required',
            'cancel_url'    => 'required',
            'amount'        => 'required|numeric',
            'shipping_id'   => 'numeric',
            'products'      => '',
            'product_qty'   => '',
            'product_price' => '',
            'bill_country'  => 'required_if:alt_pay_type,icepay',
        ],
        'shippingMethodView'      => [
            'shipping_id' => 'required|numeric',
        ],
        'shippingMethodFind'      => [
            'campaign_id' => 'required',
            'search_type' => 'in:any,all',
            'return_type' => 'in:,shipping_method_view',
            'criteria'    => 'string',
        ],
        'couponValidate'          => [
            'campaign_Id' => 'required|numeric',
            'shipping_id' => 'required|numeric',
            'email'       => 'required|email',
            'product_ids' => 'required',
            'promo_code'  => 'required',
        ],
        'gatewayView'             => [
            'gateway_id' => 'required|numeric',
        ],
        'skipNextBilling'         => [
            'subscription_id' => 'required',
        ],
        'paymentRouterView'       => [
            'payment_router_id' => 'required',
        ],
        'subscriptionOrderUpdate' => [
            'order_id'   => 'required|numeric',
            'product_id' => 'required_with:new_recurring_product_id,new_recurring_date|numeric',
        ],
        'threeDRedirect'          => [
            'order_id' => 'required|integer',
        ],
        'authorizePayment'        => [
            'billingFirstName'   => '',
            'billingLastName'    => '',
            'billingAddress1'    => 'required|max:64',
            'billingAddress2'    => '',
            'billingCity'        => 'required|max:32',
            'billingState'       => 'required|max:32',
            'billingZip'         => 'required|max:10',
            'billingCountry'     => 'required',
            'phone'              => 'required',
            'email'              => 'required|email|max:96',
            'creditCardType'     => 'required|in:amex,visa,master,discover,checking,offline,solo,maestro,switch,boleto,paypal,diners,hipercard,aura,eft_germany,giro,amazon,icepay,bitcoin_pg,eurodebit,sepa,boleto_bradesco,boleto_caixa_eco_fed,boleto_hsbc,boleto_banco_do_bras,boleto_itau,cielo_visa,cielo_mastercard,cielo_amex,cielo_diners,cielo_elo,redecard_webs_visa,redecard_webs_master',
            'creditCardNumber'   => 'required',
            'expirationDate'     => 'required',
            'CVV'                => 'numeric',
            'ipAddress'          => 'max:15|required',
            'productId'          => 'integer|required',
            'campaignId'         => 'integer|required',
            'auth_amount'        => 'numeric',
            'temp_customer_id'   => '',
            'cascade_enabled'    => 'integer',
            'save_customer'      => 'integer',
            'validate_only_flag' => 'integer',
            'void_flag'          => 'integer',
        ],
    ],
];
