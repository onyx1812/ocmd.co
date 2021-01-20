<?php

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Funnel optin page module
 * Class WFFN_Optin_Pages
 */
class WFFN_Optin_Pages extends WFFN_Module_Common {

	private static $ins = null;
	/**
	 * @var WFOP_Admin|null
	 */
	public $admin;
	protected $options;
	protected $custom_options;
	protected $optin_form_option;
	protected $action_options;
	protected $template_type = [];
	protected $design_template_data = [];
	protected $templates = [];
	protected $wfop_is_optin = false;
	public $edit_id = 0;
	public $form_builder;
	public $optin_integration_option;
	public $url = '';
	const WFOP_EMAIL_FIELD_SLUG = 'optin_email';
	const WFOP_FIRST_NAME_FIELD_SLUG = 'optin_first_name';
	const WFOP_LAST_NAME_FIELD_SLUG = 'optin_last_name';
	const WFOP_PHONE_FIELD_SLUG = 'optin_phone';
	const FIELD_PREFIX = 'wfop_';

	/**
	 * WFFN_Optin_Pages constructor.
	 */
	public function __construct() {

		parent::__construct();
		$this->url = plugin_dir_url( __FILE__ );
		$this->process_url();

		include_once __DIR__ . '/class-wfop-admin.php';
		$this->admin = WFOP_Admin::get_instance();
		include_once __DIR__ . '/includes/class-wffn-ecomm-tracking-optin.php';
		$this->ecom_tracking = WFFN_Ecomm_Tracking_Optin::get_instance();

		include_once __DIR__ . '/class-wffn-form-builder.php';
		$this->form_builder = WFFN_Form_Builder::get_instance();
		add_action( 'init', array( $this, 'maybe_handle_preview' ), - 1 );
		add_action( 'init', array( $this, 'register_post_type' ), 100 );

		add_action( 'wffn_admin_assets', [ $this, 'load_assets' ] );
		add_filter( 'template_include', [ $this, 'may_be_change_template' ], 98 );

		$post_type = $this->get_post_type_slug();
		add_filter( "theme_{$post_type}_templates", [ $this, 'add_elementor_page_templates' ], 99, 4 );

		add_action( 'wp', array( $this, 'parse_request_for_optin' ), - 1 );

		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_register_breadcrumb_nodes' ), 88 );
		add_action( 'wp_ajax_wffn_op_global_settings_update', array( $this, 'update_global_settings' ) );
		add_action( 'wp_ajax_wffn_op_custom_settings_update', array( $this, 'update_custom_settings' ) );
		add_action( 'wp_ajax_wffn_optin_get_form_list', array( $this, 'optin_get_form_list' ) );

		add_action( 'wp_ajax_wffn_op_save_design', [ $this, 'save_design' ] );
		add_action( 'wp_ajax_wffn_op_remove_design', [ $this, 'remove_design' ] );
		add_action( 'wp_ajax_wffn_op_import_template', [ $this, 'import_template' ] );
		add_action( 'wp_ajax_wffn_op_toggle_state', [ $this, 'toggle_state' ] );
		add_action( 'wp_ajax_wffn_update_optin_page', [ $this, 'update_optin_page' ] );
		add_action( 'wp_ajax_wffn_create_a_new_optin_form', [ $this, 'create_new_optin_form' ] );
		add_action( 'wp_ajax_wffn_op_actions_settings_update', array( $this, 'update_actions_settings' ) );
		add_action( 'wp_ajax_wffn_op_page_search', array( $this, 'page_search' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'optin_page_frontend_scripts' ), 21 );
		add_action( 'wffn_import_completed', array( $this, 'set_page_template' ), 10, 2 );

		add_filter( 'post_type_link', array( $this, 'post_type_permalinks' ), 10, 3 );
		add_action( 'pre_get_posts', array( $this, 'add_cpt_post_names_to_main_query' ), 20 );
		add_filter( 'bwf_child_entities', array( $this, 'add_optin_as_contact_child' ), 10, 1 );
		add_filter( 'wffn_assets_styles', [ $this, 'add_optin_fronted_style' ], 10, 1 );

		add_action( 'wp_ajax_wffn_wfop_form_save', array( $this, 'form_save' ) );
		add_filter( 'wffn_localized_data', array( $this, 'maybe_add_js_localized' ) );
		add_action( 'wp_ajax_wffn_course_search', array( $this, 'course_search' ) );

		add_action( 'wp_ajax_wfop_save_layout', [ $this, 'save_layout' ] );
		add_action( 'wp_ajax_wfop_add_field', [ $this, 'add_field' ] );
		add_action( 'wp_ajax_wfop_delete_custom_field', [ $this, 'delete_custom_field' ] );
		add_action( 'wp_ajax_wfop_update_custom_field', [ $this, 'update_custom_field' ] );

		add_action( 'plugins_loaded', [ $this, 'include_compatibility_files' ], 12 );
		add_action( 'wp_print_scripts', array( $this, 'no_conflict_mode_script' ), 1000 );
		add_action( 'admin_print_footer_scripts', array( $this, 'no_conflict_mode_script' ), 9 );
	}

