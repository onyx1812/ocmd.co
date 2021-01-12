<?php

namespace CodeClouds\Konnektive;

use CodeClouds\Konnektive\Konnektive;

/**
 * Serve the customer API.
 * {@inheritdoc} .In addition,
 *  Konnektive Customer related data operation
 * 
 * @package Konnektive
 * 
 * @final
 */
final class Customer extends Konnektive
{

    /**
     * The Konnektive Query Customer API returns information about existing Customers.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function customerQuery($params)
    {
        $this->section = 'customer';
        $this->method  = 'query';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Add Customer Note API will allow you to add a note to a customer account.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function customerAddnote($params)
    {
        $this->section = 'customer';
        $this->method  = 'addnote';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Update Customer API will allow you to update customer information.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function customerUpdate($params)
    {
        $this->section = 'customer';
        $this->method  = 'update';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Query Customer History API returns a customer's about existing Customers.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function customerHistory($params)
    {
        $this->section = 'customer';
        $this->method  = 'history';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Blacklist API allows you to add a new blacklist entry.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function customerBlacklist($params)
    {
        $this->section = 'customer';
        $this->method  = 'blacklist';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * The Konnektive Query Customer Contracts API returns the base64-encoded customer contract
     * pdf file(s) based on order ID or customer ID.
	 * 
     * @param  array $params parameters consists required fields in key value pair.
	 * 
     * @return void
     */

    public function customerContracts($params)
    {
        $this->section = 'customer';
        $this->method  = 'contracts';
        $this->fields  = $params;
        $this->__post();
    }

}
