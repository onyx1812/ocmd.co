<?php

namespace CodeClouds\Konnektive;

use CodeClouds\Konnektive\Config;
use \Exception;

/**
 * Konnektive API
 *
 * Serve API request
 *
 * @package Konnektive
 */
class Konnektive
{

	/**
	 * Endpoint URL of Konnektive API
	 * @link https://api.konnektive.com
	 */
	const API_END_POINT = 'https://api.konnektive.com';

	/**
	 * Store header required status
	 *
	 * @access public
	 */
	public $headerRequired = false;

	/**
	 * Store instances of particular API method's class
	 *
	 * @static a static variable
	 *
	 * @access private
	 */
	private static $_instances = [];

	/**
	 * Defining error status
	 *
	 * @access private
	 */
	private $checkError = false;

	/**
	 * Defining Konnektive API authorization userName
	 *
	 * @access private
	 */
	private $userName;

	/**
	 * Defining Konnektive API authorization password
	 *
	 * @access private
	 */
	private $password;

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
	 * Storing API response data
	 *
	 * @access private
	 */
	private $apiResponse;

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
	 *  Load API method validation
	 *
	 * @access protected
	 */
	protected $method;

	/**
	 * Store guzzle client object
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
	 * Initialize credentials for API call
	 *
	 * @param type string $userName
	 * @param type string $password
	 *
	 * @return void
	 *
	 * @access protected
	 */
	protected function __construct( $userName, $password )
	{
		$this->userName	 = $userName;
		$this->password	 = $password;
	}

	/**
	 * Get the instance
	 *
	 * @param array $apiInfo (must have username, password)
	 *
	 * @return \App\api_lib\KonnektiveApi
	 */
	public static function instance( $apiInfo )
	{
		$calledClassName = get_called_class();

		$instanceKey = md5( $calledClassName . '_' . serialize( $apiInfo ) );
		if( !array_key_exists( $instanceKey, self::$_instances ) )
		{
			self::$_instances[$instanceKey] = new $calledClassName( $apiInfo['username'], $apiInfo['password'] );
		}
		return self::$_instances[$instanceKey];
	}

	/**
	 * Used to get raw API response
	 *
	 * @return json
	 */
	public function getResponse()
	{
		return $this->apiResponse;
	}

	/**
	 * Used to get raw API response in array format
	 *
	 * @return array
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
	 */
	public function getPayloadInfo()
	{
		return $this->fields;
	}

	/**
	 * API data validation
	 *
	 * @return void
	 */
	private function fieldValidate()
	{
		$validator = Validator::make(
				$this->fields, $this->rule
		);
		if( $validator->fails() )
		{
			throw new Exception( implode( ' ', $validator->errors()->all() ) );
		}
	}

	/**
	 * Handling API request using GuzzleHttp
	 *
	 * @return void
	 */
	private function request()
	{
		try
        {
            $this->apiResponse = null;
            $ch                = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpVerb);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($ch);
            $header  = curl_getinfo($ch);
            $error   = curl_error($ch);
            curl_close($ch);
            if (!empty($error)) {
                throw new \Exception($error);
            }
            $this->apiResponse = ($this->headerRequired) ? ['content' => $content, 'header' => $header] : $content;
        } catch (\Exception $ex) {
            $this->apiResponse = $ex->getMessage();
        }
	}

	/**
	 * Preparing request data to make API request.
	 *
	 * @return void
	 */
	protected function __post()
	{
		$className		 = explode( '\\', get_called_class() );
		$this->httpVerb	 = 'POST';

		$rules		 = Config::get( end( $className ) );
		$this->rule	 = $rules['apiRules'][$this->section . ucfirst( $this->method )];
		$this->fieldValidate();

		$this->apiUrl				 = self::API_END_POINT . '/' . $this->section . '/' . $this->method . '/';
		$this->fields['loginId']	 = $this->userName;
		$this->fields['password']	 = $this->password;
		$this->guzzleData			 = [ 'query' => $this->fields ];
		$this->client				 = new \GuzzleHttp\Client();
		$this->request();
	}

	/**
	 * Method to check a string is JSON or not
	 *
	 * @param  string  $string
	 * @param  boolean $arrFlag
	 *
	 * @return mixed
	 */
	protected function isJson( $string, $arrFlag = false )
	{
		$decodedResponse = json_decode( $string, $arrFlag );
		if( json_last_error() !== JSON_ERROR_NONE )
		{
			throw new Exception( Config::get( 'Messages.jsonFormatError' ) );
		}
		return $decodedResponse;
	}

}
