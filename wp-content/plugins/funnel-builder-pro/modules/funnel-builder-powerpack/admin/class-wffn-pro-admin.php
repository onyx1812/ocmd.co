<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class to initiate admin functionality
 * Class WFFN_Pro_Admin
 */
class WFFN_Pro_Admin {

	private static $ins = null;

	/**
	 * WFFN_Pro_Admin constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return WFFN_Pro_Admin|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

}

if ( class_exists( 'WFFN_Pro_Core' ) ) {
	WFFN_Pro_Core::register( 'admin', 'WFFN_Pro_Admin' );
}
