<?php

namespace CodeClouds\Unify\Actions;

/**
 * Gateway actions.
 * @package CodeClouds\Unify
 */
class Gateway
{
    /**
     * Initialize payment gateway.
     */
    public static function init()
    {
        return new \CodeClouds\Unify\Models\Unify_Payment();
        return new \CodeClouds\Unify\Models\Unify_Paypal_Payment();
    }

    /**
     * Add payment gateway.
     * @param array $methods
     */
    public static function add_unify_gateway_class($methods)
    {
        $methods[] = 'CodeClouds\Unify\Models\Unify_Payment';
        $methods[] = 'CodeClouds\Unify\Models\Unify_Paypal_Payment';
        return $methods;
    }
}
