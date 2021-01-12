<?php

return [
    'apiRules' => [
        'campaignQuery'      => [
            'campaignId'       => 'integer',
            'campaignName'     => 'max:30',
            'campaignType'     => 'in:PHONE,ECOMMERCE,LANDER',
            'visible'          => 'boolean',
            'showAllCampaigns' => 'boolean',
            'showAllProducts'  => 'boolean',
            'resultsPerPage'   => 'integer',
            'page'             => 'integer',
        ],
        'reportsMid-summary' => [
            'midId'          => 'integer',
            'startDate'      => 'date|date_format:m/d/Y',
            'endDate'        => 'date|date_format:m/d/Y',
            'resultsPerPage' => 'integer',
            'page'           => 'integer',
        ],
        'reportsRetention'   => [
            'reportType'     => 'required|in:campaign,affiliate,callcenter,mid',
            'campaignId'     => 'integer',
            'productId'      => 'integer',
            'affiliateId'    => '',
            'callCenterId'   => '',
            'maxCycles'      => 'integer',
            'include'        => 'in:ByProduct,ByPublisher,BySubAff,ByAgent',
            'startDate'      => 'date|date_format:m/d/Y',
            'endDate'        => 'date|date_format:m/d/Y',
            'resultsPerPage' => 'integer',
            'page'           => 'integer',
        ],
    ],
];
