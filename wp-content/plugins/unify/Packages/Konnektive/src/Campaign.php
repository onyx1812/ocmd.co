<?php

namespace CodeClouds\Konnektive;

use CodeClouds\Konnektive\Konnektive;

/**
 * Serve the campaign API.
 *  {@inheritdoc} .In addition,
 * Konnektive Campaign related data operation
 * 
 * @package Konnektive
 * 
 * @final
 */
final class Campaign extends Konnektive
{

	/**
	 * The Konnektive Query Campaigns API returns information about Campaigns.
	 * 
	 * @param  array $params parameters consists required fields in key value pair.
	 * @return void
	 */
	public function campaignQuery( $params )
	{
		$this->section	 = 'campaign';
		$this->method	 = 'query';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Query Mid Summary Report API returns summary report of mid information.
	 * 
	 * @param type $param
	 * 
	 * @return void
	 */
	public function midSummary( $params )
	{
		$this->section	 = 'reports';
		$this->method	 = 'mid-summary';
		$this->fields	 = $params;
		$this->__post();
	}

	/**
	 * The Konnektive Query Retention Report API returns information about the retention report.
	 * 
	 * @param type $param
	 * 
	 * @return void
	 */
	public function retention( $params )
	{
		$this->section	 = 'reports';
		$this->method	 = 'retention';
		$this->fields	 = $params;
		$this->__post();
	}

}
