<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff

use Elementor\Plugin;

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_Pro_Optin_Pages_Elementor
 */
class WFFN_Pro_Optin_Pages_Elementor {

	private static $ins = null;
	protected $template_type = [];
	protected $design_template_data = [];
	protected $templates = [];
	/**
	 * WFFN_Optin_Pages_Elementor constructor.
	 */
	public function __construct() {

		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ], 11 );
	}

	/**
	 * @return WFFN_Pro_Optin_Pages_Elementor|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function register_widgets() {
		if ( did_action( 'wffn_optin_elementor_lite_loaded' ) ) {
			require_once( __DIR__ . '/widgets/class-elementor-wffn-pro-optin-popup-widget.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_WFFN_Pro_Optin_Popup_Widget() );
		}
	}
}

WFFN_Pro_Optin_Pages_Elementor::get_instance();
