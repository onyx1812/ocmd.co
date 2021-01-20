<?php
defined( 'ABSPATH' ) || exit;

class WFFN_WooFunnels_Support {

	public static $_instance = null;


	public function __construct() {

		add_filter( 'woofunnels_default_reason_' . WFFN_PLUGIN_BASENAME, function () {
			return 1;
		} );
		add_filter( 'woofunnels_default_reason_default', function () {
			return 1;
		} );
		$this->encoded_basename = sha1( WFFN_PLUGIN_BASENAME );

		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 85 );
		add_action( 'admin_menu', array( $this, 'add_menus' ), 80.1 );

	}

	/**
	 * @return null|WFFN_WooFunnels_Support
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	public function woofunnels_page() {

		if ( null === filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING ) ) {
			if ( class_exists( 'WFFN_Pro_Core' ) ) {
				WooFunnels_dashboard::$selected = 'licenses';
			}else{
				WooFunnels_dashboard::$selected = 'support';
			}
		}

		WooFunnels_dashboard::load_page();
	}

	/**
	 * Adding WooCommerce sub-menu for global options
	 */
	public function add_menus() {
		if ( ! WooFunnels_dashboard::$is_core_menu ) {
			add_menu_page( __( 'WooFunnels', 'funnel-builder' ), __( 'WooFunnels', 'funnel-builder' ), 'manage_options', 'woofunnels', array(
				$this,
				'woofunnels_page',
			), '', 59 );
			add_submenu_page( 'woofunnels', __( 'Licenses', 'funnel-builder' ), __( 'License', 'funnel-builder' ), 'manage_options', 'woofunnels' );
			WooFunnels_dashboard::$is_core_menu = true;
		}
	}

	public function register_admin_menu() {
		WFFN_Core()->admin->register_admin_menu();
	}

	public function is_onboarding_complete() {
		return get_option( '_wffn_onboarding_completed', false );
	}




}

if ( class_exists( 'WFFN_WooFunnels_Support' ) ) {
	WFFN_Core::register( 'support', 'WFFN_WooFunnels_Support' );
}

/**new WFFN_WooFunnels_Support();*/
