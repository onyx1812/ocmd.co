<?php

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Funnel pro optin page module
 * Class WFFN_Pro_Optin_Pages
 */
class WFFN_Pro_Optin_Pages {

	private static $ins = null;
	protected $options;
	protected $custom_options;
	const WFOP_PHONE_FIELD_SLUG = 'optin_phone';
	const FIELD_PREFIX = 'wfop_';

	/**
	 * WFFN_Pro_Optin_Pages constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'include_compatibility_files' ] );
		add_action( 'wfopp_before_optin_customizer_settings', [ $this, 'add_pro_optin_customizer_settings' ] );
	}

	/**
	 * @return WFFN_Pro_Optin_Pages|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function get_module_path() {
		return plugin_dir_path( WFOPP_PRO_PLUGIN_FILE ) . 'modules/optin-pages/';
	}

	public function include_compatibility_files() {
		include_once $this->get_module_path() . 'compatibilities/page-builders/elementor/class-wffn-pro-optin-pages-elementor.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	}

	public function add_pro_optin_customizer_settings() {
		include_once __DIR__ . '/admin/views/optin-pages/pro-form-customize.phtml';
	}
}

if ( class_exists( 'WFOPP_PRO_Core' ) ) {
	WFOPP_PRO_Core::register( 'pro_optin_pages', 'WFFN_Pro_Optin_Pages' );
}