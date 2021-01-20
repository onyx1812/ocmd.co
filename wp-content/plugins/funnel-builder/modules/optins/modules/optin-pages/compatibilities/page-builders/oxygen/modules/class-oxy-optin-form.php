<?php

class WFFN_Optin_Oxy_Form extends WFFN_Optin_OXY_HTML_BLOCK {
	public $slug = 'wffn_optin_oxy_form';
	public $form_sub_headings = [];
	protected $get_local_slug = 'wfacp_form';
	protected $id = 'wffn_optin_oxy_checkout_form';
	private $custom_class_tab_id = '';

	public function __construct() {
		$this->name = __( 'Optin Form', 'woofunnels-aero-checkout' );
		parent::__construct();
	}


	public function name() {
		return $this->name;
	}

	/**
	 * @param $template WFACP_Template_Common;
	 */
	public function setup_data() {
		$this->register_form_fields();
		$this->register_form_styles();
	}

	protected function register_form_fields() {
		$tab_id      = $this->add_tab( __( 'Form Settings', 'funnel-builder' ) );
		$optinPageId = WFOPP_Core()->optin_pages->get_id();
		$get_fields  = [];
		if ( $optinPageId > 0 ) {
			$get_fields = WFOPP_Core()->optin_pages->form_builder->get_form_fields( $optinPageId );
		}
		foreach ( is_array( $get_fields ) ? $get_fields : [] as $key => $field ) {
			$options = [
				'wffn-sm-100' => __( 'Full', 'funnel-builder' ),
				'wffn-sm-50'  => __( 'One Half', 'funnel-builder' ),
				'wffn-sm-33'  => __( 'One Third', 'funnel-builder' ),
				'wffn-sm-67'  => __( 'Two Third', 'funnel-builder' ),
			];
			$default = isset( $field['width'] ) ? $field['width'] : 'wffn-sm-100';
			$this->add_select( $tab_id, $field['InputName'], esc_html__( $field['label'] ), $options, $default );
		}
		$this->add_switcher( $tab_id, 'show_labels', __( 'Label', 'funnel-builder' ), 'on' );
		$this->add_heading( $tab_id, __( 'Submit Button', 'funnel-builder' ) );
		$this->add_text( $tab_id, 'button_text', __( 'Title', 'funnel-builder' ), __( 'Signup Now', 'funnel-builder' ), [], '', __( 'Enter the Button Text', 'funnel-builder' ) );
		$this->add_text( $tab_id, 'subtitle', 'Sub Title', '', [], '', __( 'Enter subtitle', 'funnel-builder' ) );
		$this->add_text( $tab_id, 'button_submitting_text', 'Submitting Text', __( 'Submitting...', 'funnel-builder' ) );
		do_action( 'wffn_additional_controls', $this );

	}

	protected function register_form_styles() {
		$tab_id    = $this->add_tab( __( 'Form Style', 'funnel-builder' ) );
		$condition = [
			'show_labels' => 'on',
		];
		$this->add_heading( $tab_id, __( 'Label', 'funnel-builder' ), '', $condition );


		$this->add_color( $tab_id, 'mark_required_color', '.bwfac_form_sec > label > span, .bwfac_form_sec .wfop_input_cont > label > span', __( 'Asterisk', 'funnel-builder' ), '#000000', $condition );
		$this->add_typography( $tab_id, 'label_typography', '.bwfac_form_sec > label, .bwfac_form_sec .wfop_input_cont > label', __( 'Label Typography' ) );

		$this->add_heading( $tab_id, __( 'Input', 'funnel-builder' ) );
		$this->add_background_color( $tab_id, 'field_background_color', '.bwfac_form_sec .wffn-optin-input', __( 'Background', 'funnel-builder' ), '#ffffff' );
		$this->add_typography( $tab_id, 'field_typography', '.bwfac_form_sec .wffn-optin-input', __( 'Field Typography' ) );

		$this->add_heading( $tab_id, __( 'Advanced', 'funnel-builder' ) );
		$this->add_border( $tab_id, 'field_border', '.bwfac_form_sec .wffn-optin-input', __( 'Field Border' ) );

//		$this->add_heading( $tab_id, __( "Spacing", 'funnel-builder' ) );
//		$this->add_padding( $tab_id, 'column_gap_padding', '.bwfac_form_sec' );
//		$this->add_margin( $tab_id, 'column_gap_margin', '.elementor-form-fields-wrapper' );


	}


	public function html( $settings, $defaults, $content ) {
		$settings['button_border_size'] = 0;
		$wrapper_class                  = 'elementor-form-fields-wrapper';
		$show_labels                    = ( isset( $settings['show_labels'] ) && $settings['show_labels'] == 'on' );
		$wrapper_class                  .= $show_labels ? '' : ' wfop_hide_label';

		$optinPageId    = WFOPP_Core()->optin_pages->get_optin_id();
		$optin_fields   = WFOPP_Core()->optin_pages->form_builder->get_optin_layout( $optinPageId );
		$optin_settings = WFOPP_Core()->optin_pages->get_optin_form_integration_option( $optinPageId );

		foreach ( $optin_fields as $step_slug => $optinFields ) {
			foreach ( $optinFields as $key => $optin_field ) {
				$optin_fields[ $step_slug ][ $key ]['width'] = $settings[ $optin_field['InputName'] ];
			}
		}

		$custom_form = WFOPP_Core()->form_controllers->get_integration_object( 'form' );
		if ( $custom_form instanceof WFFN_Optin_Form_Controller_Custom_Form ) {
			$custom_form->_output_form( $wrapper_class, $optin_fields, $optinPageId, $optin_settings, 'inline', $settings );
		}
		?>
        <script>
            jQuery(document).trigger('wffn_reload_phone_field');
        </script>
		<?php

	}


}

new WFFN_Optin_Oxy_Form;