<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\Limelight;

/**
 * Retrieve/Update/Create Transaction.
 *
 * Only one aspect to this class.
 * {@inheritdoc}.In addition, the Transaction class can't be inherited
 *
 * @final
 *
 * @package  Limelight
 */
final class Transaction extends Limelight
{

    /**
     * Update the subscription status of an order
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function subscriptionUpdate($params)
    {
        $this->section = 'membership';
        $this->method  = 'subscription_update';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Stop upsell product recurring
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function upsellStopRecurring($params)
    {
        $this->section = 'membership';
        $this->method  = 'upsell_stop_recurring';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Send orders to the fulfillment provider.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function repostToFulfillment($params)
    {
        $this->section = 'membership';
        $this->method  = 'repost_to_fulfillment';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Retrieve redirection information for alternative payment providers.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function getAlternativeProvider($params)
    {
        $this->section = 'membership';
        $this->method  = 'get_alternative_provider';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * View data about a shipping method in the LimeLight platform.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function shippingMethodView($params)
    {
        $this->section = 'membership';
        $this->method  = 'shipping_method_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Retrieve a list of shipping ids that match a set of criteria.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function shippingMethodFind($params)
    {
        $this->section = 'membership';
        $this->method  = 'shipping_method_find';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Check if a promo code that was entered is still valid to apply the discount.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function couponValidate($params)
    {
        $this->section = 'membership';
        $this->method  = 'coupon_validate';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * View the details of a particular
     * or list of gateways from within the CRM.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function gatewayView($params)
    {
        $this->section = 'membership';
        $this->method  = 'gateway_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Skip the next billing cycle on an existing subscription.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function skipNextBilling($params)
    {
        $this->section = 'membership';
        $this->method  = 'skip_next_billing';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Display desired payment router(s) and the related gateway details.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function paymentRouterView($params)
    {
        $this->section = 'membership';
        $this->method  = 'payment_router_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Update an existing order's next recurring details.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function subscriptionOrderUpdate($params)
    {
        $this->section = 'membership';
        $this->method  = 'subscription_order_update';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Redirects the customer to their personal bank URL for 3D Secure payments.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function threeDRedirect($params)
    {
        $this->section = 'transact';
        $this->method  = 'three_d_redirect';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Utilizes the  billing and card data fields used by NewOrder.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function authorizePayment($params)
    {
        $this->section = 'transact';
        $this->method  = 'authorize_payment';
        $this->fields  = $params;
        $this->__post();
    }

}
