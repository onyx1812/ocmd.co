<?php

return [
    'apiRules' => [
        'campaignView'          => [
            'campaign_id' => 'required|numeric',
        ],
        'campaignFindActive'    => [
        ],
        'productIndex'          => [
            'product_id' => 'required_without:criteria',
            'criteria'   => 'required_without:product_id',
        ],
        'productAttributeIndex' => [
            'product_id' => 'required',
        ],
        'productBundleIndex'    => [
        ],
        'productBundleView'     => [
            'product_id' => 'required',
        ],
        'productCopy'           => [
            'product_id' => 'required|numeric',
        ],
        'productCreate'         => [
            'product_name'            => 'required',
            'category_id'             => 'required|numeric',
            'product_max_quantity'    => 'required|numeric',
            'product_sku'             => 'required',
            'product_price'           => 'required|numeric',
            'product_description'     => 'required',
            'customer_purchase_limit' => 'boolean',
            'digitally_delivered'     => 'boolean',
            'taxable'                 => 'boolean',
            'shippable'               => 'boolean',
            'signature_confirmation'  => 'boolean',
            'delivery_confirmation'   => 'boolean',
            'preserve_quantity'       => 'boolean',
            'recurring'               => 'boolean',
            'recurring_itself'        => 'boolean|required_if:recurring,1',
            'collections'             => 'boolean',
            'ccbill_subscription_id'  => 'Boolean',
            'shipping_declared_value' => 'numeric',
            'product_restocking_fee'  => 'numeric',
            'shipping_weight'         => 'numeric',
            'subscription_type'       => 'numeric|required_if:recurring,1',
            'recurring_days'          => 'numeric|required_if:subscription_type,1|required_if:subscription_type,2',
            'subscription_week'       => 'numeric|required_if:subscription_type,3',
            'shipping_declared_value' => 'numeric',
            'subscription_day'        => 'numeric|required_if:subscription_type,3',
            'shipping_digital_url'    => 'required_if:digitally_delivered,1',
            'recurring_discount_max'  => 'numeric',
        ],
        'productDelete'         => [
            'product_id' => 'required|numeric',
        ],
        'productUpdate'         => [
            'product_ids' => 'required|numeric',
            'actions'     => 'required|in:product_name,product_price,product_description,product_sku,product_weight,is_shippable,rebill_days,rebill_product,is_free_trial,signature_confirmation,delivery_confirmation,digital_delivery_url,digital_delivery,declared_value,category',
        ],
    ],
];
