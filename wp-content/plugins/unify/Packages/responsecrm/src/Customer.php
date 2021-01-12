<?php

namespace CodeClouds\ResponseCRM;

use CodeClouds\ResponseCRM\ResponseCRM;

/**
 * Serve the customer API.
 * {@inheritdoc }  In addition, 
 * ResponseCRM customer API related data operations.
 * 
 * @package ResponseCRM
 * 
 * @final
 */
final class Customer extends ResponseCRM
{

	/**
	 * Before you run a transaction, a customer record must first be created in the CRM.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function addCustomer( $params )
	{		
		$this->section	 = 'customers';
		$this->method	 = 'addCustomer';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * To edit customer
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function editCustomer( $params, $customerID )
	{
		$this->section	 = 'customers/' . $customerID;
		$this->method	 = 'editCustomer';
		$this->fields	 = $params;
		$this->put();
	}

	/**
	 * List customer�s recurrings
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function recurringList( $params, $customerID )
	{
		$this->section	 = 'customers/recurrings/' . $customerID;
		$this->method	 = 'recuuringList';
		$this->fields	 = $params;
		$this->get();
	}

	/**
	 * Edit amount, next charge due date or active status for one or many 
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function editRecurrings( $params )
	{
		$this->section	 = 'customers/recurrings/';
		$this->method	 = 'editRecurrings';
		$this->fields	 = $params;
		$this->put();
	}

	/**
	 * List customer�s notes
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function listNotes( $params, $customerID )
	{
		$this->section	 = 'customers/notes/' . $customerID;
		$this->method	 = 'listNotes';
		$this->fields	 = $params;
		$this->get();
	}

	/**
	 * Add customer note
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function addNotes( $params, $customerID )
	{
		$this->section	 = 'customers/notes/' . $customerID;
		$this->method	 = 'addNotes';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * Mark customer as chargeback
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function addCustomerChargeback( $params, $customerID )
	{
		$this->section	 = 'customers/mark-chargeback/' . $customerID;
		$this->method	 = 'addCustomerChargeback';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * Cancel customer as chargeback
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function cancelCustomerCahrgebck( $params )
	{
		$this->section	 = 'customers/mark-cancelled';
		$this->method	 = 'cancelCustomerCahrgebck';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * Import customer
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function importCustomer( $params )
	{
		$this->section	 = 'import/customers';
		$this->method	 = 'importCustomer';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * Import recurring transaction for an existing customer and recurring charge
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function importRecurrings( $params )
	{
		$this->section	 = 'import/recurrings';
		$this->method	 = 'importRecurrings';
		$this->fields	 = $params;
		$this->__post();
	}

}

?>
