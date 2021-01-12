<?php

namespace CodeClouds\ResponseCRM;

use CodeClouds\ResponseCRM\ResponseCRM;

/**
 * Serve the leads API.
 * {@inheritdoc }  In addition, 
 * ResponseCRM leads API related data operations.
 * 
 * @package ResponseCRM
 * 
 * @final
 */
final class Leads extends ResponseCRM
{

	/**
	 * To get list of leads
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function leadList( $params )
	{
		$this->section	 = 'leads';
		$this->method	 = 'leadList';
		$this->fields	 = $params;
		$this->get();
	}

	/**
	 * To add leads
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * 
	 * @return void
	 */
	public function addLead( $params )
	{
		$this->section	 = 'leads';
		$this->method	 = 'addLead';
		$this->fields	 = $params;
		$this->__post();
	}

}

?>
