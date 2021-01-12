<?php

return [
    'apiRules' => [
        'validateCredentials'  => [
        ],
        'memberCreate'         => [
            'email'       => 'required|email',
            'customer_id' => 'required',
        ],
        'memberUpdate'         => [
            'email'                   => 'required|email',
            'current_member_password' => 'required',
            'new_member_password'     => 'required',
        ],
        'memberDelete'         => [
            'email' => 'required|email',
        ],
        'memberView'           => [
            'email' => 'required|email',
        ],
        'memberLogin'          => [
            'email'           => 'required|email',
            'member_password' => 'required',
        ],
        'memberLogout'         => [
            'token' => 'required',
        ],
        'memberCheckSession'   => [
            'token' => 'required',
        ],
        'memberForgotPassword' => [
            'email'    => 'required|email',
            'event_id' => 'required',
        ],
        'memberResetPassword'  => [
            'email'                => 'required|email',
            'member_temp_password' => 'required',
            'member_new_password'  => 'required',
        ],
        'memberEventIndex'     => [

        ],

    ],
];
