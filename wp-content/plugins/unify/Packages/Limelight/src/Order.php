<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\Limelight;

/**
 * Retrieve/Update/Create Order.
 *
 * Only one aspect to this class.
 * {@inheritdoc}.In addition, the Order class can't be inherited
 *
 * @final
 *
 * @package Limelight
 */
final class Order extends Limelight
{

    /**
     * Figure out thepro-rate amount for recurring orders to be refunded.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderCalculateRefund($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_calculate_refund';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Display orders that have been declined. The days field for the request section,
     * is the number of days in the history of orders to search within.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderFindOverdue($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_find_overdue';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Refund on the given order.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderRefund($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_refund';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Void on the given order.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderVoid($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_void';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Force bill on the given order.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderForceBill($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_force_bill';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Update the recurring status of an order.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderUpdateRecurring($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_update_recurring';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Find orders and get a result of order Ids.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderFind($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_find';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Orders that have been modified since creation.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderFindUpdated($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_find_updated';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Update a range of values on an order.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderUpdate($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_update';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Display critical information regarding the order and its associated data.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderView($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Reprocess on the given order.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function orderReprocess($params)
    {
        $this->section = 'membership';
        $this->method  = 'order_reprocess';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Creates New Order with all available input fields.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function newOrder($params)
    {
        $this->section = 'transact';
        $this->method  = 'NewOrder';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Supports all the existing parameters of the NewOrder method.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function newOrderCardOnFile($params)
    {
        $this->section = 'transact';
        $this->method  = 'NewOrderCardOnFile';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * NewOrderWithProspect must be used in conjunction with the NewProspect call.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function newOrderWithProspect($params)
    {
        $this->section = 'transact';
        $this->method  = 'NewOrderWithProspect';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Display offer details.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function offerView($params)
    {
        $this->section = 'membership';
        $this->method  = 'offer_view';
        $this->fields  = $params;
        $this->__post();
    }

}
