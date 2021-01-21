<?php
/**
 * Customizer Controls Base.
 *
 * Extend this in other controls.
 *
 * @package     WFOCUKirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       3.0.12
 */

/**
 * A base for controls.
 */
class WFOCUKirki_Control_Base extends WP_Customize_Control {

	/**
	 * Used to automatically generate all CSS output.
	 *
	 * @access public
	 * @var array
	 */
	public $output = array();

	/**
	 * Data type
	 *
	 * @access public
	 * @var string
	 */
	public $option_type = 'theme_mod';

	/**
	 * Option name (if using options).
	 *
	 * @access public
	 * @var string
	 */
	public $option_name = false;

	/**
	 * The wfocukirki_config we're using for this control
	 *
	 * @access public
	 * @var string
	 */
	public $wfocukirki_config = 'global';

	/**
	 * Whitelisting the "required" argument.
	 *
	 * @since 3.0.17
	 * @access public
	 * @var array
	 */
	public $required = array();

	/**
	 * Whitelisting the "preset" argument.
	 *
	 * @since 3.0.26
	 * @access public
	 * @var array
	 */
	public $preset = array();

	/**
	 * Whitelisting the "css_vars" argument.
	 *
	 * @since 3.0.28
	 * @access public
	 * @var string
	 */
	public $css_vars = '';

	/**
	 * Extra script dependencies.
	 *
	 * @since 3.1.0
	 * @return array
	 */
	public function wfocukirki_script_dependencies() {
		return array();
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @access public
	 */
	public function enqueue() {

		// Build the suffix for the script.
		$suffix  = '';
		$suffix .= ( ! defined( 'SCRIPT_DEBUG' ) || true !== SCRIPT_DEBUG ) ? '.min' : '';

		// The WFOCUKirki plugin URL.
		$wfocukirki_url = trailingslashit( WFOCUKirki::$url );

		// Enqueue ColorPicker.
		wp_enqueue_script( 'wp-color-picker-alpha', trailingslashit( WFOCUKirki::$url ) . 'assets/vendor/wp-color-picker-alpha/wp-color-picker-alpha.js', array( 'wp-color-picker' ), WFOCU_KIRKI_VERSION, true );
		wp_enqueue_style( 'wp-color-picker' );

		// Enqueue selectWoo.
		wp_enqueue_script( 'selectWoo', trailingslashit( WFOCUKirki::$url ) . 'assets/vendor/selectWoo/js/selectWoo.full.js', array( 'jquery' ), '1.0.1', true );
		wp_enqueue_style( 'selectWoo', trailingslashit( WFOCUKirki::$url ) . 'assets/vendor/selectWoo/css/selectWoo.css', array(), '1.0.1' );
		wp_enqueue_style( 'wfocukirki-selectWoo', trailingslashit( WFOCUKirki::$url ) . 'assets/vendor/selectWoo/kirki.css', null );

		// Enqueue the script.
		wp_enqueue_script(
			'wfocukirki-script',
			"{$wfocukirki_url}controls/js/script{$suffix}.js",
			array(
				'jquery',
				'customize-base',
				'wp-color-picker-alpha',
				'selectWoo',
				'jquery-ui-button',
				'jquery-ui-datepicker',
			),
			WFOCU_KIRKI_VERSION
		);

		wp_localize_script(
			'wfocukirki-script',
			'wfocukirkiL10n',
			array(
				'isScriptDebug'        => ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ),
				'noFileSelected'       => esc_attr__( 'No File Selected', 'wfocukirki' ),
				'remove'               => esc_attr__( 'Remove', 'wfocukirki' ),
				'default'              => esc_attr__( 'Default', 'wfocukirki' ),
				'selectFile'           => esc_attr__( 'Select File', 'wfocukirki' ),
				'standardFonts'        => esc_attr__( 'Standard Fonts', 'wfocukirki' ),
				'googleFonts'          => esc_attr__( 'Google Fonts', 'wfocukirki' ),
				'defaultCSSValues'     => esc_attr__( 'CSS Defaults', 'wfocukirki' ),
				'defaultBrowserFamily' => esc_attr__( 'Default Browser Font-Family', 'wfocukirki' ),
			)
		);

		$suffix = str_replace( '.min', '', $suffix );
		// Enqueue the style.
		wp_enqueue_style(
			'wfocukirki-styles',
			"{$wfocukirki_url}controls/css/styles{$suffix}.css",
			array(),
			WFOCU_KIRKI_VERSION
		);
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		// Get the basics from the parent class.
		parent::to_json();
		// Default.
		$this->json['default'] = $this->setting->default;
		if ( isset( $this->default ) ) {
			$this->json['default'] = $this->default;
		}
		// Required.
		$this->json['required'] = $this->required;
		// Output.
		$this->json['output'] = $this->output;
		// Value.
		$this->json['value'] = $this->value();
		// Choices.
		$this->json['choices'] = $this->choices;
		// The link.
		$this->json['link'] = $this->get_link();
		// The ID.
		$this->json['id'] = $this->id;
		// Translation strings.
		$this->json['l10n'] = $this->l10n();
		// The ajaxurl in case we need it.
		$this->json['ajaxurl'] = admin_url( 'admin-ajax.php' );
		// Input attributes.
		$this->json['inputAttrs'] = '';
		foreach ( $this->input_attrs as $attr => $value ) {
			$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
		}
		// The wfocukirki-config.
		$this->json['wfocukirkiConfig'] = $this->wfocukirki_config;
		// The option-type.
		$this->json['wfocukirkiOptionType'] = $this->option_type;
		// The option-name.
		$this->json['wfocukirkiOptionName'] = $this->option_name;
		// The preset.
		$this->json['preset'] = $this->preset;
		// The CSS-Variables.
		$this->json['css-var'] = $this->css_vars;
	}

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper in `$this::render()`.
	 *
	 * Supports basic input types `text`, `checkbox`, `textarea`, `radio`, `select` and `dropdown-pages`.
	 * Additional input types such as `email`, `url`, `number`, `hidden` and `date` are supported implicitly.
	 *
	 * Control content can alternately be rendered in JS. See WP_Customize_Control::print_template().
	 *
	 * @since 3.4.0
	 */
	protected function render_content() {}

	/**
	 * An Underscore (JS) template for this control's content (but not its container).
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see WP_Customize_Control::print_template()
	 *
	 * @access protected
	 */
	protected function content_template() {}

	/**
	 * Returns an array of translation strings.
	 *
	 * @access protected
	 * @since 3.0.0
	 * @return array
	 */
	protected function l10n() {
		return array();
	}
}