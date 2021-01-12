<?php

namespace CodeClouds\Konnektive;

use CodeClouds\Konnektive\Konnektive;


/**
 * Serve the order API. 
 * {@inheritdoc} .In addition,
 *  Konnektive Order related data arrangement
 * 
 * @package Konnektive
 * 
 * @final
 */
final class Order extends Konnektive
{
   

    /**
     * The Konnektive Query Order API returns information about existing orders.
	 * 
     * @param  array $params This parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function orderQuery($params)
    {
        $this->section = 'order';
        $this->method  = 'query';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Import Lead API allows you to add new leads to the CRM
	 * 
     * @param  array $params This parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function importLeads($params)
    {
        $this->section = 'leads';
        $this->method  = 'import';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Preauth Order API allows you to preauth a credit card for 1.00 on new
     * orders before billing the final charges. This is usually called after Import Lead.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function preauth($params)
    {
        $this->section = 'order';
        $this->method  = 'preauth';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Import Order API allows you to create new orders and bill customers.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function importOrder($params)
    {
        $this->section = 'order';
        $this->method  = 'import';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Import Upsale API allows you bill and add upsales to existing orders.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function importUpsale($params)
    {
        $this->section = 'upsale';
        $this->method  = 'import';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Confirm Order API will immediately send confirmation auto responder emails
     * to the customer.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function confirm($params)
    {
        $this->section = 'order';
        $this->method  = 'confirm';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Refund Order API will allow you to issue partial and full refunds against
     * an order.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function refund($params)
    {
        $this->section = 'order';
        $this->method  = 'refund';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Cancel Order API will cancel an existing order and optionally issue a
     * refund.
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function cancel($params)
    {
        $this->section = 'order';
        $this->method  = 'cancel';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Order QA API allows you to approve or decline orders that are pending
     * Quality Assurance.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function qa($params)
    {
        $this->section = 'order';
        $this->method  = 'qa';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Update Fulfillment API will allow you to update fulfillment information.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function fulfillmentUpdate($params)
    {
        $this->section = 'fulfillment';
        $this->method  = 'update';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Rerun Declined Sale API will allow you to retry billing of a declined
     * transaction in a new sale.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function rerun($params)
    {
        $this->section = 'order';
        $this->method  = 'rerun';
        $this->fields  = $params;
        $this->__post();
    }
    /**
     * The Konnektive Order Sales Tax API allows you to compute tax for new orders with a tax service plugin through a simple http GET or POST request.
	 * 
     * @param type $params
     */
    public function salestax($params)
    {
        $this->section = 'order';
        $this->method  = 'salestax';
        $this->fields  = $params;
        $this->__post();
    }
}
