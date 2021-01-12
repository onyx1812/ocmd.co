<?php

namespace CodeClouds\Konnektive;

use \Exception;

/**
 * Make API instance.
 *
 * Handle response in particular format.
 * Validation of access username and password  which are empty or not.
 *
 * @author CodeClouds <sales@codeclouds.com>
 *
 * @package Konnektive
 */
class API
{

	/**
	 * Defining Konnektive API username
	 */
	public $userName;

	/**
	 * Defining Konnektive API password
	 */
	public $password;

	/**
	 * Defining API methods dependencies
	 *
	 * @access private
	 */
	private $dependencies = [
		'CodeClouds\\Konnektive\\Order',
		'CodeClouds\\Konnektive\\Campaign',
		'CodeClouds\\Konnektive\\Customer',
		'CodeClouds\\Konnektive\\Transaction',
	];

	/**
	 * Store parent class methods
	 *
	 * @access private
	 */
	private $parentMethods;

	/**
	 *  Store instance of class of API methods
	 *
	 * @access private
	 */
	private $desiredInstance = null;

	/**
	 * Initialize API credentials for calling API
	 *
	 * @param string $userName (API user name)
	 * @param string $password (API password)
	 *
	 * @return void
	 *
	 * @access public
	 */
	public function __construct( $userName, $password )
	{
		if( empty( $userName ) || empty( $password ) )
		{
			throw new Exception( Config::get( 'Messages.invalidApiAuth' ) );
		}
		$this->userName	 = $userName;
		$this->password	 = $password;
	}

	/**
	 * __call() method to calling expected method
	 *
	 * @param array $method (This contains method name for API call)
	 * @param array $data (Required data to call API)
	 *
	 * @return objects
	 */
	public function __call( $method, $data )
	{
		$arguments				 = reset( $data );
		$invokeInstance			 = [ $this->validateMethods( $method ), 'instance' ];
		$this->desiredInstance	 = $invokeInstance( [
			'username'	 => $this->userName,
			'password'	 => $this->password,
			] );
		$invokeMethod			 = [ $this->desiredInstance, $method ];
		$invokeMethod( $arguments );
		return $this;
	}

	/**
	 * Used to get raw API response
	 *
	 * @param  boolean $payloadFlag
	 * Show/Hide input parameters, the default value is set false
	 *
	 * @return array
	 */
	public function get( $payloadFlag = false )
	{
		if( empty( $this->desiredInstance ) )
		{
			throw new Exception( Config::get( 'Messages.apiInvokedFailure' ) );
		}

		$responseMethod	 = [ $this->desiredInstance, 'getResponse' ];
		$payloadMethod	 = [ $this->desiredInstance, 'getPayloadInfo' ];
		return !$payloadFlag ? [
			'response' => $responseMethod(),
			] : [
			'response'	 => $responseMethod(),
			'payload'	 => $payloadMethod(),
		];
	}

	/**
	 * Used to get API response in array format
	 *
	 * @param  boolean $payloadFlag
	 * Show/Hide input parameters, the default value is set false
	 *
	 * @return array
	 */
	public function getInArray( $payloadFlag = false )
	{
		if( empty( $this->desiredInstance ) )
		{
			throw new Exception( Config::get( 'Messages.apiInvokedFailure' ) );
		}

		$responseMethod	 = [ $this->desiredInstance, 'getArrayResponse' ];
		$payloadMethod	 = [ $this->desiredInstance, 'getPayloadInfo' ];
		return !$payloadFlag ? [
			'response' => $responseMethod(),
			] : [
			'response'	 => $responseMethod(),
			'payload'	 => $payloadMethod(),
		];
	}

	/**
	 * Used to get API response in object format
	 *
	 * @param  boolean $payloadFlag
	 * Show/Hide input parameters, the default value is set false
	 *
	 * @return array
	 */
	public function getInObject( $payloadFlag = false )
	{
		if( empty( $this->desiredInstance ) )
		{
			throw new Exception( Config::get( 'Messages.apiInvokedFailure' ) );
		}

		$responseMethod	 = [ $this->desiredInstance, 'getObjectResponse' ];
		$payloadMethod	 = [ $this->desiredInstance, 'getPayloadInfo' ];
		return !$payloadFlag ? [
			'response' => $responseMethod(),
			] : [
			'response'	 => $responseMethod(),
			'payload'	 => (object) $payloadMethod(),
		];
	}

	/**
	 * Find the method from expected class
	 *
	 * @param  string $method
	 * The method name for API call
	 *
	 * @return string
	 * Class name for calling method
	 *
	 * @throws Exception
	 * When method is not found
	 */
	private function validateMethods( $method )
	{
		$this->parentMethods = get_class_methods( 'CodeClouds\\Konnektive\\Konnektive' );
		$desiredDependency	 = null;
		foreach( $this->dependencies as $dependency )
		{
			$methods = get_class_methods( $dependency );
			if( in_array( $method, array_diff( $methods, $this->parentMethods ) ) )
			{
				$desiredDependency = $dependency;
				break;
			}
		}
		if( empty( $desiredDependency ) )
		{
			throw new Exception( Config::get( 'Messages.methodNotFound' ) );
		}
		return $desiredDependency;
	}

}
