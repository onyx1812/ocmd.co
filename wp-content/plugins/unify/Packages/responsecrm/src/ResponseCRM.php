<?php

namespace CodeClouds\ResponseCRM;

use CodeClouds\ResponseCRM\Config;
use \Exception;

/**
 * ResponseCRM API.
 * 
 * Serve API request.
 * 
 * @package ResponseCRM
 * 
 */
class ResponseCRM
{

	/**
	 * Endpoint URL of ResponseCRM API
	 * @link https://openapi.responsecrm.com/api/v2/open
	 */
	const API_END_POINT = 'https://openapi.responsecrm.com/api/v2/open';

	/**
	 * Defining HTTP method 
	 * 
	 * @access private
	 */
	private $httpVerb;

	/**
	 * Defining API calling URL
	 * 
	 * @access private
	 */
	private $apiUrl;

	/**
	 * Defining ResponseCRM API authorization secret key
	 * 
	 * @access private
	 */
	private $secret;

	/**
	 * Storing API response data
	 * 
	 * @access private
	 */
	private $apiResponse;

	/**
	 * Defining error status
	 * 
	 * @access private
	 */
	private $checkError = false;

	/**
	 * Store dynamic parameters 
	 * 
	 * @access protected
	 */
	protected $fields;

	/**
	 * Assign rule according to API method
	 * 
	 * @access protected
	 */
	protected $rule;

	/**
	 * For API calling defining API method 
	 * 
	 * @access protected
	 */
	protected $section;

	/**
	 * Load API method validation
	 * 
	 * @access protected
	 */
	protected $method;

	/**
	 * Store guzzle client object 
	 * 
	 * @access protected
	 */
	protected $client;

	/**
	 * Store parameters data and header to send guzzle client
	 * 
	 * @access protected
	 */
	protected $guzzleData;

	/**
	 * Store header required status
	 * 
	 * @access public
	 */
	public $headerRequired = false;

	/**
	 * Store instances of particular API method's class 
	 * 
	 * a static variable
	 * @static
	 * 
	 * @access private
	 */
	private static $_instances = [];

	/**
	 * Initialize credentials for API call
	 * 
	 * @param type string $secret
	 * 
	 * @return void
	 * 
	 *  @access protected
	 */
	protected function __construct( $secret )
	{
		$this->secret = $secret;
	}

	/**
	 * Get the instance
	 * 
	 * @param array $apiInfo (must have secret)
	 * 
	 * @return \App\ResponseCRM
	 * 
	 * @access public
	 */
	public static function instance( $apiInfo )
	{
		$calledClassName = get_called_class();

		$instanceKey = md5( $calledClassName . '_' . serialize( $apiInfo ) );
		if( !array_key_exists( $instanceKey, self::$_instances ) )
		{
			self::$_instances[$instanceKey] = new $calledClassName( $apiInfo['secret'] );
		}
		return self::$_instances[$instanceKey];
	}

	/**
	 * validation API required field
	 * 
	 * @throws Exception
	 * 
	 * @access private
	 */
	private function fieldValidate()
	{
		$validator = Validator::make( $this->fields, $this->rule );

		if( $validator->fails() )
		{
			throw new Exception( implode( ' <br/>', $validator->errors()->all() ) );
		}
	}

	/**
	 * Preparing request data to make API request.
	 * 
	 * @return void
	 * 
	 * @access protected
	 */
	protected function __post()
	{
		$className		 = explode( '\\', get_called_class() );
		$this->httpVerb	 = 'POST';
		$rules			 = Config::get( end( $className ) );
		$this->rule		 = $rules['apiRules'][$this->method];
		$this->fieldValidate();

		$this->apiUrl		 = self::API_END_POINT . '/' . $this->section;
		$this->guzzleData	 = [
			'body' => json_encode( $this->fields )
		];
		$this->request();
	}

