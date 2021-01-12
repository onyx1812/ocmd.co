<?php

namespace CodeClouds\ResponseCRM;

use CodeClouds\ResponseCRM\Repository;

/**
 * Configuration Repository.
 *
 * Set configuration and retrieve the specified configuration value.
 *
 * @final
 * @package  ResponseCRM
 */
final class Repository
{
    /**
     * Set configuration array
     *
     * @var array
     * @access protected
     */
    protected static $config;

    /**
     * Get the specified configuration value.
     *
     * @param  string  $configKey
     * @return array|string
     */
    public static function get($configKey)
    {
        if (!empty($configKey)) {
            if (strpos($configKey, '.') !== false) {
                $configkeyArr = explode('.', $configKey);
                $conf         = [];
                for ($i = 0; $i < count($configkeyArr); $i++) {
                    $conf = isset($conf[$configkeyArr[$i]]) ? $conf[$configkeyArr[$i]] : self::$config[$configkeyArr[$i]];
                }
            } else {
                $conf = self::$config[$configKey];
            }
            return $conf;
        }
    }

    /**
     * Set a given configuration value.
     *
     * @param  string $configKey
     * @param  array  $configVal
     * @return bool
     */
    public static function set($configKey, $configVal)
    {
        if (!empty($configKey)) {
            self::$config[$configKey] = $configVal;
        }
        return true;
    }

}
