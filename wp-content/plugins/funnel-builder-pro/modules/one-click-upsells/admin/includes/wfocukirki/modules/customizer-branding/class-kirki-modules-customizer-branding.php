<?php
/**
 * Changes the customizer's branding.
 * For documentation please see
 * https://github.com/aristath/kirki/wiki/Styling-the-Customizer
 *
 * @package     WFOCUKirki
 * @category    Modules
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds styles to the customizer.
 */
class WFOCUKirki_Modules_Customizer_Branding {

	/**
	 * The object instance.
	 *
	 * @static
	 * @access private
	 * @since 3.0.0
	 * @var object
	 */
	private static $instance;

	/**
	 * Constructor.
	 *
	 * @access protected
	 * @since 3.0.0
	 */
	protected function __construct() {
		add_action( 'customize_controls_print_scripts', array( $this, 'customize_controls_print_scripts' ) );
	}

	/**
	 * Gets an instance of this object.
	 * Prevents duplicate instances which avoid artefacts and improves performance.
	 *
	 * @static
	 * @access public
	 * @since 3.0.0
	 * @return object
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Enqueues the script responsible for branding the customizer
	 * and also adds variables to it using the wp_localize_script function.
	 * The actual branding is handled via JS.
	 *
	 * @access public
	 * @since 3.0.0
	 */
	public function customize_controls_print_scripts() {

		$config = apply_filters( 'wfocukirki_config', array() );
		$vars   = array(
			'logoImage'   => '',
			'description' => '',
		);
		if ( isset( $config['logo_image'] ) && '' !== $config['logo_image'] ) {
			$vars['logoImage'] = esc_url_raw( $config['logo_image'] );
		}
		if ( isset( $config['description'] ) && '' !== $config['description'] ) {
			$vars['description'] = esc_textarea( $config['description'] );
		}

		if ( ! empty( $vars['logoImage'] ) || ! empty( $vars['description'] ) ) {
			wp_register_script( 'wfocukirki-branding', WFOCUKirki::$url . '/modules/customizer-branding/branding.js', array(), WFOCU_KIRKI_VERSION );
			wp_localize_script( 'wfocukirki-branding', 'wfocukirkiBranding', $vars );
			wp_enqueue_script( 'wfocukirki-branding' );
		}
	}
}
