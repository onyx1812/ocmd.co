<?php

namespace CodeClouds\ResponseCRM;

use CodeClouds\ResponseCRM\ResponseCRM;

/**
 * Serve the order API.
 * {@inheritdoc }  In addition, 
 * ResponseCRM order API related data operations.
 * 
 * @package ResponseCRM
 * 
 * @final
 * 
 * 
 */
final class Order extends ResponseCRM
{

	/**
	 * The endpoint returns information about all
	 *  transactions recorded in the CRM waiting for fulfillment
	 * 
	 * @param  array $params This parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function fulfillmentListOrder( $params )
	{
		$this->method	 = 'fulfillmentListOrder';
		$this->section	 = 'fulfillment-orders';
		$this->fields	 = $params;
		$this->get();
	}

	/**
	 * Update tracking
	 * @param  array $params This parameters 
	 * consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function updateTracking( $params )
	{
		$this->method	 = 'updateTracking';
		$this->section	 = 'fulfillment-orders';
		$this->fields	 = $params;
		$this->put();
	}

}
