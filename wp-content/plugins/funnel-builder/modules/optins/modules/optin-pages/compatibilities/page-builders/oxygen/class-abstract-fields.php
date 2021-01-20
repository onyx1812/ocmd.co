<?php

abstract class WFFN_Optin_OXY_Field extends OxyEl {
	protected $get_local_slug = '';
	protected $name = '';
	public $slug = 'wfacp_checkout_form_summary';
	protected $id = 'wfacp_order_summary_widget';
	protected $settings = [];
	protected $post_id = 0;
	protected $tabs = [];
	protected $sub_tabs = [];
	protected $html_fields = [];
	private $add_tab_number = 1;

	protected $style_box = null;

	public function __construct() {
		parent::__construct();
	}

	public function init() {
		$this->El->useAJAXControls();
	}    /*
	  * used by OxyEl class to show the element button in a specific section/subsection
	  * @returns {string}
	  */
	public function button_place() {
		return 'woofunnels::woofunnels';
	}

	protected function add_tab( $title = '', $tab_type = 1, $condition = [], $instance = null ) {
		if ( empty( $title ) ) {
			$title = $this->get_title();
		}
		$field_key = 'wfacp_' . $this->add_tab_number . "_tab";
		$control   = $this->addControlSection( $field_key, $title, "assets/icon.png", $this );
		$this->add_tab_number ++;

		return $control;
	}


	public function add_heading( $control, $heading, $separator = '', $conditions = [] ) {
		$key            = $this->get_unique_id();
		$custom_control = $control->addCustomControl( __( '<div class="oxygen-option-default"  style="color: #fff; line-height: 1.3; font-size: 15px;font-weight: 900;    text-transform: uppercase;    text-decoration: underline;">' . $heading . '</div>' ), 'description' );
		$custom_control->setParam( $key, '' );
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition_string = $this->get_condition_string( $key, $conditions );
			if ( '' !== $condition_string ) {
				$custom_control->setCondition( $condition_string );
			}
		}

