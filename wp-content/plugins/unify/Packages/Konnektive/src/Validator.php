<?php

namespace CodeClouds\Konnektive;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation;
use Illuminate\Validation;

/**
 * Validate the rules defined in configuration files
 * 
 * @package Konnektive
 * 
 * @final
 */
final class Validator
{

	/**
	 * use to get the Illuminate Validation Factory instance.
	 * 
	 * @access private
	 */
	private $factory;

	/**
	 * use to get the Illuminate Validation Factory instance
	 * 
	 * @access protected
	 */
	protected function __construct()
	{
		$this->factory = new Validation\Factory(
			$this->loadTranslator()
		);
	}

	/**
	 * Load and compile the language file using the translator
	 *
	 * @return \Illuminate\Translation\Translator
	 * 
	 * @access protected
	 */
	protected function loadTranslator()
	{
		$filesystem	 = new Filesystem();
		$loader		 = new Translation\FileLoader(
			$filesystem, dirname( dirname( __FILE__ ) ) . '/lang' );
		$loader->addNamespace(
			'lang', dirname( dirname( __FILE__ ) ) . '/lang'
		);
		$loader->load( 'en', 'validation', 'lang' );
		return new Translation\Translator( $loader, 'en' );
	}

	/**
	 * A magic method used for the method overloading
	 *
	 * @param string $method
	 * @param array $args
	 * 
	 * @return mixed
	 */
	public function __call( $method, $args )
	{
		return call_user_func_array(
			[ $this->factory, $method ], $args
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
	public static function __callStatic( $method, $args )
	{
		$validatorInstance = self::instance();
		return call_user_func_array(
			[ $validatorInstance, $method ], $args
		);
	}

	/**
	 * To get the singleton instance
	 *
	 * @staticvar type $inst
	 * 
	 * @return \self
	 */
	public static function instance()
	{
		static $inst = null;
		if( $inst === null )
		{
			$inst = new self();
		}
		return $inst;
	}

}
