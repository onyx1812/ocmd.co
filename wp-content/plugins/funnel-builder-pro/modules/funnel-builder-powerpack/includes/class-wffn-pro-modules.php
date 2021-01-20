<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Funnel Modules facing functionality
 * Class WFFN_Pro_Modules
 */
class WFFN_Pro_Modules {

	public static $modules = [];

	public static function init_modules() {

		add_action( 'admin_init', array( __CLASS__, 'update_modules' ) );

		foreach ( glob( plugin_dir_path( WFFN_PRO_PLUGIN_FILE ) . 'modules/*.php' ) as $module_name ) {
			$basename = basename( $module_name );
			if ( false !== strpos( $basename, 'index.php' ) ) {
				continue;
			}
			require_once( plugin_dir_path( WFFN_PRO_PLUGIN_FILE ) . 'modules/' . $basename );
		}
	}

	public static function update_modules() {
		$modules       = get_option( '_bwf_individual_modules', [] );
		$all_plugins   = get_option( 'woofunnels_plugins_info', [] );
		$update_needed = false;

		foreach ( $all_plugins as $plugin_data ) {
			$plugin_data = is_array( $plugin_data ) ? $plugin_data : [];
			$sw_title    = ( isset( $plugin_data['data_extra'] ) && isset( $plugin_data['data_extra']['software_title'] ) ) ? $plugin_data['data_extra']['software_title'] : '';

			if ( empty( $sw_title ) ) {
				continue;
			}

			if ( ! isset( $modules['bump'] ) && 'OrderBumps: WooCommerce Checkout Offers' === $sw_title ) {
				$modules['bump'] = 'yes';
				$update_needed   = true;
			}
			if ( ! isset( $modules['checkout'] ) && 'Aero: Custom WooCommerce Checkout Pages' === $sw_title ) {
				$modules['checkout'] = 'yes';
				$update_needed       = true;
			}
			if ( ! isset( $modules['upsells'] ) && 'Upstroke: WooCommerce One Click Upsell' === $sw_title ) {
				$modules['upsells'] = 'yes';
				$update_needed      = true;
			}
		}
		if ( $update_needed ) {
			update_option( '_bwf_individual_modules', $modules, true );
		}
	}

	public static function register( $basename, $class ) {
		self::$modules[ $basename ] = $class;
	}

	public static function maybe_load( $basename ) {
		$module = self::get_module( $basename );
		$module::maybe_load();
	}

	public static function get_module( $basename ) {
		return self::$modules[ $basename ];
	}


}

add_action( 'plugins_loaded', function () {
	WFFN_Pro_Modules::init_modules();
	do_action('wffn_pro_modules_loaded');
}, - 500 );


