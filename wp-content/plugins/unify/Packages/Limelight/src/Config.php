<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\ConfigRepository;
use Symfony\Component\Finder\Finder;

/**
 * Load Config Files.
 *
 * Dynamically load all configuration files.
 *
 * @final
 * @package  Limelight
 */
final class Config
{
    /**
     * Get Illuminate Configuration Repository instance
     *
     * @access private
     */
    private $repository;

    /**
     * Create instance of Repository and call loadConfiguration method
     */
    protected function __construct()
    {
        $this->repository = new ConfigRepository();
        $this->loadConfiguration();
    }

    /**
     * Load configuration file using the translator
     *
     * @access protected
     * @return void
     */
    protected function loadConfiguration()
    {
        $configPath  = dirname(dirname(__FILE__)) . '/config';
        $configFiles = Finder::create()->files()->name('*.php')->in($configPath)->depth(0);
        foreach ($configFiles as $file) {
            $this->repository->set(basename($file->getRealPath(), '.php'), require $file->getRealPath());
        }
    }

    /**
     * A magic method used for the method overloading
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(
            [$this->repository, $method], $args
        );
    }

    /**
     * A magic method used for the static method overloading
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $configInstance = self::instance();
        return call_user_func_array(
            [$configInstance, $method], $args
        );
    }

    /**
     * To get the singleton instance
     *
     * @staticvar type $inst
     * @return \self
     */
    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }
        return $inst;
    }

}
