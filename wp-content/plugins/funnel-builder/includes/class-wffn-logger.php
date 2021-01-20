<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WFFN_Logger
 * @package Autobot
 * @author XlPlugins
 */
class WFFN_Logger {

	private static $ins = null;

	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function log( $message, $file_name = 'wffn' ) {
		$should_logs_made = true;
		/** Restricting logs creation for bulk execution */
		$should_logs_made = apply_filters( 'wffn_before_making_logs', $should_logs_made );

		if ( ! class_exists( 'BWF_Logger' ) ) {
			$bwf_configuration = WooFunnel_Loader::get_the_latest();
			require $bwf_configuration['plugin_path'] . '/woofunnels/includes/class-bwf-logger.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}

		if ( false === $should_logs_made || ! class_exists( 'BWF_Logger' ) ) {
			return;
		}

		$file_name  = sanitize_title( $file_name );
		$logger_obj = BWF_Logger::get_instance();
		$logger_obj->log( $message, $file_name, 'flex-funnels' );
	}

}

if ( class_exists( 'WFFN_Core' ) ) {
	WFFN_Core::register( 'logger', 'WFFN_Logger' );
}
