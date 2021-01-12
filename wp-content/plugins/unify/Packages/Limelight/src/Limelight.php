<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\Config;
use \Exception;

/**
 * To Serve Limelight API.
 *
 * Set API credentials for handling API request and retrieve Limelight API response.
 *
 * @package Limelight
 */
class Limelight
{
    /**
     * Store header required info
     *
     * @var boolean
     */
    public $headerRequired = false;

    /**
     * Instances of particular API method's class
     *
     * @access private
     */
    private static $_instances = [];

    /**
     * Defining error status
     *
     * @var boolean
     * @access private
     */
    private $checkError = false;

    /**
     * Set API end point
     *
     * @access private
     */
    private $endPoint;

    /**
     * Set API username
     *
     * @access private
     */
    private $userName;

    /**
     * Set API password
     *
     * @access private
     */
    private $password;

    /**
     * Defining HTTP method
     *
     * @var string
     * @access protected
     */
    private $httpVerb;

    /**
     * API calling URL
     *
     * @var string
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
     * @var array
     * @access protected
     */
    protected $fields;

    /**
     * Load API section name
     *
     * @var string
     * @access protected
     */
    protected $section;

    /**
     * Load API method name
     *
     * @var string
     * @access protected
     */
    protected $method;

    /**
     * Assign validation rule according to API method
     *
     * @var array
     * @access protected
     */
    protected $rule;

    /**
     * Store guzzle client object
     *
     * @var object
     * @access protected
     */
    protected $client;

    /**
     * Store parameters data and header to send guzzle client
     *
     * @var array
     * @access protected
     */
    protected $guzzleData;

    /**
     * Initialize credentials for API call
     *
     * @param string $endPoint
     * @param string $userName
     * @param string $password
     */
    protected function __construct($endPoint, $userName, $password)
    {
        $this->endPoint = $endPoint;
        $this->userName = $userName;
        $this->password = $password;
    }

    /**
     * Get the multiton instance
     *
     * @param array $apiInfo
     * @return object
     */
    public static function instance($apiInfo)
    {
        $calledClassName = get_called_class();
        $instanceKey     = md5($calledClassName . '_' . serialize($apiInfo));
        if (!array_key_exists($instanceKey, self::$_instances)) {
            self::$_instances[$instanceKey] = new $calledClassName($apiInfo['endPoint'], $apiInfo['username'], $apiInfo['password']);
        }
        return self::$_instances[$instanceKey];
    }

    /**
     * Get raw API response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->apiResponse;
    }

    /**
     * Get raw API response in array format
     *
     * @return array
     */
    public function getArrayResponse()
    {
        if ($this->checkError) {
            return ['curlError' => $this->apiResponse];
        }
        $formattedResponse = null;
        if (!empty($this->fields['return_format']) && $this->fields['return_format'] == 'json') {
            $formattedResponse = $this->isJson($this->apiResponse, true);
        } else {
            $response = [];
            parse_str($this->apiResponse, $response);
            $formattedResponse = $response;
        }
        return $formattedResponse;
    }

    /**
     * Get API response in object format
     *
     * @return object
     */
    public function getObjectResponse()
    {
        if ($this->checkError) {
            return json_decode(json_encode(['curlError' => $this->apiResponse]));
        }
        $formattedResponse = null;
        if (!empty($this->fields['return_format']) && $this->fields['return_format'] == 'json') {
            $formattedResponse = $this->isJson($this->apiResponse);
        } else {
            $response = [];
            parse_str($this->apiResponse, $response);
            $formattedResponse = json_decode(json_encode($response));
        }
        return $formattedResponse;
    }

    /**
     * Get the last payload info
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
     * @access private
     *
     * @return void
     *
     * @throws Exception
     * When validation error found
     */
    private function fieldValidate()
    {
        $validator = Validator::make(
            $this->fields,
            $this->rule
        );
       
        if ($validator->fails()) {

            throw new Exception(implode(' ', $validator->errors()->all()));

        }
    }

    /**
     * Handling API request using GuzzleHttp
     *
     * @access private
     * @return void
     */
    private function request()
    {
        try {
            $this->apiResponse = null;
            $this->client      = new \GuzzleHttp\Client();
            $response          = $this->client->request(
                $this->httpVerb,
                $this->apiUrl,
                $this->guzzleData
            );
            $content = $response->getBody()->getContents();

            if ($response->getStatusCode() != 200) {
                throw new Exception($ex->getMessage());
            }
            $this->apiResponse = ($this->headerRequired) ? ['content' => $content, 'header' => $header] : $content;
        } catch (\Guzzle\Http\Exception\BadResponseException $ex) {
            $this->checkError  = true;
            $this->apiResponse = $ex->getMessage();
        }
    }

    /**
     * Preparing request data to make API request.
     *
     * @access protected
     * @return void
     */
    protected function __post()
    {
        $className  = explode('\\', get_called_class());
        $rules      = Config::get(end($className));
        $this->rule = $rules['apiRules'][camel_case($this->method)];
        $this->fieldValidate();
        $this->httpVerb           = 'POST';
        $this->apiUrl             = rtrim($this->endPoint, '/') . '/admin/' . ($this->section == 'transact' ? 'transact.php' : 'membership.php');
        $this->fields['username'] = $this->userName;
        $this->fields['password'] = $this->password;
        $this->fields['method']   = $this->method;
        $this->guzzleData         = ['form_params' => $this->fields];
        
        $this->request();
    }

    /**
     * Method to check a string is JSON or not
     *
     * @param  string  $string
     * @param  boolean $arrFlag
     *
     * @return mixed
     *
     * @throws Exception
     * When API response is not in desired JSON format
     */
    protected function isJson($string, $arrFlag = false)
    {
        $decodedResponse = json_decode($string, $arrFlag);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(Config::get('Messages.jsonFormatError'));
        }
        return $decodedResponse;
    }

}
