<?php

/**
 * Class WFOB_Plugin_Compatibilities
 * Loads all the compatibilities files we have to provide compatibility with each plugin
 */
class WFOB_Plugin_Compatibilities {

	public static $plugin_compatibilities = array();

	public static function load_all_compatibilities() {
		// load all the WFOB_Compatibilities files automatically
		foreach ( glob( plugin_dir_path( WFOB_PLUGIN_FILE ) . '/compatibilities/*.php' ) as $_field_filename ) {
			require_once( $_field_filename );
		}
	}


	public static function register( $object, $slug ) {
		self::$plugin_compatibilities[ $slug ] = $object;
	}

	public static function get_compatibility_class( $slug ) {
		return ( isset( self::$plugin_compatibilities[ $slug ] ) ) ? self::$plugin_compatibilities[ $slug ] : false;
	}
}


WFOB_Plugin_Compatibilities::load_all_compatibilities();