	private function process_url() {
		if ( isset( $_REQUEST['page'] ) && 'wf-op' === $_REQUEST['page'] && isset( $_REQUEST['edit'] ) && $_REQUEST['edit'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->edit_id = absint( $_REQUEST['edit'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		if ( isset( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && isset( $_REQUEST['post'] ) && $_REQUEST['post'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->edit_id = absint( $_REQUEST['post'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	public function register_native_templates() {
		$template = [
			'slug'        => 'wp_editor',
			'title'       => __( 'Custom', 'funnel-builder' ),
			'button_text' => __( 'Edit', 'funnel-builder' ),
			'edit_url'    => add_query_arg( [
				'post'   => $this->get_edit_id(),
				'action' => 'edit',
			], admin_url( 'post.php' ) ),
		];
		$this->register_template_type( $template );
		$designs = [
			'wp_editor' => [
				'wp_editor_1' => [
					'name'               => __( 'Build from scratch', 'funnel-builder' ),
					'type'               => 'view',
					'import'             => 'no',
					'show_import_popup'  => 'no',
					'show_shortcodes'    => 'yes',
					'import_button_text' => __( 'Apply', 'funnel-builder' ),
					'slug'               => 'wp_editor_1',
					'build_from_scratch' => true,
					'group'              => [ 'inline', 'popup' ]
				],
			],
		];
		foreach ( $designs as $d_key => $templates ) {

			if ( is_array( $templates ) ) {
				foreach ( $templates as $temp_key => $temp_val ) {
					$this->register_template( $temp_key, $temp_val, $d_key );
				}
			}
		}
	}

	public function get_post_type_slug() {
		return 'wffn_optin';
	}

	/**
	 * @return WFFN_Optin_Pages|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function register_template_type( $data ) {

		if ( isset( $data['slug'] ) && ! empty( $data['slug'] ) && isset( $data['title'] ) && ! empty( $data['title'] ) ) {
			$slug  = sanitize_title( $data['slug'] );
			$title = esc_html( trim( $data['title'] ) );
			if ( ! isset( $this->template_type[ $slug ] ) ) {
				$this->template_type[ $slug ]        = trim( $title );
				$this->design_template_data[ $slug ] = [
					'edit_url'    => $data['edit_url'],
					'button_text' => $data['button_text'],
					'title'       => $data['title'],
					'description' => isset( $data['description'] ) ? $data['description'] : '',
				];
			}
		}
	}

	public function register_template( $slug, $data, $type = 'pre_built' ) {
		if ( '' !== $slug && ! empty( $data ) ) {
			$this->templates[ $type ][ $slug ] = $data;
		}
	}

	public function register_post_type() {
		/**
		 * Optin page Post Type
		 */
		$bwb_admin_setting = BWF_Admin_General_Settings::get_instance();

		register_post_type( $this->get_post_type_slug(), apply_filters( 'wffn_optin_post_type_args', array(
			'labels'              => array(
				'name'          => $this->get_module_title( true ),
				'singular_name' => $this->get_module_title(),
				'add_new'       => sprintf( __( 'Add %s', 'funnel-builder' ), $this->get_module_title() ),
				'add_new_item'  => sprintf( __( 'Add New %s', 'funnel-builder' ), $this->get_module_title() ),
				'search_items'  => sprintf( esc_html__( 'Search %s', 'funnel-builder' ), $this->get_module_title( true ) ),
				'all_items'     => sprintf( esc_html__( 'All %s', 'funnel-builder' ), $this->get_module_title( true ) ),
				'edit_item'     => sprintf( esc_html__( 'Edit %s', 'funnel-builder' ), $this->get_module_title() ),
				'view_item'     => sprintf( esc_html__( 'View %s', 'funnel-builder' ), $this->get_module_title() ),
				'update_item'   => sprintf( esc_html__( 'Update %s', 'funnel-builder' ), $this->get_module_title() ),
				'new_item_name' => sprintf( esc_html__( 'New %s', 'funnel-builder' ), $this->get_module_title() ),
			),
			'public'              => true,
			'show_ui'             => true,
			'map_meta_cap'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_in_menu'        => false,
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => array(
				'slug'       => ( empty( $bwb_admin_setting->get_option( 'optin_page_base' ) ) ? $this->get_post_type_slug() : $bwb_admin_setting->get_option( 'optin_page_base' ) ),
				'with_front' => false,
			),
			'capabilities'        => array(
				'create_posts' => 'do_not_allow', // Prior to Wordpress 4.5, this was false.
			),
			'query_var'           => true,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'elementor', 'editor' ),
			'has_archive'         => false,
		) ) );
	}

	public function optin_page_frontend_scripts() {

		if ( $this->is_wfop_page() ) {
			$suffix = '.min';

			if ( defined( 'WFFN_IS_DEV' ) && true === WFFN_IS_DEV ) {
				$suffix = '';
			}
			wp_enqueue_style( 'wffn-optin-frontend-style' );
			wp_register_script( 'wffn-optin-public', $this->url . 'assets/js/public' . $suffix . '.js', 'jquery', WFFN_VERSION );
			wp_enqueue_script( 'wffn-optin-public' );
			global $post;
			$page_template = ( $post instanceof WP_Post ) ? get_post_meta( $post->ID, '_wp_page_template', true ) : '';
			if ( 'default' === $page_template || empty( $page_template ) ) {
				return;
			}
			wp_enqueue_style( 'wffn-frontend-style' );

		}
	}

	/**
	 * Checks whether its our page or not
	 * @return bool
	 */
	public function is_wfop_page() {
		return $this->wfop_is_optin;
	}

	/**
	 * Set wfty_is_thankyou flag if it's our page
	 * @return void
	 */
	public function parse_request_for_optin() {
		global $post;

		if ( is_null( $post ) || ! $post instanceof WP_Post ) {
			return;
		}

		$funnel = WFFN_Core()->data->get_session_funnel();

		if ( is_singular( $post->post_type ) && ( $this->get_post_type_slug() === $post->post_type ) ) {

			if ( wffn_is_valid_funnel( $funnel ) ) {
				WFFN_Core()->logger->log( "Funnel id: #" . $funnel->id . " parse request for optin" );
			}

			$this->wfop_is_optin = true;
		}
	}

	public function get_option( $key = 'all' ) {

		if ( null === $this->options ) {
			$this->setup_options();
		}
		if ( 'all' === $key ) {
			return $this->options;
		}

		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : false;
	}

	public function get_custom_option( $key = 'all' ) {

		if ( null === $this->custom_options ) {
			$this->setup_custom_options();
		}
		if ( 'all' === $key ) {
			return $this->custom_options;
		}

		return isset( $this->custom_options[ $key ] ) ? $this->custom_options[ $key ] : false;
	}

	public function get_action_option( $key = 'all' ) {

		if ( null === $this->action_options ) {
			$this->setup_action_options();
		}
		if ( 'all' === $key ) {
			return $this->action_options;
		}

		return isset( $this->action_options[ $key ] ) ? $this->action_options[ $key ] : false;
	}

	public function setup_action_options() {
		$optin_id             = isset( $this->edit_id ) ? $this->edit_id : 0;
		$db_actions           = get_post_meta( $optin_id, 'wffn_actions_custom_settings', true );
		$db_actions           = ( ! empty( $db_actions ) && is_array( $db_actions ) ) ? $db_actions : array();
		$this->action_options = wp_parse_args( $db_actions, $this->default_actions_settings() );

		return $this->action_options;
	}

	public function get_optin_form_integration_option( $optin_page_id = 0, $key = 'all' ) {

		if ( null === $this->optin_integration_option ) {
			$this->setup_optin_form_integration_options( $optin_page_id );
		}
		if ( 'all' === $key ) {
			return $this->optin_integration_option;
		}

		return isset( $this->optin_integration_option[ $key ] ) ? $this->optin_integration_option[ $key ] : false;
	}

	/**
	 * @param $optin_page_id
	 *
	 * @return array
	 */
	public function setup_optin_form_integration_options( $optin_page_id ) {
		$optin_id   = ( $optin_page_id > 0 ) ? $optin_page_id : ( isset( $this->edit_id ) ? $this->edit_id : 0 );
		$db_options = get_post_meta( $optin_id, 'wffn_actions_custom_settings', true );
		$db_options = isset( $db_options['optin_service_form'] ) ? $db_options['optin_service_form'] : [];

		$db_options                     = ( ! empty( $db_options ) && is_array( $db_options ) ) ? $db_options : array();
		$this->optin_integration_option = wp_parse_args( $db_options, $this->default_optin_integration_settings() );

		return $this->optin_integration_option;
	}

	public function setup_options() {
		$db_options    = get_option( 'wffn_op_settings', [] );
		$db_options    = ( ! empty( $db_options ) && is_array( $db_options ) ) ? array_map( 'html_entity_decode', $db_options ) : array();
		$this->options = wp_parse_args( $db_options, $this->default_global_settings() );

		return $this->options;
	}


	public function default_global_settings() {
		$user = WFFN_Common::admin_user();

		return array(
			'op_user_name'        => $user->first_name . ' ' . $user->last_name,
			'op_user_email'       => $user->user_email,
			'op_user_email_reply' => $user->user_email,
			'op_recaptcha_msg'    => __( 'We are unable to sign you in due to some security issues', 'funnel-builder' ),
			'css'                 => '',
			'script'              => '',
			'ty_css'              => '',
			'ty_script'           => '',
			'op_recaptcha'        => 'false',
			'op_recaptcha_secret' => '',
		);
	}

	public function default_custom_settings() {
		return array(
			'custom_css'      => '',
			'custom_js'       => '',
			'op_valid_enable' => 'true',
			'op_valid_text'   => __( 'This is a required field.', 'funnel-builder' ),
			'op_valid_email'  => __( 'Enter a valid email address.', 'funnel-builder' ),
			'custom_redirect' => 'false',
		);
	}

	public function default_actions_settings() {
		$user = WFFN_Common::admin_user();

		return array(
			'user_login'                => 'true',
			'optin_service_form'        => [],
			'lms_course'                => 'false',
			'assign_ld_course'          => '',
			'test_email'                => '',
			'lead_enable_notify'        => 'false',
			'lead_notification_subject' => __( 'Thankyou For taking interest', 'funnel-builder' ),
			'lead_notification_body'    => __( 'Hello [bwf_contact_first_name default="there"], thankyou for your submission. As promised here is your free ebook download link. <a href="#">Download Now</a>', 'funnel-builder' ),
			'admin_email_notify'        => 'false',
			'op_admin_email'            => $user->user_email,
		);
	}

	public function default_optin_integration_settings() {

		return array(
			'optin_form_enable' => 'false',
			'formBuilder'       => '',
			'fields'            => [],
			'formFields'        => [],
			'html_code'         => '',
		);
	}

	public function get_optin_form_groups() {
		return array(
			'form_builders'   => array(
				'id'    => 'form_builders',
				'title' => __( 'Form Builders', 'funnel-builder' )
			),
			'page_builders'   => array(
				'id'    => 'page_builders',
				'title' => __( 'Page Builders', 'funnel-builder' )
			),
			'auto_responders' => array(
				'id'    => 'auto_responders',
				'title' => __( 'Auto Responders', 'funnel-builder' )
			),
			'custom_form'     => array(
				'id'    => 'custom_form',
				'title' => __( 'Custom Form', 'funnel-builder' )
			),
		);
	}

	/**
	 * Copy data from old checkout page to new checkout page
	 *
	 * @param $post_id
	 *
	 * @return int|null|WP_Error
	 */
	public function duplicate_optin_page( $optin_page_id ) {

		$exclude_metas = array(
			'cartflows_imported_step',
			'enable-to-import',
			'site-sidebar-layout',
			'site-content-layout',
			'theme-transparent-header-meta',
			'_uabb_lite_converted',
			'_astra_content_layout_flag',
			'site-post-title',
			'ast-title-bar-display',
			'ast-featured-img',
			'_thumbnail_id',
		);

		if ( $optin_page_id > 0 ) {
			$optin_page = get_post( $optin_page_id );
			if ( ! is_null( $optin_page ) && ( $optin_page->post_type === $this->get_post_type_slug() || in_array( $optin_page->post_type, $this->get_inherit_supported_post_type(), true ) ) ) {
				$args         = [
					'post_title'   => $optin_page->post_title . ' - ' . __( 'Copy', 'funnel-builder' ),
					'post_content' => $optin_page->post_content,
					'post_name'    => sanitize_title( $optin_page->post_title . ' - ' . __( 'Copy', 'funnel-builder' ) ),
					'post_type'    => $this->get_post_type_slug(),
				];
				$duplicate_id = wp_insert_post( $args );
				if ( ! is_wp_error( $duplicate_id ) ) {

					global $wpdb;

					$post_meta_all = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$optin_page_id" ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

					if ( ! empty( $post_meta_all ) ) {
						$sql_query_selects = [];
						$sql_query_meta    = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

						if ( in_array( $optin_page->post_type, $this->get_inherit_supported_post_type(), true ) ) {

							foreach ( $post_meta_all as $meta_info ) {

								$meta_key = $meta_info->meta_key;

								if ( in_array( $meta_key, $exclude_metas, true ) ) {
									continue;
								}
								if ( strpos( $meta_key, 'wcf-' ) !== false ) {
									continue;
								}
								if ( strpos( $meta_key, '_oembed' ) !== false ) {
									continue;
								}

								/**
								 * Good to remove slashes before adding
								 */
								$meta_value = addslashes( $meta_info->meta_value );
								if ( $meta_key === '_wp_page_template' ) {
									$meta_value = ( strpos( $meta_value, 'cartflows' ) !== false ) ? str_replace( 'cartflows', "wfop", $meta_value ) : $meta_value;
								}


								$sql_query_selects[] = "SELECT $duplicate_id, '$meta_key', '$meta_value'"; //db call ok; no-cache ok; WPCS: unprepared SQL ok.


							}
						} else {

							foreach ( $post_meta_all as $meta_info ) {

								$meta_key = $meta_info->meta_key;
								/**
								 * Good to remove slashes before adding
								 */
								$meta_value = addslashes( $meta_info->meta_value );


								$sql_query_selects[] = "SELECT $duplicate_id, '$meta_key', '$meta_value'"; //db call ok; no-cache ok; WPCS: unprepared SQL ok.
							}
						}

						$sql_query_meta .= implode( ' UNION ALL ', $sql_query_selects ); //db call ok; no-cache ok; WPCS: unprepared SQL ok.

						$wpdb->query( $sql_query_meta ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared


						if ( in_array( $optin_page->post_type, $this->get_inherit_supported_post_type(), true ) ) {
							$template = [
								'selected'        => 'wp_editor_1',
								'selected_type'   => 'wp_editor',
								'template_active' => 'yes'
							];
							update_post_meta( $duplicate_id, '_wfop_selected_design', $template );
						}
						do_action( 'wffn_step_duplicated', $duplicate_id );
					}

					return $duplicate_id;
				}
			}
		}

		return 0;
	}

	/**
	 * @return array
	 */
	public function get_optin_pages( $term ) {
		$args = array(
			'post_type'   => array( $this->get_post_type_slug(), 'cartflows_step', 'page' ),
			'post_status' => 'any',
		);
		if ( ! empty( $term ) ) {
			if ( is_numeric( $term ) ) {
				$args['p'] = $term;
			} else {
				$args['s'] = $term;
			}
		}
		$query_result = new WP_Query( $args );

		if ( $query_result->have_posts() ) {
			return $query_result->posts;
		}

		return array();
	}

	public function load_assets() {
		$page_now = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		if ( 'wf-op' === $page_now ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_editor();
			wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script( 'jquery-ui-sortable' );

			wp_enqueue_style( 'wffn_op_admin', $this->url . 'assets/css/admin.css', [], time() );
			$section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
			if ( 'customize' === $section ) {
				wp_enqueue_style( 'wffn_op_frontend', $this->url . 'assets/css/wfopp-optin-frontend.css', [], time() );
			}

			wp_enqueue_script( 'wffn_op_global_js', $this->url . 'assets/js/global.js', [], time() );
			wp_enqueue_script( 'wffn_op_js', $this->url . 'assets/js/admin.js', [ 'wffn_op_global_js' ], time() );

			/**
			 * deregister this script as its in the conflict with the vue JS
			 */
			if ( WFFN_Core()->admin->is_wffn_flex_page( 'wf-op' ) ) {
				wp_dequeue_script( 'backbone-marionette' );
				wp_deregister_script( 'backbone-marionette' );
			}
			wp_localize_script( 'wffn_op_global_js', 'wfop', $this->localize_data() );
			wp_localize_script( 'wffn_op_global_js', 'wfop_localization', $this->localize_text_data() );
			wp_localize_script( 'wffn_op_global_js', 'wfop_action', $this->localize_action_data() );
			wp_localize_script( 'wffn_op_global_js', 'wfop_secure', [
				'nonce' => wp_create_nonce( 'wfop_secure_key' ),
			] );
			foreach ( WFOPP_Core()->form_fields->get_supported_form_fields_controller() as $field ) {
				$field->load_scripts();
				$field->load_style();
			}
		}
	}

	public function get_template_filter() {
		return [
			'inline' => __( 'Inline', 'funnel-builder' ),
			'popup'  => __( 'Popup', 'funnel-builder' ),
		];
	}

	public function localize_data() {
		$data                                = [];
		$design                              = [];
		$data['nonce_save_design']           = wp_create_nonce( 'wffn_op_save_design' );
		$data['nonce_remove_design']         = wp_create_nonce( 'wffn_op_remove_design' );
		$data['nonce_import_design']         = wp_create_nonce( 'wffn_op_import_design' );
		$data['nonce_global_settings']       = wp_create_nonce( 'wffn_op_global_settings_update' );
		$data['nonce_custom_settings']       = wp_create_nonce( 'wffn_op_custom_settings_update' );
		$data['nonce_optin_get_form_list']   = wp_create_nonce( 'wffn_optin_get_form_list' );
		$data['nonce_optin_get_form_field']  = wp_create_nonce( 'wffn_optin_get_form_field' );
		$data['nonce_toggle_state']          = wp_create_nonce( 'wffn_op_toggle_state' );
		$data['nonce_page_search']           = wp_create_nonce( 'wffn_op_page_search' );
		$data['wfop_edit_nonce']             = wp_create_nonce( 'wfop_edit_optin' );
		$data['wfop_create_new_form_nonce']  = wp_create_nonce( 'wfop_create_new_from' );
		$data['design_template_data']        = $this->design_template_data;
		$data['options']                     = $this->get_option();
		$data['custom_options']              = $this->get_custom_option();
		$data['optin_form_option']           = $this->get_optin_form_integration_option();
		$data['optin_customization_options'] = $this->form_builder->get_form_customization_option( 'all', $this->edit_id );
		$data['layout']                      = $this->get_layout();
		$data['filters']                     = $this->get_template_filter();
		$data['currentStepsFilter']          = 'inline';
		$data['is_wffn_pro_active']          = WFFN_Common::wffn_is_funnel_pro_active();
		$data['upgrade_button_text']         = __( 'Unlock the Pro Version', 'funnel-builder' ) . '&nbsp;<i class="dashicons dashicons-arrow-right-alt"></i>';
		$data['pro_info_title']              = __( 'This is pro feature!', 'funnel-builder' );
		$data['pro_info_lock_icon']          = '<i class="dashicons dashicons-lock"></i>';
		$data['pro_info_subtitle']           = __( 'Unlock this Pro feature now and experience the fully-loaded version.', 'funnel-builder' );
		$data['pro_info_title_popover']      = __( 'Want to show the optin form in popup?', 'funnel-builder' );
		$data['pro_info_subtitle_popover']   = __( 'Get a stylish popup form with this pro feature.', 'funnel-builder' );
		$data['pro_info_title_new_field']    = __( 'Want to add new fields to your optin form?', 'funnel-builder' );
		$data['pro_info_subtitle_new_field'] = __( 'Choose from radio, HTML, password field, and more with this pro feature.', 'funnel-builder' );
		$data['pro_info_title_phone']        = __( 'Want to add phone fields to your optin form?', 'funnel-builder' );
		$data['pro_info_subtitle_phone']     = __( 'Choose from different country phone code and enter the phone number.', 'funnel-builder' );

		$data['texts']         = array(
			'settings_success'       => __( 'Changes saved', 'funnel-builder' ),
			'copy_success'           => __( 'Link copied!', 'funnel-builder' ),
			'shortcode_copy_success' => __( 'Shortcode Copied!', 'funnel-builder' ),
			'html_err'               => __( 'Error: Unable to find valid form HTML', 'funnel-builder' ),
		);
		$data['update_popups'] = array(
			'label_texts' => array(
				'title' => array(
					'label'       => __( 'Name', 'funnel-builder' ),
					'placeholder' => __( 'Enter Name', 'funnel-builder' ),
				),
				'slug'  => array(
					'label'       => sprintf( __( '%s URL Slug', 'funnel-builder' ), $this->get_module_title() ),
					'placeholder' => __( 'Enter Slug', 'funnel-builder' ),
				),
			),
		);

		$data['email_hint'] = __( 'Enter a valid email address', 'funnel-builder' );

		$data['global_setting_fields'] = array(
			'legends_texts' => array(
				'global_css'         => __( 'Custom CSS', 'funnel-builder' ),
				'global_script'      => __( 'External Scripts', 'funnel-builder' ),
				'notify'             => __( 'Notifications', 'funnel-builder' ),
				'op_recaptcha_label' => __( 'Spam Protection', 'funnel-builder' ),
			),
			'fields'        => array(
				'css'                 => array(
					'label' => __( 'Custom CSS Tweaks', 'funnel-builder' ),
				),
				'ty_css'              => array(
					'label' => __( 'Optin Thank You Custom CSS Tweaks', 'funnel-builder' ),
				),
				'script'              => array(
					'label' => __( 'External JS Scripts', 'funnel-builder' ),
					'hint'  => __( 'These scripts will be globally embedded in all the optin Pages.', 'funnel-builder' )
				),
				'ty_script'           => array(
					'label' => __( 'Optin Thank You External JS Scripts', 'funnel-builder' ),
					'hint'  => __( 'These scripts will be globally embedded in all the optin thank you Pages.', 'funnel-builder' )
				),
				'op_recaptcha'        => array(
					'label' => __( 'Invisible reCAPTCHA v2', 'funnel-builder' ),
					'hint'  => __( 'Generating Google v2 Invisible reCAPTCHA Site and Secret Key <a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank">here</a>', 'funnel-builder' ),
				),
				'op_recaptcha_site'   => array(
					'label' => __( 'Site Key', 'funnel-builder' ),
				),
				'op_recaptcha_secret' => array(
					'label' => __( 'Secret Key', 'funnel-builder' ),
				),
				'op_recaptcha_msg'    => array(
					'label' => __( 'Fail Message', 'funnel-builder' ),
					'hint'  => __( 'The message displayed to users who fail the reCAPTCHA verification process.', 'funnel-builder' ),
				),
				'admin_email_notify'  => array(
					'label' => __( 'Admin Notification', 'funnel-builder' ),
				),
				'op_admin_email'      => array(
					'label'       => __( 'Email', 'funnel-builder' ),
					'placeholder' => __( 'Email Address', 'funnel-builder' ),
					'hint'        => __( 'Email address from the email will be sent from.', 'funnel-builder' ),
				),
				'op_user_name'        => array(
					'label'       => __( '"From" Name', 'funnel-builder' ),
					'placeholder' => __( 'Name', 'funnel-builder' ),
					'hint'        => __( 'Name that will appear in email sent.', 'funnel-builder' ),
				),
				'op_user_email'       => array(
					'label'       => __( '"From" Address', 'funnel-builder' ),
					'placeholder' => __( 'Email Address', 'funnel-builder' ),
					'hint'        => __( "Email address where user's reply will be sent to.", 'funnel-builder' ),
				),
				'op_user_email_reply' => array(
					'label'       => __( '"Reply To" Address', 'funnel-builder' ),
					'placeholder' => __( 'Email', 'funnel-builder' ),
					'hint'        => __( "Email address where user's reply will be sent to.", 'funnel-builder' ),
				),
			),
		);
		$data['add_new_field_default'] = array(
			'type'      => 'text',
			'required'  => true,
			'label'     => 'NoTitle',
			'InputName' => 'no_title',
			'default'   => '',
		);
		$data['custom_form']           = $this->form_builder->get_form_fields( $this->edit_id );

		$data['field_alert']  = array(
			'confirm_button' => __( 'Yes, remove this field!', 'funnel-builder' ),
			'cancel_button'  => __( 'Cancel', 'funnel-builder' ),
			'title'          => __( 'Are you sure you want to delete this field?', 'funnel-builder' ),
		);
		$data['radio_fields'] = array(
			array(
				'value' => 'true',
				'name'  => __( 'Yes', 'funnel-builder' ),
			),
			array(
				'value' => 'false',
				'name'  => __( 'No', 'funnel-builder' ),
			),
		);


		$data['custom_options']['pages']     = [];
		$data['custom_options']['not_found'] = __( 'Oops! No elements found. Consider changing the search query.', 'funnel-builder' );
		$data['prefix']                      = WFFN_Optin_Pages::FIELD_PREFIX;
		$data['custom_setting_fields']       = array(
			'legends_texts' => array(
				'custom_css'           => __( 'Custom CSS', 'funnel-builder' ),
				'custom_js'            => __( 'External Scripts', 'funnel-builder' ),
				'lead_notification'    => __( 'Email Notification', 'funnel-builder' ),
				'op_valid_field_label' => __( 'Validation', 'funnel-builder' ),
				'custom_redirect'      => __( 'Custom Redirection', 'funnel-builder' ),
			),
			'fields'        => array(
				'custom_css'           => array(
					'label' => __( 'Custom CSS Tweaks', 'funnel-builder' ),
				),
				'custom_js'            => array(
					'label' => __( 'Custom JS Tweaks', 'funnel-builder' ),
				),
				'op_valid_enable'      => array(
					'label' => __( 'Show Validation Message', 'funnel-builder' ),
				),
				'op_valid_text'        => array(
					'label' => __( 'Required', 'funnel-builder' ),
					'hint'  => __( '', 'funnel-builder' ),
				),
				'op_valid_email'       => array(
					'label' => __( 'Email', 'funnel-builder' ),
					'hint'  => __( '', 'funnel-builder' ),
				),
				'custom_redirect'      => array(
					'label' => __( 'Custom Redirection', 'funnel-builder' ),
				),
				'custom_redirect_page' => array(
					'label' => __( 'Select Page', 'funnel-builder' ),
				),
				'search_hint'          => __( 'Enter minimum 3 letters.', 'funnel-builder' ),
			),
		);

		$data['field_map_table'] = array(
			'label_head' => __( 'Form', 'funnel-builder' ),
			'field_head' => __( 'CRM', 'funnel-builder' ),
		);

		$form_fields = WFOPP_Core()->form_fields->get_supported_form_fields_controller();

		$fields = [];
		foreach ( $form_fields as $form_field ) {
			$fields[ $form_field->get_slug() ] = $form_field->get_editor_data();
		}

		$data['optin_field_types'] = $fields;

		if ( $this->edit_id > 0 ) {
			$post = get_post( $this->edit_id );

			$data['id']                   = $this->edit_id;
			$data['title']                = $post->post_title;
			$data['op_title']             = $this->get_module_title();
			$data['status']               = $post->post_status;
			$data['content']              = $post->post_content;
			$data['view_url']             = get_the_permalink( $this->edit_id );
			$data['embedMode']            = $this->get_embed_mode();
			$data['design_template_data'] = $this->design_template_data;
			$design                       = $this->get_page_design( $this->edit_id );
			$data['form_field_width']     = $this->get_form_field_width();


			$data['update_popups']['values'] = array(
				'title' => $post->post_title,
				'slug'  => $post->post_name,
			);

			if ( isset( $_GET['section'] ) && $_GET['section'] === 'design' ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$data['optin_form'] = "yes";
			}
		}

		$design = array_merge( [
			'designs'         => $this->templates,
			'design_types'    => $this->template_type,
			'template_active' => "yes"
		], $design, $data );

		return $design;
	}


	public function localize_action_data() {
		$data                              = [];
		$data['nonce_actions_settings']    = wp_create_nonce( 'wffn_op_actions_settings_update' );
		$data['nonce_form_preview']        = wp_create_nonce( 'wffn_wfop_show_preview' );
		$data['nonce_form_save']           = wp_create_nonce( 'wffn_wfop_form_save' );
		$data['view_url']                  = get_the_permalink( $this->edit_id );
		$data['nonce_course_search']       = wp_create_nonce( 'wffn_course_search' );
		$data['action_options']            = $this->get_action_option();
		$data['action_options']['courses'] = [];


		$data['action_fileld']['radio_fields'] = [
			[
				'value' => 'true',
				'name'  => __( 'Yes', 'funnel-builder' ),
			],
			[
				'value' => 'false',
				'name'  => __( 'No', 'funnel-builder' ),
			],
		];

		$data['action_fileld']['radio_product_fields'] = [
			[
				'value' => 'simple_product',
				'name'  => __( 'Simple', 'funnel-builder' ),
			],
			[
				'value' => 'free_product',
				'name'  => __( 'Free', 'funnel-builder' ),
			],
			[
				'value' => 'virtual',
				'name'  => __( 'Virtual', 'funnel-builder' ),
			],
		];

		$userText = __( 'Optin page user setting', 'funnel-builder' );

		$data['action_fileld']['user'] = [
			'heading'         => __( 'WordPress User', 'funnel-builder' ),
			'sub_heading'     => $userText,
			'create_wp_user'  => __( 'Create WP User', 'funnel-builder' ),
			'default_wp_role' => __( 'Choose user role', 'funnel-builder' ),
			'get_user_role'   => self::get_user_role(),
			'user_login'      => __( 'Choose if we need to login user after optin', 'funnel-builder' ),
		];

		$data['lms_active'] = false;
		$lms_obj            = WFOPP_Core()->optin_actions->get_integration_object( WFFN_Optin_Action_Assign_LD_Course::get_slug() );
		if ( $lms_obj instanceof WFFN_Optin_Action ) {
			$data['lms_active'] = $lms_obj->should_register();
		}

		$data['action_fileld']['lms'] = [
			'heading'            => __( 'Assign Course', 'funnel-builder' ),
			'lms_course'         => __( 'LMS Course', 'funnel-builder' ),
			'assign_ld_course'   => __( 'Select Course', 'funnel-builder' ),
			'course_placeholder' => __( 'Select Course', 'funnel-builder' ),
			'hint'               => __( 'Enter minimum 3 letters.', 'funnel-builder' ),
			'not_found'          => __( 'Oops! No elements found. Consider changing the search query.', 'funnel-builder' ),
		];

		$data['action_fileld']['email_notify'] = array(
			'legends_texts' => array(
				'email_notification' => __( 'Email Notification', 'funnel-builder' ),
			),
			'fields'        => array(
				'lead_enable_notify'        => array(
					'label' => __( 'Lead Notification', 'funnel-builder' ),
				),
				'lead_notification_subject' => array(
					'label'       => __( 'Subject', 'funnel-builder' ),
					'placeholder' => __( 'Enter email subject', 'funnel-builder' ),
				),
				'lead_notification_body'    => array(
					'label'       => __( 'Body', 'funnel-builder' ),
					'placeholder' => __( 'Enter email content to be sent', 'funnel-builder' ),
					'hint'        => __( "Use shortcode [wfop_id] to show optin form id <br> Use shortcode [wfop_first_name] to show optin first name <br> Use shortcode [wfop_last_name] to show optin last name <br> Use shortcode [wfop_email] to show optin email <br> Use shortcode [wfop_phone] to show optin phone number <br> Use shortcode [wfop_custom key='Label'] to show optin custom filed value <br> Use shortcode [bwf_autologin_link] to show option auto login generated link. (Works for learndash integratgion only)", 'funnel-builder' ),
				),
				'test_email'                => array(
					'label'       => __( 'Test Email', 'funnel-builder' ),
					'placeholder' => __( 'Enter your email to test', 'funnel-builder' ),
				),
				'admin_email_notify'        => array(
					'label' => __( 'Admin Notification', 'funnel-builder' ),
				),
				'op_admin_email'            => array(
					'label'       => __( 'Email', 'funnel-builder' ),
					'placeholder' => __( 'Email Address', 'funnel-builder' ),
					'hint'        => __( 'Enter comma separated email IDs for multiple emails', 'funnel-builder' ),
				),
			),
		);

		return $data;
	}

	public static function get_user_role() {
		global $wp_roles;
		$users     = array();
		$get_roles = $wp_roles->roles;

		foreach ( $get_roles as $key => $value ) {
			$user         = [];
			$user['id']   = $key;
			$user['name'] = $value['name'];
			$users[]      = $user;
		}

		return $users;
	}

	public static function validate_product() {
		check_admin_referer( 'wffn_op_validate_product', '_nonce' );
		$product_id = 0;

		$options = ( isset( $_POST['data'] ) && ( wffn_clean( $_POST['data'] ) ) ) ? wffn_clean( $_POST['data'] ) : 0;


		if ( is_array( $options ) ) {
			$product_id = $options['id'];
		}

		$resp           = [];
		$resp['status'] = false;

		$create_woo_order = WFOPP_Core()->optin_actions->get_integration_object( WFFN_Optin_Action_Create_Woo_Order::get_slug() );
		if ( $create_woo_order instanceof WFFN_Optin_Action ) {
			$resp['status'] = $create_woo_order->get_product_status( $product_id );
		}

		wp_send_json( $resp );
	}

	public static function product_search() {
		check_admin_referer( 'wffn_product_search', '_nonce' );

		global $wpdb;
		$products = array();

		$term = ( isset( $_POST['term'] ) && ( wffn_clean( $_POST['term'] ) ) ) ? wffn_clean( $_POST['term'] ) : '';

		if ( empty( $term ) ) {
			wp_die();
		}

		$like_term     = '%' . $wpdb->esc_like( $term ) . '%';
		$post_types    = array( 'product' );
		$post_statuses = current_user_can( 'edit_private_products' ) ? array( 'private', 'publish' ) : array( 'publish' );

		/**
		 * phpcs:disable
		 */
		$all_products = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT posts.ID, posts.post_title FROM {$wpdb->posts} posts
				LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
				WHERE (
					posts.post_title LIKE %s
					OR (
						postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
					)
				)
				AND posts.post_type IN ('" . implode( "','", $post_types ) . "')
				AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "') 
				ORDER BY posts.post_parent ASC, posts.post_title ASC", $like_term, $like_term ) );
		/**
		 * phpcs:enable
		 */

		if ( $all_products ) {
			foreach ( $all_products as $product ) {
				$data         = [];
				$data['id']   = absint( $product->ID );
				$data['name'] = sprintf( '%s (#%d)', rawurldecode( $product->post_title ), absint( $product->ID ) );
				$products[]   = $data;
			}
		}

		if ( is_numeric( $term ) ) {
			$post_id   = absint( $term );
			$post_type = get_post_type( $post_id );
			if ( 'product' === $post_type ) {
				$data         = [];
				$data['id']   = $post_id;
				$data['name'] = sprintf( '%s (#%d)', get_the_title( $post_id ), absint( $post_id ) );
				$products[]   = $data;
			}

		}

		wp_send_json( $products );
	}

	public function update_actions_settings() {

		check_admin_referer( 'wffn_op_actions_settings_update', '_nonce' );

		$options    = ( isset( $_POST['data'] ) && ( $_POST['data'] ) ) ? wp_unslash( $_POST['data'] ) : 0;   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$test_email = ( isset( $_POST['email_testing'] ) ) ? wffn_clean( $_POST['email_testing'] ) : false;
		if ( $test_email === 'true' ) {
			if ( ! isset( $options['test_email'] ) || empty( $options['test_email'] ) ) {
				$resp           = [];
				$resp['status'] = false;
				$resp['msg']    = __( 'Kindly provide the test email id.', 'funnel-builder' );
				wp_send_json( $resp );
			}
			$resp = $this->testing_email( $options, $options['test_email'] );
		} else {
			unset( $options['test_email'] );
			$optin_id = isset( $_POST['optin_id'] ) ? wffn_clean( $_POST['optin_id'] ) : 0;
			$resp     = [];

			$service_form  = isset( $_POST['optin_service_form'] ) ? $_POST['optin_service_form'] : [];  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$optin_service = $service_form;
			$wffn_op_id    = $optin_id;
			$formBuilder   = isset( $service_form['formBuilder'] ) ? $service_form['formBuilder'] : '';

			if ( $wffn_op_id > 0 && ! empty( $formBuilder ) ) {
				$optin_data                      = [];
				$optin_data['optin_form_enable'] = isset( $service_form['optin_form_enable'] ) ? $service_form['optin_form_enable'] : 'false';
				$optin_data['formBuilder']       = $formBuilder;

				$optin_data['fields']     = isset( $service_form['fields'] ) ? $service_form['fields'] : [];
				$optin_data['formFields'] = isset( $service_form['formFields'] ) ? $service_form['formFields'] : [];
				$optin_data['html_code']  = isset( $service_form['htmlCode'] ) ? $service_form['htmlCode'] : '';

				$options['optin_service_form'] = $optin_data;
				WFFN_Core()->logger->log( "Form connection settings: " . print_r( $optin_service, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( is_array( $options ) ) {
				update_post_meta( $optin_id, 'wffn_actions_custom_settings', $options );
			}

			$resp['status'] = true;
			$resp['msg']    = __( 'Settings Updated', 'funnel-builder' );
			$resp['data']   = '';
		}

		wp_send_json( $resp );
	}

	/**
	 * @param $page_id
	 *
	 * @return mixed|string[]
	 */
	public function get_page_design( $page_id ) {

		$design_data = get_post_meta( $page_id, '_wfop_selected_design', true );
		if ( empty( $design_data ) ) {
			return $this->default_design_data();
		}

		return $design_data;
	}

	public function default_design_data() {
		return [
			'selected'        => 'wp_editor_1',
			'selected_type'   => 'wp_editor',
			'template_active' => 'no',
		];
	}

	public function localize_text_data() {

		$data           = [
			'importer' => [
				'activate_template' => [
					'heading'     => __( 'Are you sure you want to Activate this template?', 'funnel-builder' ),
					'sub_heading' => '',
					'button_text' => __( 'Yes, activate this template!', 'funnel-builder' ),
				],
				'add_template'      => [
					'heading'     => __( 'Are you sure you want to import this template?', 'funnel-builder' ),
					'sub_heading' => '',
					'button_text' => __( 'Yes, Import this template!', 'funnel-builder' ),
				],
				'remove_template'   => [
					'heading'     => __( 'Are you sure you want to remove this template?', 'funnel-builder' ),
					'sub_heading' => __( 'Any changes done to the current template will be lost. This action can\'t be undone.', 'funnel-builder' ),
					'button_text' => __( 'Yes, remove this template!', 'funnel-builder' ),
				],
			],
		];
		$data['fields'] = [
			'field_id_slug'               => __( 'Field ID', 'funnel-builder' ),
			'inputs'                      => [
				'active'   => __( 'Active', 'funnel-builder' ),
				'inactive' => __( 'Inactive', 'funnel-builder' ),
			],
			'section'                     => [
				'default_sub_heading' => __( 'Example: Fields marked with * are mandatory', 'funnel-builder' ),
				'default_classes'     => '',
				'add_heading'         => __( 'Add Section', 'funnel-builder' ),
				'update_heading'      => __( 'Update', 'funnel-builder' ),
				'delete'              => __( 'Aero you sure you want to remove {{section_name}} Section?', 'funnel-builder' ),
				'fields'              => [
					'heading'     => __( 'Section Name', 'funnel-builder' ),
					'sub_heading' => __( 'Sub Heading', 'funnel-builder' ),
					'classes'     => __( 'Classes', 'funnel-builder' ),
				],
			],
			'steps_error_msgs'            => [
				'single_step' => __( 'Step 1', 'funnel-builder' ),
				'two_step'    => __( 'Step 2', 'funnel-builder' ),
				'third_step'  => __( 'Step 3', 'funnel-builder' ),
			],
			'empty_step_error'            => __( 'can\'t be blank. Add a few fields or remove the step and save again.', 'funnel-builder' ),
			'input_field_error'           => [
				'wfop_optin_email' => __( 'Optin Email is required for optin form', 'funnel-builder' ),
			],
			'add_new_btn'                 => __( 'Add Section', 'funnel-builder' ),
			'update_btn'                  => __( 'Update', 'funnel-builder' ),
			'show_field_label1'           => __( 'Status', 'funnel-builder' ),
			'show_field_label2'           => __( 'Label', 'funnel-builder' ),
			'show_field_label3'           => __( 'Placeholder', 'funnel-builder' ),
			'product_you_save_merge_tags' => __( 'Merge Tags: {{quantity}},{{saving_value}} or {{saving_percentage}}', 'funnel-builder' ),
			'field_types_label'           => __( 'Field Type', 'funnel-builder' ),
			'field_types'                 => [
				[
					'id'   => 'text',
					'name' => __( 'Text', 'funnel-builder' ),
				],
				[
					'id'   => 'radio',
					'name' => __( 'Radios', 'funnel-builder' ),
				],
				[
					'id'   => 'wfop_wysiwyg',
					'name' => __( 'HTML', 'funnel-builder' ),
				],
				[
					'id'   => 'select',
					'name' => __( 'Select', 'funnel-builder' ),
				],
				[
					'id'   => 'textarea',
					'name' => __( 'Textarea', 'funnel-builder' ),
				],
				[
					'id'   => 'checkbox',
					'name' => __( 'Checkbox', 'funnel-builder' ),
				],
				[
					'id'   => 'hidden',
					'name' => __( 'Hidden', 'funnel-builder' ),
				],
			],

			'label_field_label'              => __( 'Label', 'funnel-builder' ),
			'options_field_label'            => __( 'Options', 'funnel-builder' ),
			'options_field_placeholder'      => __( 'Enter options comma separated. Example: apple,grapes', 'funnel-builder' ),
			'default_field_label'            => __( 'Default', 'funnel-builder' ),
			'default_field_placeholder'      => __( 'Default Value', 'funnel-builder' ),
			'order_total_breakup_label'      => __( 'Detailed Summary', 'funnel-builder' ),
			'default_field_checkbox_options' => [
				[
					'id'   => '1',
					'name' => __( 'Checked', 'funnel-builder' ),
				],
				[
					'id'   => '0',
					'name' => __( 'Un-checked', 'funnel-builder' ),
				],
			],
			'field_width_options'            => [
				[
					'id'   => 'wffn-sm-100',
					'name' => __( 'Full', 'funnel-builder' ),
				],
				[
					'id'   => 'wffn-sm-33',
					'name' => __( 'One Third', 'funnel-builder' ),
				],
				[
					'id'   => 'wffn-sm-50',
					'name' => __( 'One Half', 'funnel-builder' ),
				],
				[
					'id'   => 'wffn-sm-67',
					'name' => __( 'Two Third', 'funnel-builder' ),
				],
			],
			'field_radio_alignment_options'  => [
				[
					'value' => 'horizontal',
					'name'  => __( 'Horizontal', 'funnel-builder' ),
				],
				[
					'value' => 'vertical',
					'name'  => __( 'Vertical', 'funnel-builder' ),
				],

			],
			'placeholder_field_label'        => __( 'Placeholder', 'funnel-builder' ),
			'required_field_label'           => __( 'Required', 'funnel-builder' ),
			'add_field'                      => __( 'Add Field', 'funnel-builder' ),
			'edit_field'                     => __( 'Edit Field', 'funnel-builder' ),
			'validation_error'               => __( 'Validation Error', 'funnel-builder' ),
			'delete_c_field'                 => __( 'Are you sure you want to delete field', 'funnel-builder' ),
			'delete_c_field_sub_heading'     => __( 'This action can be undone.', 'funnel-builder' ),
			'yes_delete_the_field'           => __( 'Yes, Delete the field', 'funnel-builder' ),
			'field_width_label'              => __( 'Width', 'funnel-builder' ),
			'radio_field_alignment'          => __( 'Alignment', 'funnel-builder' ),
		];
		$data['global'] = [
			'form_has_changes'              => [
				'title'             => __( 'Changes have been made!', 'funnel-builder' ),
				'text'              => __( 'You need to save changes before generating preview.', 'funnel-builder' ),
				'confirmButtonText' => __( 'Yes, Save it!', 'funnel-builder' ),
				'cancelText'        => __( 'Cancel', 'funnel-builder' ),
			],
			'no_products'                   => __( 'No product associated with this checkout. You need to add minimum one product to generate preview', 'funnel-builder' ),
			'remove_product'                => [
				'title'             => __( 'Want to delete this product from checkout?', 'funnel-builder' ),
				'text'              => __( "You won't be able to revert this!", 'funnel-builder' ),
				'confirmButtonText' => __( 'Delete', 'funnel-builder' ),
			],
			'active'                        => __( 'Active', 'funnel-builder' ),
			'inactive'                      => __( 'Inactive', 'funnel-builder' ),
			'add_checkout'                  => [
				'heading'           => __( 'Title', 'funnel-builder' ),
				'post_content'      => __( 'Description', 'funnel-builder' ),
				'checkout_url_slug' => __( 'URL Slug', 'wordpress' ),
			],
			'confirm_button_text'           => __( 'Yes, remove this section!', 'funnel-builder' ),
			'confirm_button_text_ok'        => __( 'Ok', 'funnel-builder' ),
			'cncel_button_text'             => __( 'Cancel', 'funnel-builder' ),
			'delete_checkout_page_head'     => __( 'Are you sure you want to delete this checkout page?', 'funnel-builder' ),
			'delete_checkout_page'          => __( 'Are you sure, you want to delete this permanently? This can`t be undone', 'funnel-builder' ),
			'add_checkout_page'             => __( 'Add New ', 'funnel-builder' ),
			'edit_checkout_page'            => __( 'Edit Checkout Page', 'funnel-builder' ),
			'add_checkout_btn'              => __( 'Create a Checkout', 'funnel-builder' ),
			'update_btn'                    => __( 'Update', 'funnel-builder' ),
			'data_saving'                   => __( 'Data Saving...', 'funnel-builder' ),
			'shortcode_copy_message'        => __( 'Shortcode Copied!', 'funnel-builder' ),
			'enable'                        => __( 'Enable', 'funnel-builder' ),
			'add_product_popup'             => __( 'Add Product', 'funnel-builder' ),
			'pro_feature_mesage_heading'    => __( 'This is pro feature!', 'funnel-builder' ),
			'pro_feature_mesage_subheading' => __( '', 'funnel-builder' ),
		];
		$data['error']  = [
			400 => array(
				'title'             => __( 'Oops! Unable to save this form', 'funnel-builder' ),
				'text'              => __( 'This Forms contains extremely large options. Please increase server\'s max_input_vars limit. Not sure? Contact support.', 'funnel-builder' ),
				'confirmButtonText' => __( 'Okay! Got it', 'funnel-builder' ),
				'type'              => 'error',
			),
			500 => array(
				'title'             => __( 'Oops! Internal Server Error', 'funnel-builder' ),
				'text'              => '',
				'confirmButtonText' => __( 'Okay! Got it', 'funnel-builder' ),
				'type'              => 'error',
			),
			502 => array(
				'title'             => __( 'Oops! Bad Gateway', 'funnel-builder' ),
				'text'              => '',
				'confirmButtonText' => __( 'Okay! Got it', 'funnel-builder' ),
				'type'              => 'error',
			)
		];

		return $data;
	}


	public function add_elementor_page_templates() {

		$all_templates = wp_get_theme()->get_post_templates();
		$path          = [

			'wfop-boxed.php'  => __( 'Woofunnels Boxed', 'funnel-builder' ),
			'wfop-canvas.php' => __( 'Woofunnels Canvas for Page Builder', 'funnel-builder' )
		];
		if ( isset( $all_templates['page'] ) && is_array( $all_templates['page'] ) ) {
			$paths = array_merge( $all_templates['page'], $path );
		} else {
			$paths = $path;
		}

		return $paths;

	}

	public function may_be_change_template( $template ) {
		global $post;
		if ( ! is_null( $post ) && $post->post_type === $this->get_post_type_slug() ) {
			$template = $this->get_template_url( $template );
		}

		return $template;
	}

	public function get_template_url( $main_template ) {
		global $post;
		$wfop_id       = $post->ID;
		$page_template = apply_filters( 'bwf_page_template', get_post_meta( $wfop_id, '_wp_page_template', true ), $wfop_id );

		$file         = '';
		$body_classes = [];

		switch ( $page_template ) {
			case 'wfop-boxed.php':
				$file           = $this->get_module_path() . 'templates/wfop-boxed.php';
				$body_classes[] = $page_template;
				break;

			case 'wfop-canvas.php':
				$file           = $this->get_module_path() . 'templates/wfop-canvas.php';
				$body_classes[] = $page_template;
				break;

			default:
				/**
				 * Remove Next/Prev Navigation
				 */ add_filter( 'next_post_link', '__return_empty_string' );
				add_filter( 'previous_post_link', '__return_empty_string' );

				$page = locate_template( array( 'page.php' ) );

				if ( ! empty( $page ) ) {
					$file = $page;
				}
				break;
		}
		if ( ! empty( $body_classes ) ) {
			add_filter( 'body_class', [ $this, 'wffn_add_unique_class' ], 9999, 1 );
		}
		if ( file_exists( $file ) ) {
			return $file;
		}

		return $main_template;
	}

	public function get_module_path() {
		return plugin_dir_path( WFOPP_PLUGIN_FILE ) . 'modules/optin-pages/';
	}

	public function maybe_register_breadcrumb_nodes() {
		if ( WFFN_Core()->admin->is_wffn_flex_page( 'wf-op' ) ) {
			BWF_Admin_Breadcrumbs::register_node( array(
				'text' => get_the_title( $this->edit_id ),
				'link' => '',
			) );
		}
	}

	public function include_compatibility_files() {
		include_once $this->get_module_path() . 'compatibilities/page-builders/elementor/class-wffn-optin-pages-elementor.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		$this->register_native_templates();
	}



	public function get_edit_id() {
		return $this->edit_id;
	}

	public function update_global_settings() {
		check_admin_referer( 'wffn_op_global_settings_update', '_nonce' );
		$options = ( isset( $_POST['data'] ) && ( $_POST['data'] ) ) ? wp_unslash( $_POST['data'] ) : 0;   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$resp = [];
		if ( is_array( $options ) ) {
			$options['css']    = isset( $options['css'] ) ? htmlentities( $options['css'] ) : '';
			$options['script'] = isset( $options['script'] ) ? htmlentities( $options['script'] ) : '';
		}

		update_option( 'wffn_op_settings', $options, true );
		$resp['status'] = true;
		$resp['msg']    = __( 'Settings Updated', 'funnel-builder' );
		$resp['data']   = '';
		wp_send_json( $resp );
	}

	public function update_custom_settings() {
		check_admin_referer( 'wffn_op_custom_settings_update', '_nonce' );

		$options = ( isset( $_POST['data'] ) && ( $_POST['data'] ) ) ? wp_unslash( $_POST['data'] ) : 0; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$optin_id = isset( $_POST['optin_id'] ) ? wffn_clean( $_POST['optin_id'] ) : 0;

		$resp = [];
		if ( is_array( $options ) ) {
			$options['custom_css'] = isset( $options['custom_css'] ) ? htmlentities( $options['custom_css'] ) : '';
			$options['custom_js']  = isset( $options['custom_js'] ) ? htmlentities( $options['custom_js'] ) : '';
		}

		update_post_meta( $optin_id, 'wffn_step_custom_settings', $options );

		wp_update_post( get_post( $optin_id ) );
		$resp['status'] = true;
		$resp['msg']    = __( 'Settings Updated', 'funnel-builder' );
		$resp['data']   = '';

		wp_send_json( $resp );
	}

	public function testing_email( $options, $email ) {
		$posted_data               = wp_parse_args( $options, $this->default_custom_settings() );
		$posted_data['test_email'] = $email;
		$user_email_action         = WFOPP_Core()->optin_actions->get_integration_object( WFFN_Optin_Action_User_Email::get_slug() );

		if ( $user_email_action instanceof WFFN_Optin_Action ) {
			$result = $user_email_action->test_email( $posted_data, '', '' );
		}

		$resp           = [];
		$resp['status'] = false;
		$resp['msg']    = __( 'Something wrong try again.', 'funnel-builder' );
		if ( isset( $result['success'] ) && $result['success'] === true ) {
			$resp['status'] = true;
			$resp['msg']    = __( 'Email successfully send.', 'funnel-builder' );
		}

		return $resp;
	}

	public function optin_get_form_list() {
		check_admin_referer( 'wffn_optin_get_form_list', '_nonce' );
		$posted_data  = wffn_clean( $_POST );
		$form_builder = isset( $posted_data['formBuilder'] ) ? $posted_data['formBuilder'] : '';
		$page_id      = isset( $posted_data['page_id'] ) ? $posted_data['page_id'] : 0;
		$result       = array(
			'success'   => false,
			'form_list' => []
		);

		$form_controller = WFOPP_Core()->form_controllers->get_integration_object( $form_builder );
		if ( $form_controller instanceof WFFN_Optin_Form_Controller ) {
			$result['form_list'] = $form_controller->get_all_active_forms( $page_id );
			$result['success']   = true;
		}
		wp_send_json( $result );
	}


	public function create_new_optin_form() {
		check_admin_referer( 'wfop_create_new_from', '_nonce' );
		$posted_data = wffn_clean( $_POST );

		$formBuilder = isset( $posted_data['formBuilder'] ) ? $posted_data['formBuilder'] : '';
		$page_id     = isset( $posted_data['page_id'] ) ? $posted_data['page_id'] : 0;
		$formTitle   = isset( $posted_data['formTitle'] ) ? $posted_data['formTitle'] : '';

		$result = array(
			'success' => false,
		);

		$form_controller = WFOPP_Core()->form_controllers->get_integration_object( $formBuilder );
		if ( $form_controller instanceof WFFN_Optin_Form_Controller ) {
			$result['new_form'] = $form_controller->create_new_form( $formTitle, $page_id );
			$result['success']  = true;
		}

		wp_send_json( $result );
	}

	public function get_base_url_args( $args ) {

		$url = get_home_url();

		$url = add_query_arg( $args, $url );

		return $url;

	}

	/**
	 * Save selected design template against checkout page
	 */

	public function save_design() {
		$resp = array(
			'msg'    => '',
			'status' => false,
		);
		check_ajax_referer( 'wffn_op_save_design', '_nonce' );
		$wfop_id = isset( $_POST['wfop_id'] ) ? absint( wffn_clean( $_POST['wfop_id'] ) ) : 0;

		if ( $wfop_id > 0 ) {
			$selected_type = isset( $_POST['selected_type'] ) ? wffn_clean( $_POST['selected_type'] ) : '';
			$this->update_page_design( $wfop_id, [
				'selected'      => isset( $_POST['selected'] ) ? sanitize_text_field( $_POST['selected'] ) : '',
				'selected_type' => $selected_type
			] );

			$resp = array(
				'msg'    => __( 'Design Saved Successfully', 'funnel-builder' ),
				'status' => true,
			);
		}
		self::send_resp( $resp );
	}

	public function update_page_design( $page_id, $data ) {
		if ( $page_id < 1 ) {
			return $data;
		}
		if ( ! is_array( $data ) ) {
			$data = $this->default_design_data();
		}
		update_post_meta( $page_id, '_wfop_selected_design', $data );

		if ( isset( $data['selected_type'] ) && 'wp_editor' === $data['selected_type'] ) {
			update_post_meta( $page_id, '_wp_page_template', 'wfop-boxed.php' );
		} else {
			update_post_meta( $page_id, '_wp_page_template', 'wfop-canvas.php' );
		}

		return $data;
	}

	public static function send_resp( $data = array() ) {
		if ( ! is_array( $data ) ) {
			$data = [];
		}
		$data['nonce'] = wp_create_nonce( 'wfop_secure_key' );
		wp_send_json( $data );
	}

	public function remove_design() {
		$resp = array(
			'msg'    => '',
			'status' => false,
		);
		check_ajax_referer( 'wffn_op_remove_design', '_nonce' );
		if ( isset( $_POST['wfop_id'] ) && $_POST['wfop_id'] > 0 ) {
			$wfop_id                     = absint( $_POST['wfop_id'] );
			$template                    = $this->default_design_data();
			$template['template_active'] = 'no';
			$this->update_page_design( $wfop_id, $template );
			do_action( 'wfop_template_removed', $wfop_id );
			do_action( 'woofunnels_module_template_removed', $wfop_id );

			$args = [
				'ID'           => $wfop_id,
				'post_content' => ''
			];
			wp_update_post( $args );

			$resp = array(
				'msg'    => __( 'Design Saved Successfully', 'funnel-builder' ),
				'status' => true,
			);
		}
		self::send_resp( $resp );
	}

	public function import_template() {
		$resp = [
			'status' => false,
			'msg'    => __( 'Importing of template failed', 'funnel-builder' ),
		];
		check_ajax_referer( 'wffn_op_import_design', '_nonce' );
		$builder  = isset( $_POST['builder'] ) ? sanitize_text_field( $_POST['builder'] ) : '';
		$template = isset( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : '';
		$wfop_id  = isset( $_POST['wfop_id'] ) ? sanitize_text_field( $_POST['wfop_id'] ) : '';

		$result = WFFN_Core()->importer->import_remote( $wfop_id, $builder, $template, $this->get_cloud_template_step_slug() );

		if ( true === $result['success'] ) {
			$resp['status'] = true;
			$resp['msg']    = __( 'Importing of template finished', 'funnel-builder' );
		} else {
			$resp['error'] = $result['error'];
		}

		self::send_resp( $resp );
	}

	public function toggle_state() {
		check_ajax_referer( 'wffn_op_toggle_state', '_nonce' );
		$resp = [
			'status' => false,
			'msg'    => __( 'Unable to change state', 'funnel-builder' ),
		];

		$state   = isset( $_POST['toggle_state'] ) ? sanitize_text_field( $_POST['toggle_state'] ) : '';
		$wfop_id = isset( $_POST['wfop_id'] ) ? sanitize_text_field( $_POST['wfop_id'] ) : '';

		$status = ( 'true' === $state ) ? 'publish' : 'draft';

		wp_update_post( [ 'ID' => $wfop_id, 'post_status' => $status ] );

		$resp['status'] = true;
		$resp['msg']    = __( 'Status changed successfully', 'funnel-builder' );


		self::send_resp( $resp );
	}

	public function get_cloud_template_step_slug() {
		return 'optin';
	}

	public function update_optin_page() {
		check_ajax_referer( 'wfop_edit_optin', '_nonce' );
		$resp = [
			'status' => false,
			'msg'    => __( 'Unable to change state', 'funnel-builder' ),
			'title'  => '',
		];

		$data    = isset( $_POST['data'] ) ? wffn_clean( json_decode( wp_unslash( wffn_clean( $_POST['data'] ) ), true ) ) : '';
		$wfop_id = isset( $_POST['optin_id'] ) ? sanitize_text_field( $_POST['optin_id'] ) : '';

		$updated = wp_update_post( [ 'ID' => $wfop_id, 'post_title' => $data['title'], 'post_name' => $data['slug'] ] );
		if ( absint( $updated ) === absint( $wfop_id ) ) {
			$resp['status'] = true;
			$resp['title']  = $data['title'];
			$resp['msg']    = __( 'Title updated successfully', 'funnel-builder' );
		}
		self::send_resp( $resp );
	}

	public function get_status() {
		$post_op = get_post( $this->get_edit_id() );

		return $post_op->post_status;
	}

	public function get_module_title( $plural = false ) {
		return ( $plural ) ? __( 'Optin Pages', 'funnel-builder' ) : __( 'Optin Page', 'funnel-builder' );
	}

	public function set_page_template( $wfop_id, $module ) {
		if ( $this->get_cloud_template_step_slug() !== $module ) {
			return;
		}
		update_post_meta( $wfop_id, '_wp_page_template', 'wfop-boxed.php' );

	}

	/**
	 * Modify permalink
	 *
	 * @param string $post_link post link.
	 * @param array $post post data.
	 * @param string $leavename leave name.
	 *
	 * @return string
	 */
	public function post_type_permalinks( $post_link, $post, $leavename ) { //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedParameter

		$bwb_admin_setting = BWF_Admin_General_Settings::get_instance();

		if ( isset( $post->post_type ) && $this->get_post_type_slug() === $post->post_type && empty( trim( $bwb_admin_setting->get_option( 'optin_page_base' ) ) ) ) {

			// If elementor page preview, return post link as it is.
			if ( isset( $_REQUEST['elementor-preview'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return $post_link;
			}

			$structure = get_option( 'permalink_structure' );
			if ( in_array( $structure, $this->get_supported_permalink_strcutures_to_normalize(), true ) ) {
				$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
			}
		}

		return $post_link;
	}

	/**
	 * Have WordPress match postname to any of our public post types.
	 * All of our public post types can have /post-name/ as the slug, so they need to be unique across all posts.
	 * By default, WordPress only accounts for posts and pages where the slug is /post-name/.
	 *
	 * @param WP_Query $query query statement.
	 */
	function add_cpt_post_names_to_main_query( $query ) {

		// Bail if this is not the main query.
		if ( ! $query->is_main_query() ) {
			return;
		}


		// Bail if this query doesn't match our very specific rewrite rule.
		if ( ! isset( $query->query['page'] ) || 2 !== count( $query->query ) ) {
			return;
		}

		// Bail if we're not querying based on the post name.
		if ( empty( $query->query['name'] ) ) {
			return;
		}

		// Add optin page step post type to existing post type array.
		if ( isset( $query->query_vars['post_type'] ) && is_array( $query->query_vars['post_type'] ) ) {

			$post_types = $query->query_vars['post_type'];

			$post_types[] = $this->get_post_type_slug();

			$query->set( 'post_type', $post_types );

		} else {

			// Add CPT to the list of post types WP will include when it queries based on the post name.
			$query->set( 'post_type', array( 'post', 'page', $this->get_post_type_slug() ) );
		}
	}


	public function wffn_add_unique_class( $classes ) {
		$classes[] = 'wffn-page-template';

		return $classes;
	}

	/**
	 * Defines scripts needed for "no conflict mode".
	 *
	 * @access public
	 * @global $wp_scripts
	 *
	 * @uses WFFN_Admin::no_conflict_mode()
	 */
	public function no_conflict_mode_script() {
		if ( ! apply_filters( 'wffn_no_conflict_mode', true ) ) {
			return;
		}

		global $wp_scripts;

		$wp_required_scripts   = array( 'admin-bar', 'common', 'jquery-color', 'utils', 'svg-painter', 'updates', 'wp-color-picker' );
		$wffn_required_scripts = apply_filters( 'wffn_no_conflict_scripts', array(
			'common'   => array(
				'wffn-admin-ajax',
				'wffn-izimodal-scripts',
				'wffn-sweetalert',
				'wffn-vuejs',
				'wffn-vue-vfg',
				'wffn-vue-multiselect',
				'wffn-admin-scripts',
			),
			'wf-op'    => array(
				'jquery-ui-sortable',
				'wffn_op_js',
				'phone_flag_intl',
				'editor',
				'wp-tinymce',
			),
			'settings' => array(),
		) );

		$this->no_conflict_mode( $wp_scripts, $wp_required_scripts, $wffn_required_scripts, 'scripts', 'wf-op' );
	}

	public function add_optin_as_contact_child( $children ) {
		$children          = is_array( $children ) ? $children : [];
		$children['optin'] = 'WooFunnels_Optin';

		return $children;
	}

	/**
	 * @param $styles
	 *
	 * @return mixed
	 */
	public function add_optin_fronted_style( $styles ) {
		$styles['wffn-optin-frontend-style'] = array(
			'path'      => $this->url . 'assets/css/wfopp-optin-frontend.css',
			'version'   => WFFN_VERSION_DEV,
			'in_footer' => false,
			'supports'  => array(),
		);

		return $styles;
	}

	public function get_optin_id() {
		if ( $this->form_builder->is_preview ) {
			return isset( $_POST['wffn_optin_id'] ) ? wffn_clean( $_POST['wffn_optin_id'] ) : 0; // phpcs:ignore
		}
		global $post;

		return ( $post instanceof WP_Post ) ? $post->ID : 0;

	}

	public function form_save() {

		check_ajax_referer( 'wffn_wfop_form_save', '_nonce' );

		$customizations   = isset( $_POST['wffn_form_customizations'] ) ? wffn_clean( json_decode( wp_unslash( wffn_clean( $_POST['wffn_form_customizations'] ) ), true ) ) : [];
		$form_field_width = isset( $_POST['wffn_form_field_width'] ) ? wffn_clean( $_POST['wffn_form_field_width'], true ) : [];

		$optin_id = isset( $_POST['wffn_optin_id'] ) ? wffn_clean( $_POST['wffn_optin_id'] ) : 0;
		$this->form_builder->save_form_field_width( $optin_id, $form_field_width );
		$this->form_builder->save_form_customizations( $optin_id, $customizations );
		wp_update_post( get_post( $optin_id ) );
		$resp           = [];
		$resp['status'] = true;

		wp_send_json( $resp );
	}


	public function maybe_handle_preview() {
		if ( 'yes' !== filter_input( INPUT_POST, 'wfop_show_preview', FILTER_SANITIZE_STRING ) ) {
			return;
		}
		check_ajax_referer( 'wffn_wfop_show_preview', '_nonce' );
		$this->form_builder->is_preview = true;

		$get_controller = WFOPP_Core()->form_controllers->get_integration_object( 'form' );

		echo $get_controller->add_optin_form_shortcode();// phpcs:ignore
		exit;

	}

	public function maybe_add_js_localized( $localized ) {
		if ( $this->is_wfop_page() ) {
			$current_step = WFFN_Core()->data->get_current_step();
			$db_options   = $this->setup_custom_options( $current_step['id'] );
			if ( isset( $db_options['op_valid_enable'] ) && $db_options['op_valid_enable'] === 'true' ) {
				$localized['op_valid_text']  = $db_options['op_valid_text'];
				$localized['op_valid_email'] = $db_options['op_valid_email'];
			} else {
				$localized['op_valid_text']  = '';
				$localized['op_valid_email'] = '';
			}
		}

		return $localized;
	}

	public function maybe_export_optin_form() {
		$funnel_id = filter_input( INPUT_POST, 'wffn-funnel-id', FILTER_SANITIZE_STRING );
		$action    = filter_input( INPUT_POST, 'wffn-action', FILTER_SANITIZE_STRING );
		if ( 'export_optin_data' !== $action || $funnel_id === '' ) {
			return;
		}
		check_admin_referer( 'wffn-action-nonce', 'wffn-action-nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$optin_obj = WFFN_DB_Optin::get_instance();
		$contacts  = $optin_obj->get_contact_by_funnels( $funnel_id );

		if ( count( $contacts ) < 1 ) {
			return;
		}

		$optins = array();
		foreach ( $contacts as $contact ) {
			$data = json_decode( $contact['data'], true );
			unset( $contact['data'] );
			$optins[] = array_merge( $contact, $data );
		}

		ob_clean();
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename=wffn-optin-form-export-' . gmdate( 'm-d-Y' ) . '.csv' );
		if ( isset( $contacts['0'] ) ) {
			$fp = fopen( 'php://output', 'w' );
			fputcsv( $fp, array_keys( $optins['0'] ) ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fputcsv
			foreach ( $optins as $values ) {
				fputcsv( $fp, $values ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fputcsv
			}
			fclose( $fp ); //phpcs:ignore
		}
		ob_flush();

		exit;
	}

	/**
	 *
	 */
	public function course_search() {
		check_admin_referer( 'wffn_course_search', '_nonce' );

		$result = array();

		$term = ( isset( $_POST['term'] ) && ( wffn_clean( $_POST['term'] ) ) ) ? wffn_clean( $_POST['term'] ) : '';

		if ( empty( $term ) ) {
			wp_send_json( $result );
		}

		$lms_obj = WFOPP_Core()->optin_actions->get_integration_object( WFFN_Optin_Action_Assign_LD_Course::get_slug() );

		if ( $lms_obj instanceof WFFN_Optin_Action ) {
			$lms_posts = $lms_obj->get_courses( $term );
		}

		if ( count( $lms_posts ) > 0 ) {
			$data = [];
			foreach ( $lms_posts as $lms_post ) {
				$data['id']   = $lms_post->ID;
				$data['name'] = $lms_post->post_title;
			}

			$result[] = $data;
		}

		wp_send_json( $result );
	}

	public function get_embed_mode() {

		if ( true === $this->form_builder->is_preview ) {
			return wffn_clean( filter_input( INPUT_POST, 'wffn_embed_mode', FILTER_SANITIZE_STRING ) );
		}

		return 'inline';
	}

	public function get_open_popup_url() {
		return site_url() . '?wfop-popup=yes';
	}


	/*
	 * Send Response back to checkout page builder
	 * With nonce security keys
	 * also delete transient of particular checkout page it page is found in request
	 */
	public function get_layout() {
		/**
		 * remove selected field(step field) from main checkout fields [billing,shipping];
		 */
		$data                  = $this->manage_input_fields();
		$data['default_steps'] = $this->get_default_steps_fields();

		return $data;
	}

	/**
	 * Remove Selected field from available checkout fields
	 * @return array|mixed
	 */
	private function manage_input_fields() {
		$page_data        = $this->get_page_layout( $this->edit_id );
		$input_fields     = $this->get_optin_fields();
		$input_fields     = $this->merge_custom_fields( $input_fields );
		$available_fields = $input_fields;
		$selected_fields  = $page_data['fieldsets'];

		if ( empty( $selected_fields ) || ! is_array( $selected_fields ) ) {
			return $input_fields;
		}
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
					$id                                                              = $field['id'];
					$type                                                            = $field['field_type'];
					$temp_page_field                                                 = $page_data['fieldsets'][ $step ][ $index ]['fields'][ $f_index ];
					$page_data['fieldsets'][ $step ][ $index ]['fields'][ $f_index ] = apply_filters( 'wfop_builder_merge_field_arguments', $temp_page_field, $id, $type, $available_fields );
					if ( isset( $input_fields[ $type ][ $id ] ) ) {
						unset( $input_fields[ $type ][ $id ] );
					}
				}
			}
		}

		foreach ( $input_fields as $key => $field_data ) {
			if ( is_array( $field_data ) && count( $field_data ) === 0 ) {
				$input_fields[ $key ] = new stdClass();
			}
		}
		foreach ( $input_fields as $type => $section_fields ) {
			foreach ( $section_fields as $field_id => $field ) {
				if ( ! isset( $field['data_label'] ) ) {
					$input_fields[ $type ][ $field_id ]['data_label'] = $field['label'];
				}
			}
		}
		$input_fields = [
			'input_fields'     => $input_fields,
			'available_fields' => $available_fields,
		];
		$data         = array_merge( $page_data, $input_fields );

		return $data;
	}

	private function get_optin_fields() {
		$output = [
			'basic' => [
				self::FIELD_PREFIX . self::WFOP_FIRST_NAME_FIELD_SLUG => array(
					'label'       => 'First name',
					'required'    => 'true',
					'class'       => array(
						0 => 'form-row-first',
					),
					'priority'    => '10',
					'type'        => 'text',
					'id'          => self::FIELD_PREFIX . self::WFOP_FIRST_NAME_FIELD_SLUG,
					'placeholder' => __( 'Your First Name', 'funnel-builder' ),
					'width'       => 'wffn-sm-100',
				),
				self::FIELD_PREFIX . self::WFOP_LAST_NAME_FIELD_SLUG  => array(
					'label'       => 'Last name',
					'required'    => 'true',
					'class'       => array(
						0 => 'form-row-first',
					),
					'priority'    => '10',
					'type'        => 'text',
					'id'          => self::FIELD_PREFIX . self::WFOP_LAST_NAME_FIELD_SLUG,
					'placeholder' => __( 'Your Last Name', 'funnel-builder' ),
					'width'       => 'wffn-sm-100',
				),
				self::FIELD_PREFIX . self::WFOP_EMAIL_FIELD_SLUG      => array(
					'label'       => __( 'Email', 'funnel-builder' ),
					'required'    => true,
					'type'        => self::FIELD_PREFIX . self::WFOP_EMAIL_FIELD_SLUG,
					'class'       => array( 'form-row-wide' ),
					'validate'    => array( 'email' ),
					'placeholder' => __( 'Your Email', 'funnel-builder' ),
					'priority'    => 110,
					'width'       => 'wffn-sm-100',
				),
				self::FIELD_PREFIX . self::WFOP_PHONE_FIELD_SLUG      => array(
					'label'       => __( 'Phone Number', 'funnel-builder' ),
					'type'        => 'tel',
					'class'       => array( 'form-row-wide' ),
					'id'          => self::FIELD_PREFIX . self::WFOP_PHONE_FIELD_SLUG,
					'validate'    => array( 'phone' ),
					'placeholder' => __( 'Your Phone Number', 'funnel-builder' ),
					'is_locked'   => 'yes',
					'width'       => 'wffn-sm-100',
					'priority'    => 100,
				),
			]
		];

		return apply_filters( 'wffn_get_optin_default_fields', $output );
	}

	/**
	 * Merge Custom created field with real fields;
	 *
	 * @param $input_fields
	 *
	 * @return mixed
	 */
	private function merge_custom_fields( $input_fields ) {

		$custom_fields = $this->get_page_custom_fields( $this->edit_id );

		if ( ! is_array( $custom_fields ) ) {
			return $input_fields;
		}
		$custom_fields = $custom_fields['advanced'];
		if ( ! empty( $custom_fields ) ) {
			foreach ( $custom_fields as $key => $field ) {
				$input_fields['advanced'][ $key ] = $field;
			}
		} else {
			$input_fields['advanced'] = new stdClass();
		}

		return $input_fields;
	}


	public function get_default_steps_fields( $active_steps = false ) {

		return array(
			'single_step' => array(
				'name'          => __( 'Step 1', 'funnel-builder' ),
				'slug'          => 'single_step',
				'friendly_name' => __( 'Single Step Optin', 'funnel-builder' ),
				'active'        => 'yes',
			),
			'two_step'    => array(
				'name'          => __( 'Step 2', 'funnel-builder' ),
				'slug'          => 'two_step',
				'friendly_name' => __( 'Two Step Optin', 'funnel-builder' ),
				'active'        => true === $active_steps ? 'yes' : 'no',
			),
			'third_step'  => array(
				'name'          => __( 'Step 3', 'funnel-builder' ),
				'slug'          => 'third_step',
				'friendly_name' => __( 'Three Step Optin', 'funnel-builder' ),
				'active'        => true === $active_steps ? 'yes' : 'no',
			),
		);
	}

	/**
	 * Get page layout data
	 *
	 * @param $page_id
	 *
	 * @return array|mixed
	 */
	public function get_page_layout( $page_id ) {

		$data = get_post_meta( $page_id, '_wfop_page_layout', true );
		if ( ! empty( $data ) && count( $data ) > 0 ) {
			return $data;
		}

		$data = array(
			'steps'        => $this->get_default_steps_fields(),
			'fieldsets'    => [
				'single_step' => [],
				'two_step'    => [],
				'third_step'  => [],
			],
			'current_step' => 'single_step',
		);

		$data['fieldsets']['single_step'][] = array(
			'class'      => 'wfop_single_step',
			'is_default' => 'yes',
			'fields'     => array(
				array(
					'label'       => __( 'First name', 'funnel-builder' ),
					'required'    => 'true',
					'class'       => array(
						0 => 'form-row-first',
					),
					'priority'    => '10',
					'type'        => 'text',
					'id'          => self::FIELD_PREFIX . self::WFOP_FIRST_NAME_FIELD_SLUG,
					'field_type'  => 'basic',
					'placeholder' => __( 'Your First Name', 'funnel-builder' ),
					'width'       => 'wffn-sm-100',
				),

				array(
					'label'       => __( 'Email', 'funnel-builder' ),
					'required'    => 'true',
					'type'        => 'email',
					'class'       => array(
						0 => 'form-row-wide',
					),
					'validate'    => array(
						0 => 'email',
					),
					'priority'    => '110',
					'id'          => self::FIELD_PREFIX . self::WFOP_EMAIL_FIELD_SLUG,
					'field_type'  => 'basic',
					'is_locked'   => 'yes',
					'placeholder' => __( 'Your Email ', 'funnel-builder' ),
					'width'       => 'wffn-sm-100',
				),
			),
		);

		$data = apply_filters( 'wfop_default_form_fieldset', $data );

		return $data;
	}

	public function get_page_custom_fields( $page_id ) {
		$fields = get_post_meta( $page_id, '_wfop_page_custom_field', true );

		if ( ! is_array( $fields ) || empty( $fields ) ) {
			$fields = [ 'advanced' => [] ];
		}

		return apply_filters( 'wfop_page_custom_field', $fields );
	}

	public function update_page_custom_fields( $wfop_id, $data = [] ) {

		if ( $wfop_id < 1 ) {
			return;
		}
		update_post_meta( $wfop_id, '_wfop_page_custom_field', $data );
	}


	/**
	 * Save form fields of checkout page
	 */
	public static function save_layout() {
		$resp = array(
			'msg'      => '',
			'status'   => false,
			'products' => [],
		);
		if ( ! isset( $_REQUEST['wfop_nonce'] ) || ! wp_verify_nonce( wffn_clean( $_REQUEST['wfop_nonce'] ), 'wfop_secure_key' ) ) {
			$resp           = [];
			$resp['status'] = false;
			$resp['msg']    = __( 'Cheating? Huh', 'funnel-builder' );
			self::send_resp( $resp );
		}


		if ( isset( $_POST['wfop_id'] ) ) {
			$wfop_id = wffn_clean( $_POST['wfop_id'] );
			self::update_page_layout( $wfop_id, $_POST );
			$resp['status'] = true;
			$resp['msg']    = __( 'Changes saved', 'funnel-builder' );
		}

	}

	/**
	 * Add custom field to current form
	 */
	public function add_field() {
		if ( ! isset( $_REQUEST['wfop_nonce'] ) || ! wp_verify_nonce( wffn_clean( $_REQUEST['wfop_nonce'] ), 'wfop_secure_key' ) ) {
			$resp           = [];
			$resp['status'] = false;
			$resp['msg']    = __( 'Cheating? Huh', 'funnel-builder' );
			self::send_resp( $resp );
		}
		$resp = array(
			'msg'      => '',
			'status'   => false,
			'products' => [],
		);
		if ( isset( $_POST['wfop_id'] ) && $_POST['wfop_id'] > 0 ) {
			$wfop_id             = wffn_clean( $_POST['wfop_id'] );
			$label               = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['label'] ) ) ? wffn_clean( $_POST['fields']['label'] ) : '';
			$name                = sanitize_title( $label );
			$placeholder         = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['placeholder'] ) ) ? wffn_clean( $_POST['fields']['placeholder'] ) : '';
			$width               = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['width'] ) ) ? wffn_clean( $_POST['fields']['width'] ) : '';
			$field_type          = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['field_type'] ) ) ? wffn_clean( $_POST['fields']['field_type'] ) : '';
			$section_type        = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['section_type'] ) ) ? wffn_clean( $_POST['fields']['section_type'] ) : '';
			$default             = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['default'] ) ) ? wffn_clean( $_POST['fields']['default'] ) : '';
			$options             = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['options'] ) && ! empty( $_POST['fields']['options'] ) ) ? ( explode( '|', trim( wffn_clean( $_POST['fields']['options'] ) ) ) ) : [];
			$new_sanitize_option = [];
			if ( is_array( $options ) && count( $options ) > 0 ) {
				foreach ( $options as $key => $option ) {
					$key                         = sanitize_title( trim( $option ) );
					$new_sanitize_option[ $key ] = trim( $option );
				}
			}

			$required = ( isset( $_POST['fields'] ) && isset( $_POST['fields']['required'] ) ) ? wffn_clean( $_POST['fields']['required'] ) : '';
			$data     = [
				'label'       => $label,
				'data_label'  => $label,
				'placeholder' => $placeholder,
				'type'        => $field_type,
				'required'    => $required,
				'options'     => $new_sanitize_option,
				'default'     => $default,
				'width'       => $width
			];
			if ( 'email' === $field_type ) {
				$data['validate'][] = 'email';
			}

			$custom_fields                           = $this->get_page_custom_fields( $wfop_id );
			$custom_fields[ $section_type ][ $name ] = $data;
			$this->update_page_custom_fields( $wfop_id, $custom_fields );
			$data['unique_id']  = $name;
			$data['field_type'] = $section_type;
			$resp['status']     = true;
			$resp['data']       = $data;
			$resp['msg']        = __( 'Field Added Saved', 'funnel-builder' );
		}
		self::send_resp( $resp );
	}

	/**
	 * Delete custom field from form of checkout page
	 */
	public function delete_custom_field() {
		if ( ! isset( $_REQUEST['wfop_nonce'] ) || ! wp_verify_nonce( wffn_clean( $_REQUEST['wfop_nonce'] ), 'wfop_secure_key' ) ) {
			$resp           = [];
			$resp['status'] = false;
			$resp['msg']    = __( 'Cheating? Huh', 'funnel-builder' );
			self::send_resp( $resp );
		}
		$resp         = array(
			'msg'    => '',
			'status' => false,
		);
		$section_type = '';
		$index        = 0;
		if ( isset( $_POST['wfop_id'] ) && $_POST['wfop_id'] > 0 ) {
			$wfacp_id = wffn_clean( $_POST['wfop_id'] );
		}
		if ( isset( $_POST['section'] ) && ! empty( $_POST['section'] ) ) {
			$section_type = wffn_clean( $_POST['section'] );
		}
		if ( isset( $_POST['index'] ) && ! empty( $_POST['index'] ) ) {
			$index = wffn_clean( $_POST['index'] );
		}

		if ( empty( $index ) ) {
			self::send_resp( $resp );
		}
		$custom_fields = $this->get_page_custom_fields( $wfacp_id );
		if ( isset( $custom_fields[ $section_type ] ) && isset( $custom_fields[ $section_type ][ $index ] ) ) {
			unset( $custom_fields[ $section_type ][ $index ] );
			$this->update_page_custom_fields( $wfacp_id, $custom_fields );
			$resp['status'] = true;
			$resp['msg']    = __( 'Field Deleted', 'funnel-builder' );
		}

		self::send_resp( $resp );
	}

	/**
	 * Update custom field of checkout form
	 */
	public function update_custom_field() {
		if ( ! isset( $_REQUEST['wfop_nonce'] ) || ! wp_verify_nonce( wffn_clean( $_REQUEST['wfop_nonce'] ), 'wfop_secure_key' ) ) {
			$resp           = [];
			$resp['status'] = false;
			$resp['msg']    = __( 'Cheating? Huh', 'funnel-builder' );
			self::send_resp( $resp );
		}
		$resp = array(
			'msg'    => '',
			'status' => false,
		);
		if ( isset( $_POST['wfop_id'] ) && $_POST['wfop_id'] > 0 ) {
			$wfacp_id     = wffn_clean( $_POST['wfop_id'] );
			$field        = isset( $_POST['field'] ) ? wffn_clean( $_POST['field'] ) : '';
			$section_type = isset( $_POST['section_type'] ) ? trim( wffn_clean( $_POST['section_type'] ) ) : '';
			$index        = $field['id'];
			unset( $field['label'] );
			unset( $field['placeholder'] );
			$custom_fields = $this->get_page_custom_fields( $wfacp_id );
			if ( isset( $custom_fields[ $section_type ] ) && isset( $custom_fields[ $section_type ][ $index ] ) ) {
				$find_field                               = $custom_fields[ $section_type ][ $index ];
				$custom_fields[ $section_type ][ $index ] = wp_parse_args( $field, $find_field );
				$options                                  = ( isset( $_POST['field']['options'] ) && count( $_POST['field']['options'] ) > 0 ) ? wffn_clean( $_POST['field']['options'] ) : [];
				if ( is_array( $options ) && count( $options ) > 0 ) {
					foreach ( $options as $key => $option ) {
						unset( $options[ $key ] );

						$key             = sanitize_title( trim( $key ) );
						$options[ $key ] = trim( $option );
					}
					$custom_fields[ $section_type ][ $index ]['options'] = $options;
				}

				$this->update_page_custom_fields( $wfacp_id, $custom_fields );
				$resp['status'] = true;
				$resp['msg']    = __( 'Field Updated', 'funnel-builder' );
			}
		}
		self::send_resp( $resp );
	}


	/**
	 * @param $page_id
	 * @param $data
	 * @param bool $update_switcher
	 *
	 * @return mixed
	 */
	public static function update_page_layout( $page_id, $data, $update_switcher = true ) { //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedParameter
		if ( $page_id < 1 ) {
			return $data;
		}

		unset( $data['wfop_id'], $data['action'], $data['wfop_nonce'] );
		//this meta use form generate form at form builder
		update_post_meta( $page_id, '_wfop_page_layout', $data );
	}

	public function get_template_settings() {
		include __DIR__ . '/admin-views/template-settings.php';
	}


	public function page_search() {
		check_admin_referer( 'wffn_op_page_search', '_nonce' );
		$term = ( isset( $_POST['term'] ) && ( wffn_clean( $_POST['term'] ) ) ) ? wffn_clean( $_POST['term'] ) : '';

		if ( empty( $term ) ) {
			wp_die();
		}

		$ids     = WFFN_Common::search_page( $term, array( 'page' ) );
		$lms_obj = WFOPP_Core()->optin_actions->get_integration_object( WFFN_Optin_Action_Assign_LD_Course::get_slug() );

		if ( $lms_obj instanceof WFFN_Optin_Action ) {
			$lms_posts = $lms_obj->get_courses( $term );
			if ( count( $lms_posts ) > 0 ) {
				foreach ( $lms_posts as $lms_id ) {
					$ids[] = $lms_id->ID;
				}
			}
		}

		$pages = array();

		foreach ( $ids as $id ) {
			$pages[] = array(
				'id'   => $id,
				'name' => get_the_title( $id ) . ' (#' . $id . ')',
			);
		}
		wp_send_json( $pages );
	}


	public function get_form_field_width() {
		$output          = [];
		$page_data       = $this->get_page_layout( $this->edit_id );
		$selected_fields = $page_data['fieldsets'];

		foreach ( $selected_fields as $step_data ) {
			if ( ! is_array( $step_data ) ) {
				continue;
			}

			foreach ( $step_data as $section ) {
				if ( empty( $section['fields'] ) ) {
					continue;
				}
				$fields = $section['fields'];
				foreach ( $fields as $field ) {
					if ( ! isset( $field['id'] ) || ! isset( $field['field_type'] ) ) {
						continue;
					}
					$id            = $field['id'];
					$output[ $id ] = isset( $field['width'] ) ? $field['width'] : 'wffn-sm-50';
				}
			}
		}

		return $output;
	}

	public function get_inherit_supported_post_type() {
		return apply_filters( 'wffn_op_inherit_supported_post_type', array( 'cartflows_step', 'page' ) );
	}

	public function set_id( $id ) {
		if ( absint( $id ) > 0 ) {
			$this->edit_id = $id;
		}
	}

	public function get_id() {
		return $this->edit_id;
	}
}

if ( class_exists( 'WFOPP_Core' ) ) {
	WFOPP_Core::register( 'optin_pages', 'WFFN_Optin_Pages' );
}