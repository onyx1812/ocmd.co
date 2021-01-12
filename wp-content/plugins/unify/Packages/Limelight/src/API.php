<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\Config;
use \Exception;

/**
 * Make API Instance.
 *
 * Initialize Limelight API credential and call method for API call.
 *
 * @package  Limelight
 * @author   CodeClouds <sales@codeclouds.com>
 */
class API
{
    /**
     * Set API end point
     *
     * @var string
     */
    public $endPoint;

    /**
     * Set API username
     *
     * @var string
     */
    public $userName;

    /**
     * Set API password
     *
     * @var string
     */
    public $password;

    /**
     * Defining API methods dependencies
     *
     * @var array
     * @access private
     */
    private $dependencies = [
        'CodeClouds\\Limelight\\Order',
        'CodeClouds\\Limelight\\Campaign',
        'CodeClouds\\Limelight\\Customer',
        'CodeClouds\\Limelight\\Transaction',
        'CodeClouds\\Limelight\\Member',
    ];

    /**
     * Store parent class methods
     *
     * @var array
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
     * @param string $endPoint
     * @param string $userName
     * @param string $password
     *
     * @throws Exception
     * If the endPoint or userName or password is empty
     */
    public function __construct($endPoint, $userName, $password)
    {
        if (empty($endPoint) || empty($userName) || empty($password)) {
            throw new Exception(Config::get('Messages.invalidAuth'));
        }
        $this->endPoint = $endPoint;
        $this->userName = $userName;
        $this->password = $password;
    }

    /**
     * __call() method to calling expected method
     *
     * @param  string $method
     * Method name for API call
     *
     * @param  array $data
     * Required data to call API
     *
     * @return objects
     */
    public function __call($method, $data)
    {
        $arguments             = reset($data);
        $invokeInstance        = [$this->validateMethods($method), 'instance'];
        $this->desiredInstance = $invokeInstance([
            'endPoint' => $this->endPoint,
            'username' => $this->userName,
            'password' => $this->password,
        ]);
        $invokeMethod = [$this->desiredInstance, $method];
        $invokeMethod($arguments);
        return $this;
    }

    /**
     * Get raw API response
     *
     * @param  boolean $payloadFlag
     * Show/Hide input parameters, the default value is set false
     *
     * @return array
     * Return API raw response
     *
     * @throws Exception
     * When method is not found
     */
    public function get($payloadFlag = false)
    {
        if (empty($this->desiredInstance)) {
            throw new Exception(Config::get('Messages.apiMethodInvoked'));
        }

        $responseMethod = [$this->desiredInstance, 'getResponse'];
        $payloadMethod  = [$this->desiredInstance, 'getPayloadInfo'];
        return !$payloadFlag ? [
            'response' => $responseMethod(),
        ] : [
            'response' => $responseMethod(),
            'payload'  => array_merge([
                'endPoint' => $this->endPoint,
            ], $payloadMethod()),
        ];
    }

    /**
     * Get API response in an array format
     *
     * @param  boolean $payloadFlag
     * Show/Hide input parameters, default value is set false
     *
     * @return array
     * Return API response in an array
     *
     * @throws Exception
     * When method is not found
     */
    public function getInArray($payloadFlag = false)
    {
        if (empty($this->desiredInstance)) {
            throw new Exception(Config::get('Messages.apiMethodInvoked'));
        }

        $responseMethod = [$this->desiredInstance, 'getArrayResponse'];
        $payloadMethod  = [$this->desiredInstance, 'getPayloadInfo'];
        return !$payloadFlag ? [
            'response' => $responseMethod(),
        ] : [
            'response' => $responseMethod(),
            'payload'  => array_merge([
                'endPoint' => $this->endPoint,
            ], $payloadMethod()),
        ];
    }

    /**
     * Get API response in an object format
     *
     * @param  boolean $payloadFlag
     * Show/Hide input parameters, default value is set false
     *
     * @return array
     * Return API response in an object
     *
     * @throws Exception
     * When method is not found
     */
    public function getInObject($payloadFlag = false)
    {
        if (empty($this->desiredInstance)) {
            throw new Exception(Config::get('Messages.apiMethodInvoked'));
        }

        $responseMethod = [$this->desiredInstance, 'getObjectResponse'];
        $payloadMethod  = [$this->desiredInstance, 'getPayloadInfo'];
        return !$payloadFlag ? [
            'response' => $responseMethod(),
        ] : [
            'response' => $responseMethod(),
            'payload'  => (object) array_merge([
                'endPoint' => $this->endPoint,
            ], $payloadMethod()),
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
    private function validateMethods($method)
    {
        $this->parentMethods = get_class_methods('CodeClouds\\Limelight\\Limelight');
        $desiredDependency   = null;
        foreach ($this->dependencies as $dependency) {
            $methods = get_class_methods($dependency);
            if (in_array($method, array_diff($methods, $this->parentMethods))) {
                $desiredDependency = $dependency;
                break;
            }
        }
        if (empty($desiredDependency)) {
            throw new Exception(Config::get('Messages.methodNotFound'));
        }
        return $desiredDependency;
    }

}
