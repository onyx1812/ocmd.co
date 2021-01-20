<?php

class WFACP_OXY {
	private static $ins = null;
	private $is_oxy = false;
	private static $front_locals = [];
	private $section_slug = "woofunnels";
	private $tab_slug = "woofunnels";
	public $modules_instance = [];
	private $edit_id = 0;


	private function __construct() {

		$this->process_url();
		$this->register();
		$this->add_default_templates();
	}

	public static function get_instance() {
		if ( is_null( self::$ins ) ) {
			self::$ins = new self();
		}

		return self::$ins;

	}

	private function process_url() {
		if ( isset( $_REQUEST['page'] ) && 'wf-op' === $_REQUEST['page'] && isset( $_REQUEST['edit'] ) && $_REQUEST['edit'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->edit_id = absint( $_REQUEST['edit'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	private function register() {

		/* show a section in +Add */
		add_action( 'oxygen_add_plus_sections', [ $this, 'add_plus_sections' ] );
		add_action( 'oxygen_vsb_global_styles_tabs', [ $this, 'global_settings_tab' ] );
		add_action( "oxygen_add_plus_" . $this->section_slug . "_section_content", [ $this, 'add_plus_subsections_content' ] );
		add_action( 'admin_bar_menu', [ $this, 'add_admin_bar_link' ], 1003 );
		add_action( 'init', [ $this, 'init_extension' ], 21 );

	}

	public function add_default_templates() {

		$template = [
			'slug'        => 'oxygen',
			'title'       => __( 'Oxygen', 'funnel-builder' ),
			'button_text' => __( 'Edit', 'funnel-builder' ),
			'description' => __( 'Use Oxygen Builder modules to create your own designs. Or pick from professionally-designed templates.', 'funnel-builder' ),
			'edit_url'    => add_query_arg( [
				'ct_builder'        => true,
				'oxy_wffn_optin_id' => $this->edit_id,
			], get_permalink( $this->edit_id ) ),
		];

		WFOPP_Core()->optin_pages->register_template_type( $template );

		$designs = [
			'oxygen' => [
				'oxygen_1' => [
					'type'               => 'view',
					'import'             => 'no',
					'show_import_popup'  => 'no',
					'slug'               => 'oxygen_1',
					'build_from_scratch' => true,
					'group'              => array( 'inline', 'popup' ),
				],
			],
		];
		foreach ( $designs as $d_key => $templates ) {

			if ( is_array( $templates ) ) {
				foreach ( $templates as $temp_key => $temp_val ) {
					if ( isset( $temp_val['pro'] ) && 'yes' === $temp_val['pro'] ) {
						$temp_val['license_exist'] = WFFN_Core()->admin->get_license_status();
					}
					WFOPP_Core()->optin_pages->register_template( $temp_key, $temp_val, $d_key );
				}
			}
		}

		return [];
	}

	public static function set_locals( $name, $id ) {
		self::$front_locals[ $name ] = $id;
	}

	public static function get_locals() {
		return self::$front_locals;

	}



	public function add_plus_sections() {
		/* show a section in +Add dropdown menu and name it "My Custom Elements" */
		CT_Toolbar::oxygen_add_plus_accordion_section( $this->section_slug, __( "WooFunnels", 'woofunnels-aero-checkout' ) );
	}

	public function global_settings_tab() {
		global $oxygen_toolbar;
		$oxygen_toolbar->settings_tab( __( "WooFunnels", 'woofunnels-aero-checkout' ), $this->tab_slug, "panelsection-icons/styles.svg" );
	}

	public function add_plus_subsections_content() {
		do_action( "oxygen_add_plus_woofunnels_woofunnels" );
	}


	public function init_extension() {

		$post_id = 0;
		if ( isset( $_REQUEST['post_id'] ) && $_REQUEST['post_id'] > 0 ) {
			$post_id = absint( $_REQUEST['post_id'] );
		} elseif ( isset( $_REQUEST['oxy_wffn_optin_id'] ) ) {
			$post_id = absint( $_REQUEST['oxy_wffn_optin_id'] );
		}

		$post = get_post( $post_id );
		if ( ! is_null( $post ) && $post->post_type === $post_type = WFOPP_Core()->optin_pages->get_post_type_slug() ) {
			WFOPP_Core()->optin_pages->set_id( $post_id );
			$this->prepare_module();

			return;
		}

		add_action( 'wfacp_after_template_found', [ $this, 'prepare_module' ] );


	}

	public function prepare_module() {
		$id     = WFOPP_Core()->optin_pages->get_id();
		$design = WFOPP_Core()->optin_pages->get_page_design( $id );
		if ( 'oxygen' !== $design['selected_type'] ) {
			return;
		}
		$modules = $this->get_modules();
		if ( ! empty( $modules ) ) {
			include __DIR__ . '/class-abstract-fields.php';
			include __DIR__ . '/class-html-block-oxy.php';
			foreach ( $modules as $key => $module ) {
				if ( ! file_exists( $module['path'] ) ) {
					continue;
				}
				$this->modules_instance[ $key ] = include $module['path'];
			}
		}
	}


	private function get_modules() {
		$modules = [
			'checkout_form' => [
				'name' => __( 'Checkout Form', 'woofunnels-aero-checkout' ),
				'path' => __DIR__ . '/modules/class-oxy-optin-form.php',
			],
//			'mini_cart'     => [
//				'name' => __( 'Mini Cart', 'woofunnels-aero-checkout' ),
//				'path' => __DIR__ . '/modules/class-oxy-optin-form.php',
//			]

		];

		return apply_filters( 'wfacp_oxy_modules', $modules, $this );
	}


	public function add_admin_bar_link() {
		/**
		 * @var $wp_admin_bar WP_Admin_Bar;
		 */ global $wp_admin_bar;

		if ( ! is_null( $wp_admin_bar ) ) {

			$node = $wp_admin_bar->get_node( 'edit_post_template' );
			if ( ! is_null( $node ) ) {
				$node = (array) $node;
				global $post;
				if ( ! is_null( $post ) ) {
					$wfacp_id     = $post->ID;
					$href         = $node['href'];
					$node['href'] = add_query_arg( [ 'ct_builder' => 'true', 'oxy_wffn_optin_id' => $wfacp_id ], $href );
					$wp_admin_bar->add_node( $node );
				}
			}
		}
	}

}

WFACP_OXY::get_instance();
