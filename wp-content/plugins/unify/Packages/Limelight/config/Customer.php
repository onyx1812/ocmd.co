<?php

return [
    'apiRules' => [
        'customerFindActiveProduct' => [
            'customer_id' => 'required|numeric',
            'campaign_id' => 'numeric',
        ],
        'customerView'              => [
            'customer_id' => 'required|numeric',
        ],
        'customerFind'              => [
            'campaign_id' => 'required',
            'start_date'  => 'required|date|date_format:m/d/Y',
            'end_date'    => 'required|date|date_format:m/d/Y',
            'start_time'  => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]|60)$/'),
            'end_time'    => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]|60)$/'),
            'search_type' => 'in:any,all',
            'return_type' => '',
            'criteria'    => 'string',
        ],
        'prospectView'              => [
            'prospect_id' => 'required|numeric',
        ],
        'prospectUpdate'            => [
            'prospect_ids' => 'required|numeric',
            'actions'      => 'required|in:first_name,last_name,address,city,state,zip,country,email,phone,notes',
            'values'       => 'required|string',
        ],
        'prospectFind'              => [
            'campaign_id' => 'required',
            'start_date'  => 'required|date|date_format:m/d/Y',
            'end_date'    => 'required|date|date_format:m/d/Y',
            'start_time'  => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]|60)$/'),
            'end_time'    => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]|60)$/'),
            'search_type' => 'in:any,all',
            'return_type' => '',
            'criteria'    => 'string',
        ],
        'newProspect'               => [
            'firstName'  => 'max:64',
            'lastName'   => 'max:64',
            'address1'   => 'max:64',
            'address2'   => 'max:64',
            'city'       => 'max:32',
            'state'      => 'max:32',
            'zip'        => '',
            'email'      => 'required|email|max:96',
            'ipAddress'  => 'max:15|required',
            'AFID'       => 'max:255',
            'SID'        => 'max:255',
            'AFFID'      => 'max:255',
            'C1'         => 'max:255',
            'C2'         => 'max:255',
            'C3'         => 'max:255',
            'AID'        => 'max:255',
            'OPT'        => 'max:255',
            'click_id'   => 'max:255',
            'campaignId' => 'required|integer',
            'notes'      => 'max:255',
        ],
    ],
];
