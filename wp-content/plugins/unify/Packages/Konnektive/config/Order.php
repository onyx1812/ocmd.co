<?php

return [
    'apiRules' => [
        'leadsImport'       => [
            'orderId'              => '',
            'firstName'            => 'required|max:30',
            'lastName'             => 'required|max:30',
            'companyName'          => '',
            'address1'             => 'required|max:30',
            'address2'             => '',
            'postalCode'           => 'required',
            'city'                 => 'required|max:30',
            'state'                => 'required',
            'country'              => 'required',
            'emailAddress'         => 'required|email',
            'phoneNumber'          => 'required',
            'ipAddress'            => 'max:46|required',
            'billShipSame'         => 'boolean',
            'shipFirstName'        => 'required_if:billShipSame,false|max:30',
            'shipLastName'         => 'required_if:billShipSame,false|max:30',
            'shipCompanyName'      => '',
            'shipAddress1'         => 'required_if:billShipSame,false|max:30',
            'shipAddress2'         => '',
            'shipCity'             => 'required_if:billShipSame,false|max:20',
            'shipState'            => 'required_if:billShipSame,false',
            'shipCountry'          => 'required_if:billShipSame,false',
            'campaignId'           => 'integer|required',
            'affId'                => 'max:10',
            'sourceValue1'         => 'max:30',
            'sourceValue2'         => 'max:30',
            'sourceValue3'         => 'max:30',
            'disableCustomerDedup' => 'boolean',
        ],
        'orderPreauth'      => [
            'customerId'       => 'required',
            'orderId'          => 'required',
            'paySource'        => 'required|in:CREDITCARD',
            'cardNumber'       => 'required',
            'cardExpiryDate'   => 'date|date_format:m/d/Y',
            'cardSecurityCode' => 'numeric|required',
            'firstName'        => 'max:30',
            'lastName'         => 'max:30',
            'companyName'      => 'max:30',
            'address1'         => 'max:30',
            'address2'         => 'max:30',
            'postalCode'       => '',
            'city'             => 'max:30',
            'state'            => '',
            'country'          => '',
            'emailAddress'     => 'email',
            'phoneNumber'      => '',
            'ipAddress'        => 'max:46',
        ],
        'orderImport'       => [
            'orderId'              => 'max:30',
            'sessionId'            => 'max:40',
            'customerId'           => '',
            'firstName'            => 'required|max:30',
            'lastName'             => 'required|max:30',
            'companyName'          => 'max:30',
            'address1'             => 'required|max:30',
            'address2'             => 'max:30',
            'postalCode'           => 'required',
            'city'                 => 'required|max:30',
            'state'                => 'required',
            'country'              => 'required',
            'emailAddress'         => 'required|email',
            'phoneNumber'          => 'required',
            'ipAddress'            => 'max:46|required',
            'billShipSame'         => 'boolean',
            'shipFirstName'        => 'required_if:billShipSame,false|max:30',
            'shipLastName'         => 'required_if:billShipSame,false|max:30',
            'shipCompanyName'      => '',
            'shipAddress1'         => 'required_if:billShipSame,false|max:30',
            'shipAddress2'         => '',
            'shipCity'             => 'required_if:billShipSame,false|max:30',
            'shipState'            => 'required_if:billShipSame,false',
            'shipCountry'          => 'required_if:billShipSame,false',
            'shipPostalCode'       => 'required_if:billShipSame,false',
            'paySource'            => 'required|in:CREDITCARD,CHECK,ACCTONFILE,PREPAID',
            'cardNumber'           => 'required',
            'cardMonth'            => 'required_if:paySource,CREDITCARD',
            'cardYear'             => 'required_if:paySource,CREDITCARD',
            'cardSecurityCode'     => 'required_if:paySource,CREDITCARD|numeric',
            'forceMerchantId'      => 'integer',
            'preAuthBillerId'      => '',
            'preAuthMerchantTxnId' => 'max:30',
            'salesTax'             => 'numeric',
            'achAccountType'       => 'required_if:paySource,CHECK|in:CHECKING,SAVINGS',
            'achRoutingNumber'     => 'required_if:paySource,CHECK',
            'achAccountNumber'     => 'required_if:paySource,CHECK',
            'campaignId'           => 'required|integer',
            'forceQA'              => 'boolean',
            'skipQA'               => 'boolean',
            'insureShipment'       => 'boolean',
            'product1_id'          => 'required',
            'product1_qty'         => 'integer',
            'product1_price'       => 'numeric',
            'product1_shipPrice'   => 'numeric',
            'couponCode'           => '',
            'shipProfileId'        => 'integer',
            'salesUrl'             => 'max:200',
            'affId'                => 'max:10',
            'sourceValue1'         => 'max:30',
            'sourceValue2'         => 'max:30',
            'sourceValue3'         => 'max:30',
            'sourceValue4'         => 'max:30',
            'sourceValue5'         => 'max:30',
            'redirectsTo'          => 'max:300',
            'errorRedirectsTo'     => 'max:300',
            'eci'                  => 'max:5',
            'xid'                  => 'max:50',
            'cavv'                 => 'max:50',
            'rebill_eci'           => 'max:5',
            'rebill_xid'           => 'max:50',
            'rebill_cavv'          => 'max:50',
            'disableCustomerDedup' => 'boolean',
        ],
        'upsaleImport'      => [
            'orderId'          => 'required|max:30',
            'productId'        => 'required|integer',
            'productQty'       => 'integer',
            'productPrice'     => 'numeric',
            'productShipPrice' => 'numeric',
            'productSalesTax'  => 'numeric',
            'replaceProductId' => 'integer',
            'forceMerchantId'  => 'integer',
            'eci'              => 'max:5',
            'xid'              => 'max:50',
            'cavv'             => 'max:50',
            'rebill_eci'       => 'max:5',
            'rebill_xid'       => 'max:50',
            'rebill_cavv'      => 'max:50',
        ],
        'orderConfirm'      => [
            'orderId' => 'required|max:30',
        ],
        'orderQuery'        => [
            'orderId'        => 'max:30',
            'orderStatus'    => 'in:COMPLETE,PARTIAL,DECLINED,REFUNDED,CANCELLED',
            'campaignId'     => 'integer',
            'isDeclineSave'  => 'boolean',
            'firstName'      => 'max:30',
            'lastName'       => 'max:30',
            'companyName'    => 'max:30',
            'address1'       => 'max:30',
            'address2'       => 'max:30',
            'postalCode'     => '',
            'city'           => 'max:30',
            'state'          => '',
            'country'        => '',
            'emailAddress'   => 'email',
            'phoneNumber'    => '',
            'ipAddress'      => '',
            'showExternal'   => 'boolean',
            'dateRangeType'  => 'in:dateCreated,dateUpdated',
            'startDate'      => 'required_without_all:customerId,orderId|date|date_format:m/d/Y',
            'endDate'        => 'required_without_all:customerId,orderId|date|date_format:m/d/Y',
            'startTime'      => '',
            'endTime'        => '',
            'resultsPerPage' => 'integer',
            'page'           => 'integer',
        ],
        'orderRefund'       => [
            'orderId'      => 'required|max:30',
            'refundAmount' => 'numeric|required_if:fullRefund,true',
            'fullRefund'   => 'boolean',
        ],
        'orderCancel'       => [
            'orderId'       => 'required',
            'cancelReason'  => 'required',
            'fullRefund'    => 'boolean',
            'afterNextBill' => 'boolean',
        ],
        'orderQa'           => [
            'orderId' => 'required',
            'action'  => 'required|in:APPROVE,DECLINE',
        ],
        'fulfillmentUpdate' => [
            'orderId'           => 'required_without:fulfillmentId',
            'fulfillmentId'     => 'required_without:orderId',
            'fulfillmentStatus' => 'in:SHIPPED,RMA_PENDING,RETURNED,CANCELLED',
            'trackingNumber'    => 'required_if:fulfillmentStatus,SHIPPED',
            'dateShipped'       => 'date|date_format:m/d/Y|required_if:fulfillmentStatus,SHIPPED',
            'refundAmount'      => 'numeric',
            'rmaNumber'         => 'max:32|required_if:fulfillmentStatus,RMA_PENDING',
            'dateReturned'      => 'date|date_format:m/d/Y|required_if:fulfillmentStatus,RETURNED',
            'shipCarrier'       => 'max:32',
            'shipMethod'        => 'max:32',
        ],
        'orderRerun'        => [
            'orderId'             => 'required',
            'forceBillerId'       => '',
            'forceLoadBalancerId' => '',
        ],
        'orderSalestax'     => [
            'campaignId'     => 'required|integer',
            'firstName'      => 'required|max:30',
            'lastName'       => 'required|max:30',
            'shipAddress1'   => 'required|max:30',
            'shipAddress2'   => 'max:30',
            'shipPostalCode' => 'required',
            'shipCity'       => 'required|max:30',
            'shipState'      => '',
            'shipCountry'    => '',
            'couponCode'     => 'max:30',
            'product.*._id'  => 'required',
        ],
    ],
];
