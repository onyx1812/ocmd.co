<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Funnel optin page module
 * Class WFFN_Optin_Pages
 */
class WFFN_Form_Builder {

	private static $ins = null;

	protected $form_option_customization;
	protected $form_fields;
	protected $form_layout;
	public $is_preview = false;

	/**
	 * WFFN_Optin_Pages constructor.
	 */
	public function __construct() {

	}


	/**
	 * @return WFFN_Form_Builder|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}


	public function form_customization_settings_default() {
		return array(
			'input_border_type'  => 'solid',
			'input_border_size'  => '1',
			'input_border_color' => '#ececec',
			'field_size'         => 'small',
			'input_font_size'    => '12',
			'input_font_family'  => 'inherit',
			'input_font_weight'  => '400',
			'show_input_label'   => 'yes',
			'label_font_size'    => '14',
			'input_label_color'  => '#000000',
			'input_text_color'   => '#444444',
			'input_bg_color'     => '#fff',

			'button_text'               => __( 'Send Me My Free Guide', 'funnel-builder' ),
			'button_submitting_text'    => __( 'Submitting...', 'funnel-builder' ),
			'button_size'               => 'med',
			'button_border_type'        => 'solid',
			'button_border_size'        => '2',
			'button_border_color'       => '#E69500',
			'button_font_size'          => '16',
			'button_font_family'        => 'inherit',
			'button_font_weight'        => '700',
			'button_text_color'         => '#ffffff',
			'button_text_color_hover'   => '#ffffff',
			'button_bg_color'           => '#FBA506',
			'button_bg_color_hover'     => '#E69500',
			'button_border_color_hover' => '#FBA506',

			'popup_heading'             => __( 'You\'re just one step away!', 'funnel-builder' ),
			'popup_heading_color'       => '#000000',
			'popup_heading_font_size'   => '17',
			'popup_heading_font_family' => 'inherit',
			'popup_heading_font_weight' => '400',

			'popup_sub_heading'             => __( 'Enter your details below and we\'ll get you signed up', 'funnel-builder' ),
			'popup_sub_heading_color'       => '#000000',
			'popup_sub_heading_font_size'   => '24',
			'popup_sub_heading_font_family' => 'inherit',
			'popup_sub_heading_font_weight' => '700',

			'popup_bar_pp'          => 'enable',
			'popup_bar_animation'   => 'yes',
			'popup_bar_text'        => __( '75% Complete', 'funnel-builder' ),
			'popup_bar_width'       => '75',
			'popup_bar_height'      => '30',
			'popup_bar_font_family' => 'inherit',
			'popup_bar_font_size'   => '16',
			'popup_bar_inner_gap'   => '4',
			'popup_bar_text_color'  => '#ffffff',
			'popup_bar_color'       => '#338d48',
			'popup_bar_bg_color'    => '#efefef',

			'popup_footer_text'        => __( 'Your Information is 100% Secure', 'funnel-builder' ),
			'popup_footer_font_family' => 'inherit',
			'popup_footer_font_size'   => '16',
			'popup_footer_text_color'  => '#444444',

			'popup_width'          => '600',
			'popup_padding'        => '40',
			'popup_open_animation' => 'slide-down',
		);
	}

	/**
	 * @return array|mixed
	 */
	public function get_preview_form_config() {
		$data = isset( $_POST['wffn_custom_form'] ) ? wffn_clean( json_decode( wp_unslash( wffn_clean( $_POST['wffn_custom_form'] ) ) ), true ) : []; //phpcs:ignore WordPress.Security.NonceVerification.Missing

		return $data;
	}

	/**
	 * @param $optin_page_id
	 *
	 * @return array
	 */
	public function setup_form_options_customization( $optin_page_id ) {
		$db_options = get_post_meta( $optin_page_id, 'wffn_form_customization_settings', true );
		$db_options = ( ! empty( $db_options ) && is_array( $db_options ) ) ? $db_options : array();

		return wp_parse_args( $db_options, $this->form_customization_settings_default() );
	}

	/**
	 * @param string $key
	 * @param int $optin_page_id
	 *
	 * @return array|false|mixed
	 */
	public function get_form_customization_option( $key = 'all', $optin_page_id = 0 ) {
		if ( null === $this->form_option_customization ) {
			$this->form_option_customization = $this->setup_form_options_customization( $optin_page_id );
		}
		if ( 'all' === $key ) {
			return $this->form_option_customization;
		}

		return isset( $this->form_option_customization[ $key ] ) ? $this->form_option_customization[ $key ] : false;
	}

	/**
	 * @param int $object_id
	 * @param string $key
	 *
	 * @return array|false|mixed
	 */
	public function get_form_fields( $object_id = 0, $key = 'all' ) {
		if ( null === $this->form_fields ) {
			$this->form_fields = $this->setup_form_fields_option( $object_id );
		}
		if ( 'all' === $key ) {
			return $this->form_fields;
		}

		return isset( $this->form_fields[ $key ] ) ? $this->form_fields[ $key ] : false;
	}

	/**
	 * @param $optin_id
	 *
	 * @return array
	 */
	public function get_optin_layout( $optin_id ) {
		if ( null === $this->form_layout ) {
			$this->form_layout = $this->setup_form_fields_layout( $optin_id );
		}

		return $this->form_layout;
	}