		return $custom_control;
	}

	public function add_sub_heading( $control, $heading, $separator = '', $conditions = [] ) {
		$key            = $this->get_unique_id();
		$custom_control = $control->addCustomControl( __( '<div class="oxygen-option-default"  style="color: #fff; line-height: 1.3; font-size: 13px;font-weight: 600;    text-transform: uppercase;    text-decoration: underline;">' . $heading . '</div>' ), 'description' );
		$custom_control->setParam( $key, '' );
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition_string = $this->get_condition_string( $key, $conditions );
			if ( '' !== $condition_string ) {
				$custom_control->setCondition( $condition_string );
			}
		}

		return $custom_control;
	}

	protected function add_switcher( $control, $key, $label = '', $default = 'off', $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = __( 'Enable', 'woofunnels-aero-checkout' );
		}
		$input = [
			"type"    => "radio",
			//'type'    => 'dropdown',
			"name"    => $label,
			"slug"    => $key,
			"value"   => [ 'on' => __( "Yes" ), "off" => __( 'No' ) ],
			"default" => $default
		];


		$condition_string = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition_string = $this->get_condition_string( $key, $conditions );
		}


		if ( '' !== $condition_string ) {
			$input['condition'] = $condition_string;
		}
		$control->addOptionControl( $input )->rebuildElementOnChange();

		return $key;
	}

	protected function add_select( $control, $key, $label, $options, $default, $conditions = [] ) {


		$input            = [
			'type'    => 'dropdown',
			'name'    => $label,
			'slug'    => $key,
			'value'   => $options,
			'default' => $default
		];
		$condition_string = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition_string = $this->get_condition_string( $key, $conditions );
		}
		if ( '' !== $condition_string ) {
			$input['condition'] = $condition_string;
		}
		$control->addOptionControl( $input )->rebuildElementOnChange();


		return $key;
	}

	public function add_text( $control, $key, $label, $default = '', $conditions = [], $description = '', $placeholder = '' ) {

		$input = array(
			'name'        => $label,
			'slug'        => $key,
			'type'        => 'textarea',
			'default'     => $default,
			'placeholder' => $placeholder,
		);


		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$control->addOptionControl( $input )->rebuildElementOnChange();

		return $key;
	}


	protected function add_textArea( $control, $key, $label, $default = '', $conditions = [] ) {
		$input = array(
			'name'    => $label,
			'slug'    => $key,
			'type'    => 'textarea',
			'default' => $default
		);


		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$control->addOptionControl( $input )->rebuildElementOnChange();

		return $key;
	}

	protected function add_typography( $control, $key, $selectors = '', $label = '', $default = '#333333', $conditions = [], $font_side_default = [] ) {

		if ( empty( $label ) ) {
			$label = __( 'Typography', 'woofunnels-aero-checkout' );
		}
		$typo = $control->typographySection( $label, $selectors, $this );


		return $typo;
	}

	protected function add_text_alignments( $tab_id, $key, $selectors = '', $label = '', $default = 'left', $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_html__( 'Alignment', 'et_builder' );
		}

		$input     = array(
			"name"     => $label,
			"slug"     => $key,
			"selector" => $selectors,
			"property" => 'text-align',
		);
		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$tab_id->addStyleControls( [ $input ] );

		return $key;
	}


	protected function add_color( $tab_id, $key, $selectors = '', $label = 'Color', $default = '#000000', $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = 'Color';
		}

		$input     = array(
			"name"     => $label,
			"slug"     => $key,
			"selector" => $selectors,
			"property" => 'color',
		);
		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$tab_id->addStyleControls( [ $input ] );


		return $key;
	}

	protected function add_background_color( $tab_id, $key, $selectors = [], $default = '#000000', $label = '', $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_attr__( 'Background', 'elementor' );
		}
		$input     = array(
			"name"     => $label,
			"selector" => $selectors,
			"slug"     => $key,
			'default'  => $default,
			"property" => 'background-color',
		);
		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$tab_id->addStyleControls( [ $input ] );


		return $key;
	}

	protected function add_border_color( $tab_id, $key, $selectors = [], $default = '#000000', $label = '', $box_shadow = false, $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_attr__( 'Border Color', 'elementor' );
		}

		$input     = array(
			"name"     => $label,
			"selector" => $selectors,
			"slug"     => $key,
			'default'  => $default,
			"property" => 'border-color',
		);
		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$tab_id->addStyleControls( [ $input ] );

		return $key;
	}

	protected function add_border_radius( $tab_id, $key, $selector, $conditions = [], $default = [], $custom_label = '' ) {
		if ( empty( $label ) ) {
			$label = esc_attr__( 'Border Radius', 'elementor' );
		}

		$input     = array(
			"name"     => $label,
			"selector" => $selector,
			"slug"     => $key,
			'default'  => $default,
			"property" => 'border-radius',
		);
		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$tab_id->addStyleControls( [ $input ] );

		return $key;
	}

	protected function add_padding( $tab_id, $key, $selector, $default = '', $label = '', $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_html__( 'Padding', 'woofunnels-aero-checkout' );
		}
		$tab_id->addPreset( "padding", $key, $label, $selector );

		return $key;
	}

	protected function add_margin( $tab_id, $key, $selector, $default = '', $label = '', $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_html__( 'Margin', 'woofunnels-aero-checkout' );
		}
		$tab_id->addPreset( "margin", $key, $label, $selector );

		return $key;
	}

	protected function add_border( $tab_id, $key, $selectors, $label = '', $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = __( "Border" );
		}
		$tab_id->borderSection( $label, $selectors, $this );

		return $key;
	}


	protected function add_box_shadow( $tab_id, $key, $selector, $label = '', $default = [], $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_attr__( 'Border Shadow', 'elementor' );
		}

		$tab_id->boxShadowSection( $label, $selector, $this );

		return $key;
	}


	protected function add_divider( $control, $type ) {
		$key = $this->get_unique_id();
		$control->addCustomControl( __( '<hr class="oxygen-option-default" style="color: #fff" />' ), 'description' )->setParam( $key, '' );

		return $key;
	}

	protected function add_width( $tab_id, $key, $selectors = '', $label = '', $default = [], $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_attr__( 'Width', 'elementor' );
		}
		$input     = array(
			"name"     => $label,
			"selector" => $selectors,
			"slug"     => $key,
			'default'  => $default['default'],
			"property" => 'width',
		);
		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$tab_id->addStyleControls( [ $input ] );


		return $key;
	}

	protected function add_min_width( $tab_id, $key, $selectors = '', $label = '', $default = [], $conditions = [] ) {
		if ( empty( $label ) ) {
			$label = esc_attr__( 'Min Width', 'elementor' );
		}
		$input     = array(
			"name"     => $label,
			"selector" => $selectors,
			"slug"     => $key,
			'default'  => $default,
			"property" => 'min-width',
		);
		$condition = '';
		if ( is_array( $conditions ) && ! empty( $conditions ) ) {
			$condition = $this->get_condition_string( $key, $conditions );
		}
		if ( '' != $condition ) {
			$input['condition'] = $condition;
		}
		$c = $tab_id->addStyleControls( [ $input ] );
		$c->setUnits( $default['unit'], "px,em,%" );


		return $key;
	}


	protected function get_class_options() {
		return [
			'wfacp-col-full'       => __( 'Full', 'woofunnels-aero-checkout' ),
			'wfacp-col-left-half'  => __( 'One Half', 'woofunnels-aero-checkout' ),
			'wfacp-col-left-third' => __( 'One Third', 'woofunnels-aero-checkout' ),
			'wfacp-col-two-third'  => __( 'Two Third', 'woofunnels-aero-checkout' ),
		];
	}

	protected function get_condition_string( $key, $condition ) {

		if ( empty( $condition ) ) {
			return '';
		}

		$output = [];
		foreach ( $condition as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ',', $value );
			}
			$output[] = $key . '=' . $value;
		}

		return implode( '&&', $output );
	}


	protected function get_unique_id() {
		static $count = 0;
		$count ++;
		$key = md5( 'wfacp_' . $count );

		return $key;
	}

	protected function get_name() {
		return $this->name;
	}

	protected function get_slug() {
		return $this->slug;
	}

	protected function get_id() {
		return $this->id;
	}

	protected function get_local_slug() {
		return $this->get_local_slug;
	}

	public function controls() {
		$this->process_checkout();
	}


	private function process_checkout() {


		global $post;
		// checking for when builder is open
		if ( $this->is_oxy_page() ) {
			$wffn_post_id = 0;
			if ( isset( $_REQUEST['oxy_wffn_optin_id'] ) ) {
				$wffn_post_id = $_REQUEST['oxy_wffn_optin_id'];
			} else if ( isset( $_REQUEST['post_id'] ) ) {
				$wffn_post_id = $_REQUEST['post_id'];
			}
			if ( $wffn_post_id > 0 ) {
				$post = get_post( $wffn_post_id );
			}

		}

		if ( is_null( $post ) || ( ! is_null( $post ) && $post->post_type !== WFOPP_Core()->optin_pages->get_post_type_slug() ) ) {
			return [];
		}
		WFOPP_Core()->optin_pages->set_id( $post->ID );
		WFOPP_Core()->optin_pages->setup_options();
		$this->setup_data();
	}


	protected function setup_data() {
	}

	public function is_oxy_page() {

		$status = true;
		// At load
		if ( isset( $_REQUEST['ct_builder'] ) ) {
			$this->is_oxy = true;
			$status       = true;

		}
		// when ajax running for form html
		if ( isset( $_REQUEST['action'] ) && ( 'set_oxygen_edit_post_lock_transient' == $_REQUEST['action'] || false !== strpos( $_REQUEST['action'], 'oxy_render_' ) || false !== strpos( $_REQUEST['action'], 'oxy_load_controls_oxy' ) ) ) {
			$this->is_oxy = true;
			$status       = true;
		}


		return $status;
	}

}