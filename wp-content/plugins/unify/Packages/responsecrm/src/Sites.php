<?php

namespace CodeClouds\ResponseCRM;

use CodeClouds\ResponseCRM\ResponseCRM;

/**
 * Serve the sites API.
 * {@inheritdoc}  .In addition,
 * ResponseCRM Sites API related data operations.
 * 
 * @package ResponseCRM
 * 
 * @final
 */
final class Sites extends ResponseCRM
{

	/**
	 * The endpoint returns information about all sites,
	 *  groups and product charges of a given client.
	 * 
	 * @param  array $params parameters consists
	 * required fields in key value pair.
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function siteList( $params )
	{
		$this->section	 = 'sites';
		$this->method	 = 'siteList';
		$this->fields	 = $params;
		$this->get();
	}

	public function testAuth( $params = [] )
	{
		$this->section	 = 'test-auth';
		$this->method	 = 'testAuth';
		$this->fields  = $params;
		$this->get();
	}

}