	/**
	 * Preparing request data to make API request.
	 * 
	 * @return void
	 * 
	 * @access protected
	 */
	protected function get()
	{
		$className		 = explode( '\\', get_called_class() );
		$this->httpVerb	 = 'GET';
		$rules			 = Config::get( end( $className ) );
		$this->rule		 = $rules['apiRules'][$this->method];
		$this->fieldValidate();

		$this->apiUrl		 = self::API_END_POINT . '/' . $this->section;
		$this->guzzleData	 = [
			'query' => $this->fields
		];
		$this->request();
	}

	/**
	 * Preparing request data to make API request.
	 * 
	 * @return void
	 * 
	 * @access protected
	 */
	protected function put()
	{
		$className		 = explode( '\\', get_called_class() );
		$this->httpVerb	 = 'PUT';
		$rules			 = Config::get( end( $className ) );
		$this->rule		 = $rules['apiRules'][$this->method];
		$this->fieldValidate();

		$this->apiUrl		 = self::API_END_POINT . '/' . $this->section;
		$this->guzzleData	 = [
			'body' => json_encode( $this->fields )
		];
		$this->request();
	}

	/**
	 * Handling API request using GuzzleHttp
	 * 
	 * @return void
	 * 
	 * @access private
	 */
	private function request()
	{
		try
		{

			$this->guzzleData['headers']['content-type']	 = 'application/json';
			$this->guzzleData['headers']['Authorization']	 = $this->secret;
			/**
			 * Added Idempotency-Key into the header
			 */
			$this->guzzleData['headers']['Idempotency-Key']	 = $this->genUuid();

			$this->apiResponse	 = null;
			$this->client		 = new \GuzzleHttp\Client();
			$response			 = $this->client->request( $this->httpVerb, $this->apiUrl, $this->guzzleData );
			$content			 = $response->getBody()->getContents();
			if( $response->getStatusCode() != 200 )
			{
				throw new Exception( $ex->getMessage() );
			}
			$this->apiResponse = ($this->headerRequired) ? [ 'content' => $content, 'header' => $header ] : $content;
		}
		catch( \Guzzle\HttpException\BadResponseException $ex )
		{
			$this->apiResponse = $ex->getMessage();
		}
	}

	/**
	 * Used to get raw API response
	 * 
	 * @return json
	 * 
	 * @access public
	 */
	public function getResponse()
	{
		return $this->apiResponse;
	}

	/**
	 * Used to get raw API response in array format
	 * 
	 * @return array
	 * 
	 * @access public
	 */
	public function getArrayResponse()
	{
		if( $this->checkError )
		{
			return [ 'curlError' => $this->apiResponse ];
		}
		$formattedResponse = $this->isJson( $this->apiResponse, true );
		return $formattedResponse;
	}

	/**
	 * Used to get API response in object format
	 * 
	 * @return object
	 * 
	 * @access public
	 */
	public function getObjectResponse()
	{
		if( $this->checkError )
		{
			return json_decode( json_encode( [ 'curlError' => $this->apiResponse ] ) );
		}
		$formattedResponse = $this->isJson( $this->apiResponse );
		return $formattedResponse;
	}

	/**
	 * This method is used to get the last payload
	 * 
	 * @return array
	 * 
	 * @access public
	 */
	public function getPayloadInfo()
	{
		return $this->fields;
	}

	/**
	 * Method to check a string is JSON or not
	 * 
	 * @param  string  $string
	 * @param  boolean $arrFlag
	 * 
	 * @return mixed
	 * 
	 * @access protected
	 */
	protected function isJson( $string, $arrFlag = false )
	{
		$decodedResponse = json_decode( $string, $arrFlag );
		if( json_last_error() !== JSON_ERROR_NONE )
		{
			throw new Exception( Config::get( 'Messages.invalidJson' ) );
		}
		return $decodedResponse;
	}

	/**
	 * Generate UUID
	 * @return String
	 */
	private function genUuid()
	{
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}

}
