<?php

namespace CodeClouds\ResponseCRM;

use CodeClouds\ResponseCRM\ResponseCRM;

/**
 * Serve the transaction API.
 * {@inheritdoc}  .In addition,
 * ResponseCRM transaction API related data operations.
 * 
 * @package ResponseCRM
 * 
 * @final
 */
final class Transaction extends ResponseCRM
{

	/**
	 * After a customer is inserted, a transaction may be run against it. 
	 * You may also run another transaction on and existing customers cardholder data.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function addSignupTransaction( $params )
	{
		$this->section	 = 'transactions';
		$this->method	 = 'addSignupTransaction';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * After a customer is inserted, a transaction may be run against it.
	 *  You may also run another transaction on and existing customers card holder data.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function addUpsellTransaction( $params )
	{
		$this->section	 = 'transactions/upsell';
		$this->method	 = 'addUpsellTransaction';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * Returns a list of transactions.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function transactionList( $params )
	{
		$this->section	 = 'transactions';
		$this->method	 = 'transactionList';
		$this->fields	 = $params;
		$this->get();
	}

	/**
	 * Refunds a transaction.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function refundTransaction( $params )
	{
		$this->section	 = 'customer/refund';
		$this->method	 = 'transactionList';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * Import singup or upsell transaction for an existing customer
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function importSignupUpsell( $params )
	{
		$this->section	 = 'import/signup-upsells';
		$this->method	 = 'importSignupUpsell';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * Import void or refund transaction for an existing customer.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function importVoidRefund( $params )
	{
		$this->section	 = 'import/void-refunds';
		$this->method	 = 'importVoidRefund';
		$this->fields	 = $params;
		$this->__post();
	}

}
