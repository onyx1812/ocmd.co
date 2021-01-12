<?php

namespace CodeClouds\Unify\Service;

/**
 * Server request handler.
 * @package CodeClouds\Unify
 */
class Request
{
    /**
     * Get GET variable by $key or get all.
     * @param String $key
     * @return String
     */
    public static function get($key = null)
    {
        if($key == null)
        {
            return $_GET;
        }
        
        if(isset($_GET[$key]))
        {
            return $_GET[$key];
        }
        
        return '';
    }
    
    /**
     * Get POST variable by $key or get all.
     * @param String $key
     * @return String/array
     */
    public static function post($key = null)
    {
        if($key == null)
        {
            return $_POST;
        }
        
        if(isset($_POST[$key]))
        {
            return $_POST[$key];
        }
    }
    
    /**
     * Get REQUEST variable by $key or get all.
     * @param String $key
     * @return String/array
     */
    public static function any($key = null)
    {
        if($key == null)
        {
            return $_REQUEST;
        }
        
        if(isset($_REQUEST[$key]))
        {
            return $_REQUEST[$key];
        }
    }
    
    

    /**
     * Get POST variable by $key.
     * @param String $key
     * @return String
     */
    public static function getPost($key)
    {
        if($_POST[$key])
        {
            return $_POST[$key];
        }
    }
    
    /**
     * Get all POST variables as an array.
     * @return array
     */
    public static function getPostArray()
    {
        return $_POST;
    }
    
    /**
	 * Set POST variable.
	 * @param String $key
	 * @param String/array $value
	 */
	public static function setPost($key, $value)
	{
		$_POST[$key] = $value;
	}

}