	/**
	 * @param $object_id
	 *
	 * @return array
	 */
	public function setup_form_fields_layout( $object_id ) {
		$db_options = get_post_meta( $object_id, '_wfop_page_layout', true );

		$form_layout = $steps = [];
		if ( ! empty( $db_options ) && is_array( $db_options ) && count( $db_options ) > 0 ) {
			$steps = ( isset( $db_options['fieldsets'] ) && isset( $db_options['fieldsets'] ) ) ? $db_options['fieldsets'] : [];
		} else {
			$optin_layout = WFOPP_Core()->optin_pages->get_page_layout( $object_id );
			$steps        = isset( $optin_layout['fieldsets'] ) ? $optin_layout['fieldsets'] : [];
		}
		foreach ( $steps as $step_slug => $step ) {
			$form_layout[ $step_slug ] = [];
			$step_fields               = ( is_array( $step ) && count( $step ) > 0 && isset( $step[0]['fields'] ) ) ? $step[0]['fields'] : [];
			foreach ( $step_fields as $field ) {

				$form_layout[ $step_slug ][] = $this->get_processed_form_field( $field );
			}
		}

		return ( count( $form_layout ) > 0 ) ? $form_layout : [];
	}

	public function get_processed_form_field( $field ) {
		$output = array(
			'type'        => isset( $field['type'] ) ? $this->get_optin_filed_type( $field ) : 'text',
			'label'       => isset( $field['label'] ) ? $field['label'] : '',
			'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			'width'       => isset( $field['width'] ) ? $field['width'] : 'wffn-sm-100',
			'required'    => isset( $field['required'] ) ? wffn_string_to_bool( $field['required'] ) : '',
			'InputName'   => isset( $field['id'] ) ? $field['id'] : '',
			'default'     => isset( $field['default'] ) ? $field['default'] : '',
			'options'     => isset( $field['options'] ) ? implode( ',', $field['options'] ) : '',
		);
		if ( isset( $field['radio_alignment'] ) ) {
			$output['radio_alignment'] = $field['radio_alignment'];
		}

		return $output;
	}

	/**
	 * @param $object_id
	 *
	 * @return array|mixed
	 */
	public function setup_form_fields_option( $object_id ) {
		if ( true === $this->is_preview ) {
			return $this->get_preview_form_config();
		}
		$db_options = get_post_meta( $object_id, '_wfop_page_layout', true );
		$all_fields = [];
		if ( ! empty( $db_options ) && is_array( $db_options ) ) {
			$steps = ( isset( $db_options['fieldsets'] ) && isset( $db_options['fieldsets'] ) ) ? $db_options['fieldsets'] : [];
			foreach ( $steps as $step ) {
				$step_fields = ( is_array( $step ) && count( $step ) > 0 && isset( $step[0]['fields'] ) ) ? $step[0]['fields'] : [];
				foreach ( $step_fields as $field ) {
					$all_fields[] = $this->get_processed_form_field( $field );
				}
			}
		}

		return ( count( $all_fields ) > 0 ) ? $all_fields : $this->default_fields();
	}

	/**
	 * @param $field
	 *
	 * @return mixed|string
	 */
	public function get_optin_filed_type( $field ) {
		if ( isset( $field['id'] ) ) {
			$default_fields = array(
				WFFN_Optin_Pages::FIELD_PREFIX . WFFN_Optin_Pages::WFOP_FIRST_NAME_FIELD_SLUG => WFFN_Optin_Pages::WFOP_FIRST_NAME_FIELD_SLUG,
				WFFN_Optin_Pages::FIELD_PREFIX . WFFN_Optin_Pages::WFOP_LAST_NAME_FIELD_SLUG  => WFFN_Optin_Pages::WFOP_LAST_NAME_FIELD_SLUG,
				WFFN_Optin_Pages::FIELD_PREFIX . WFFN_Optin_Pages::WFOP_EMAIL_FIELD_SLUG      => WFFN_Optin_Pages::WFOP_EMAIL_FIELD_SLUG,
				WFFN_Optin_Pages::FIELD_PREFIX . WFFN_Optin_Pages::WFOP_PHONE_FIELD_SLUG      => WFFN_Optin_Pages::WFOP_PHONE_FIELD_SLUG,
			);
			if ( array_key_exists( $field['id'], $default_fields ) ) {
				return $default_fields[ $field['id'] ];
			}
		}

		return $field['type'];
	}

	public function default_fields() {
		return array(
			WFOPP_Core()->form_fields->get_integration_object( WFFN_Optin_Pages::WFOP_FIRST_NAME_FIELD_SLUG )->get_field_format(),
			WFOPP_Core()->form_fields->get_integration_object( WFFN_Optin_Pages::WFOP_EMAIL_FIELD_SLUG )->get_field_format(),
		);
	}


	/**
	 * @param $object_id
	 * @param $data
	 */
	public function save_form_field_width( $object_id, $data ) {

		$instance = WFFN_Optin_Pages::get_instance();
		$data     = json_decode( stripslashes( $data ), true );

		if ( ! is_array( $data ) ) {
			return;
		}

		$page_data       = $instance->get_page_layout( $object_id );
		$selected_fields = $page_data['fieldsets'];

		foreach ( $selected_fields as $step => $step_data ) {
			if ( ! is_array( $step_data ) ) {
				continue;
			}

			foreach ( $step_data as $index => $section ) {
				if ( empty( $section['fields'] ) ) {
					continue;
				}
				$fields = $section['fields'];
				foreach ( $fields as $f_index => $field ) {
					if ( ! isset( $field['id'] ) || ! isset( $field['field_type'] ) ) {
						continue;
					}
					$id                                                                       = $field['id'];
					$page_data['fieldsets'][ $step ][ $index ]['fields'][ $f_index ]['width'] = $data[ $id ];
				}
			}
		}

		update_post_meta( $object_id, '_wfop_page_layout', $page_data );
	}


	/**
	 * @param $object_id
	 * @param $data
	 */
	public function save_form_customizations( $object_id, $data ) {
		update_post_meta( $object_id, 'wffn_form_customization_settings', $data );
	}



}