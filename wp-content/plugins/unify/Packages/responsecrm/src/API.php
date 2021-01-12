<?php

namespace CodeClouds\ResponseCRM;

use \Exception;

/**
 * Make API instance
 * 
 * Handle response in particular format.
 * Validation of secret which is empty or not
 * 
 * @author CodeClouds <sales@codeclouds.com>
 * 
 * @package ResponseCRM
 */
class API
{

	/**
	 * Defining WorldPay API authorization secret key
	 * 
	 * @access public
	 */
	public $secret;

	/**
	 * Defining API methods dependencies
	 * 
	 * @access private
	 */
	private $dependencies = [
		'CodeClouds\\ResponseCRM\\Order',
		'CodeClouds\\ResponseCRM\\Customer',
		'CodeClouds\\ResponseCRM\\Transaction',
		'CodeClouds\\ResponseCRM\\Leads',
		'CodeClouds\\ResponseCRM\\Sites'
	];

	/**
	 * Store parent class methods
	 * 
	 * @access private
	 */
	private $parentMethods;

	/**
	 * Store instance of class of API methods 
	 * 
	 * @access private
	 */
	private $desiredInstance = null;

	/**
	 * Initialize API credentials for calling API
	 * 
	 * @param string $secret (API secret)
	 * 
	 * @return void
	 * 
	 * @access public
	 */
	public function __construct( $secret )
	{
		if( empty( $secret ) )
		{
			throw new Exception( Config::get( 'Messages.invalidApiAuth' ) );
		}

		$this->secret = $secret;
	}

	/**
	 * __call() method to calling expected method
	 * 
	 * @param array $method (This contains method name for API call)
	 * @param array $data (Required data to call API)
	 * 
	 * @return object
	 * 
	 * @access public
	 */
	public function __call( $method, $data )
	{
		//$arguments = reset($data);
		$invokeInstance			 = [ $this->validateMethods( $method ), 'instance' ];
		$this->desiredInstance	 = $invokeInstance( [
			'secret' => $this->secret
			] );
		$invokeMethod			 = [ $this->desiredInstance, $method ];
		//$invokeMethod($arguments);
		call_user_func_array( $invokeMethod, $data );
		return $this;
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
		$this->parentMethods = get_class_methods( 'CodeClouds\\ResponseCRM\\ResponseCRM' );
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

	/**
	 * Used to get raw API response
	 * 
	 * @param   boolean 
	 * Show/Hide input parameters, the default value is set false
	 * 
	 * @return array
	 * 
	 *  @access public
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
	 * 
	 * @access public
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
	 * @param  boolean Show/Hide input parameters, the default value is set false
	 * 
	 * @return array
	 * 
	 * @access public
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

}
