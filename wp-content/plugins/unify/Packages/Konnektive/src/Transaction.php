<?php

namespace CodeClouds\Konnektive;

use CodeClouds\Konnektive\Konnektive;

/**
 * Serve the transaction API.
 * {@inheritdoc} .In addition,
 *  Konnektive Transaction related data operation
 * 
 * @package Konnektive
 * 
 * @final
 */
final class Transaction extends Konnektive
{

	/**
	 * The Konnektive Query Transaction API returns information about all transactions recorded
	 * in the CRM.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function transactionsQuery( $params )
	{
		$this->section	 = 'transactions';
		$this->method	 = 'query';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Transaction Range Select API returns the IDs of successful transactions
	 * within a specific date range.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function cbdataList( $params )
	{
		$this->section	 = 'cbdata';
		$this->method	 = 'list';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Composite Data Query API returns a data set combining information from
	 * the Transaction, Order, and Customer APIs.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function cbdataQuery( $params )
	{
		$this->section	 = 'cbdata';
		$this->method	 = 'query';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Update Purchase API will allow you to update transaction information.
	 * This is used primarily for marking chargebacks.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function updateTransaction( $params )
	{
		$this->section	 = 'transactions';
		$this->method	 = 'update';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Transaction Refund API will allow you to issue partial and full refunds
	 * against a specific transaction.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function refundTransaction( $params )
	{
		$this->section	 = 'transactions';
		$this->method	 = 'refund';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Query Order API returns information about existing orders.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function purchaseQuery( $params )
	{
		$this->section	 = 'purchase';
		$this->method	 = 'query';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Update Purchase API will allow you to update purchase information.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function updatePurchase( $params )
	{
		$this->section	 = 'purchase';
		$this->method	 = 'update';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Cancel Purchase API will cancel an existing recurring purchase and
	 * optionally issue a refund.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function cancelPurchase( $params )
	{
		$this->section	 = 'purchase';
		$this->method	 = 'cancel';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Purchase Refund API will allow you to issue partial and full refunds
	 * against a continuity purchase's most recent billing.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function refundPurchase( $params )
	{
		$this->section	 = 'purchase';
		$this->method	 = 'refund';
		$this->fields	 = $params;
		$this->__post();
	}

}
