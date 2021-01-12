<?php

return [
    'apiRules' => [
        'transactionsQuery'  => [
            'orderId'          => 'max:30',
            'purchaseId'       => 'max:30',
            'customerId'       => 'integer',
            'txnType'          => 'in:SALE,AUTHORIZE,CAPTURE,VOID,REFUND',
            'paySource'        => 'in:CREDITCARD,CHECK,PREPAID',
            'responseType'     => 'in:SUCCESS,HARD_DECLINE,SOFT_DECLINE',
            'merchantTxnId'    => 'max:32',
            'clientTxnId'      => 'max:32',
            'merchantId'       => 'integer',
            'cardLast4'        => '',
            'cardBin'          => '',
            'achAccountNumber' => '',
            'achRoutingNumber' => '',
            'isChargedback'    => 'boolean',
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
            'affId'            => 'max:20',
            'showExternal'     => 'boolean',
            'dateRangeType'    => 'in:dateCreated,dateUpdated',
            'startDate'        => 'required_without_all:customerId,orderId,transactionId|date|date_format:m/d/Y',
            'endDate'          => 'required_without_all:customerId,orderId,transactionId|date|date_format:m/d/Y',
            'startTime'        => '',
            'endTime'          => '',
            'sortDir'          => 'integer|in:0,1',
            'resultsPerPage'   => 'integer',
            'page'             => 'integer',
        ],
        'cbdataList'         => [
            'dateRangeType'  => 'in:dateCreated,dateUpdated',
            'startDate'      => 'required|date|date_format:m/d/Y',
            'endDate'        => 'required|date|date_format:m/d/Y',
            'startTime'      => '',
            'endTime'        => '',
            'resultsPerPage' => 'integer',
            'page'           => 'integer',
        ],
        'cbdataQuery'        => [
            'transactionId'  => 'integer|required_without_all:startDate,endDate',
            'dateRangeType'  => '',
            'startDate'      => 'date|date_format:m/d/Y|required_without:transactionId',
            'endDate'        => 'date|date_format:m/d/Y|required_without:transactionId',
            'startTime'      => '',
            'endTime'        => '',
            'resultsPerPage' => 'integer',
            'page'           => 'integer',

        ],
        'transactionsUpdate' => [
            'transactionId'        => 'required',
            'markChargeback'       => 'required_if:revertChargeback,false|boolean',
            'revertChargeback'     => 'required_if:markChargeback,false|boolean',
            'chargebackAmount'     => 'required_if:markChargeback,true|boolean',
            'chargebackDate'       => 'required_if:markChargeback,true|date',
            'chargebackReasonCode' => 'required_if:markChargeback,true|string',
            'chargebackNote'       => '',
        ],
        'transactionsRefund' => [
            'transactionId'       => 'required|integer',
            'refundAmount'        => 'required_if:fullRefund,false|numeric',
            'fullRefund'          => 'boolean',
            'cancelPurchase'      => 'boolean',
            'externalRefund'      => 'boolean',
            'refundMerchantTxnId' => 'max:50',
        ],
        'purchaseQuery'      => [
            'orderId'        => 'max:30',
            'purchaseId'     => 'max:30',
            'customerId'     => 'integer',
            'firstName'      => 'max:30',
            'lastName'       => 'max:30',
            'emailAddress'   => 'email',
            'phoneNumber'    => '',
            'affId'          => 'max:20',
            'showExternal'   => 'boolean',
            'dateRangeType'  => 'in:dateCreated,dateUpdated',
            'startDate'      => 'required_without_all:customerId,orderId,purchaseId|date|date_format:m/d/Y',
            'endDate'        => 'required_without_all:customerId,orderId,purchaseId|date|date_format:m/d/Y',
            'startTime'      => '',
            'endTime'        => '',
            'sortDir'        => 'integer|in:0,1',
            'resultsPerPage' => 'integer',
            'page'           => 'integer',
        ],
        'purchaseUpdate'     => [
            'purchaseId'          => 'required',
            'reactivate'          => 'boolean',
            'status'              => 'in:RECYCLE_BILLING,RECYCLE_FAILED',
            'billNow'             => 'boolean',
            'newMerchantId'       => 'integer',
            'price'               => 'numeric',
            'shippingPrice'       => 'numeric',
            'nextBillDate'        => 'date|date_format:m/d/Y',
            'billingIntervalDays' => 'integer',
            'finalBillingCycle'   => 'integer',
        ],
        'purchaseCancel'     => [
            'purchaseId'    => 'required|max:200',
            'cancelReason'  => '',
            'fullRefund'    => 'boolean',
            'afterNextBill' => 'boolean',
        ],
        'purchaseRefund'     => [
            'purchaseId'   => 'required|max:30',
            'refundAmount' => 'required_if:fullRefund,true',
            'fullRefund'   => 'boolean',
        ],
    ],
];
