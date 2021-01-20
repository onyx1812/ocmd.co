<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Funnel landing page module
 * Class WFFN_Landing_Pages
 */
class WFFN_Landing_Pages extends WFFN_Module_Common {

    private static $ins = null;
    /**
     * @var WFLP_Admin|null
     */
    public $admin;
    protected $options;
    protected $custom_options;
    protected $template_type = [];
    protected $design_template_data = [];
    protected $templates = [];
    protected $wflp_is_landing = false;
    public $edit_id = 0;
    private $url = '';

    /**
     * WFFN_Landing_Pages constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->url = plugin_dir_url( __FILE__ );
        $this->process_url();


        include_once __DIR__ . '/class-wflp-admin.php';
        $this->admin = WFLP_Admin::get_instance();
        include_once __DIR__ . '/includes/class-wffn-ecomm-tracking-landing.php';
        $this->ecom_tracking = WFFN_Ecomm_Tracking_Landing::get_instance();

        add_action( 'init', array( $this, 'register_post_type' ), 100 );
        add_action( 'init', array( $this, 'load_compatibility' ), 1 );
        add_action( 'wffn_admin_assets', [ $this, 'load_assets' ] );
        add_filter( 'template_include', [ $this, 'may_be_change_template' ], 98 );

        $post_type = $this->get_post_type_slug();
        add_filter( "theme_{$post_type}_templates", [ $this, 'add_elementor_page_templates' ], 99, 4 );

        add_action( 'wp', array( $this, 'parse_request_for_landing' ), - 1 );

        add_action( 'admin_enqueue_scripts', array( $this, 'maybe_register_breadcrumb_nodes' ), 88 );
        add_action( 'wp_ajax_wffn_lp_global_settings_update', array( $this, 'update_global_settings' ) );
        add_action( 'wp_ajax_wffn_lp_custom_settings_update', array( $this, 'update_custom_settings' ) );

        add_action( 'wp_ajax_wffn_lp_save_design', [ $this, 'save_design' ] );
        add_action( 'wp_ajax_wffn_lp_remove_design', [ $this, 'remove_design' ] );
        add_action( 'wp_ajax_wffn_lp_import_template', [ $this, 'import_template' ] );
        add_action( 'wp_ajax_wffn_lp_toggle_state', [ $this, 'toggle_state' ] );
        add_action( 'wp_ajax_wffn_update_landing_page', [ $this, 'update_landing_page' ] );

        add_action( 'wp_enqueue_scripts', array( $this, 'landing_page_frontend_scripts' ), 21 );
        add_action( 'wffn_import_completed', array( $this, 'set_page_template' ), 10, 2 );


        add_filter( 'post_type_link', array( $this, 'post_type_permalinks' ), 10, 2 );
        add_action( 'pre_get_posts', array( $this, 'add_cpt_post_names_to_main_query' ), 20 );
        add_action( 'wp_print_scripts', array( $this, 'no_conflict_mode_script' ), 1000 );
        add_action( 'admin_print_footer_scripts', array( $this, 'no_conflict_mode_script' ), 9 );

    }

    private function process_url() {
        if ( isset( $_REQUEST['page'] ) && 'wf-lp' === $_REQUEST['page'] && isset( $_REQUEST['edit'] ) && $_REQUEST['edit'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
                    'type'               => 'view',
                    'import'             => 'no',
                    'show_import_popup'  => 'no',
                    'slug'               => 'wp_editor_1',
                    'build_from_scratch' => true
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
        return 'wffn_landing';
    }

    /**
     * @return WFFN_Landing_Pages|null
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
         * Landing page Post Type
         */

        $bwb_admin_setting = BWF_Admin_General_Settings::get_instance();

        register_post_type( $this->get_post_type_slug(), apply_filters( 'wffn_landing_post_type_args', array(
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
                'slug'       => ( empty( $bwb_admin_setting->get_option( 'landing_page_base' ) ) ? $this->get_post_type_slug() : $bwb_admin_setting->get_option( 'landing_page_base' ) ),
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

    public function landing_page_frontend_scripts() {
        $funnel    = WFFN_Core()->data->get_session_funnel();
        $funnel_id = '';

        if ( wffn_is_valid_funnel( $funnel ) ) {
            $funnel_id = 'funnel id: #' . $funnel->get_id() . ', ';
        }

        if ( $this->is_wflp_page() ) {
            global $post;
            $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
            if ( 'default' === $page_template || empty( $page_template ) ) {
                return;
            }
            WFFN_Core()->logger->log( $funnel_id . 'Sale Page ID: #' . $post->ID . ' sales page load scripts' );
            wp_enqueue_style( 'wffn-frontend-style' );
        }
    }

    /**
     * Checks whether its our page or not
     * @return bool
     */
    public function is_wflp_page() {
        return $this->wflp_is_landing;
    }

    /**
     * Set wfty_is_thankyou flag if it's our page
     * @return void
     */
    public function parse_request_for_landing() {
        global $post;

        if ( is_null( $post ) || ! $post instanceof WP_Post ) {
            return;
        }

        $funnel = WFFN_Core()->data->get_session_funnel();

        if ( is_singular( $post->post_type ) && ( $this->get_post_type_slug() === $post->post_type ) ) {

            if ( wffn_is_valid_funnel( $funnel ) ) {
                WFFN_Core()->logger->log( "Funnel id: #" . $funnel->get_id() . " parse request for sales page" );
            }

            $this->wflp_is_landing = true;
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

    public function setup_options() {
        $db_options    = get_option( 'wffn_lp_settings', [] );
        $db_options    = ( ! empty( $db_options ) && is_array( $db_options ) ) ? array_map( 'html_entity_decode', $db_options ) : array();
        $this->options = wp_parse_args( $db_options, $this->default_global_settings() );

        return $this->options;
    }


    public function default_global_settings() {
        return array(
            'css'    => '',
            'script' => '',
        );
    }

    public function default_custom_settings() {
        return array(
            'custom_css' => '',
            'custom_js'  => '',
        );
    }

    /**
     * Copy data from old checkout page to new checkout page
     *
     * @param $post_id
     *
     * @return int|null|WP_Error
     */
    public function duplicate_landing_page( $landing_page_id ) {

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

        if ( $landing_page_id > 0 ) {
            $landing_page = get_post( $landing_page_id );
            if ( ! is_null( $landing_page ) && ( $landing_page->post_type === $this->get_post_type_slug() || in_array( $landing_page->post_type, $this->get_inherit_supported_post_type(), true ) ) ) {
                $args         = [
                    'post_title'   => $landing_page->post_title . ' - ' . __( 'Copy', 'funnel-builder' ),
                    'post_content' => $landing_page->post_content,
                    'post_name'    => sanitize_title( $landing_page->post_title . ' - ' . __( 'Copy', 'funnel-builder' ) ),
                    'post_type'    => $this->get_post_type_slug(),
                ];
                $duplicate_id = wp_insert_post( $args );
                if ( ! is_wp_error( $duplicate_id ) ) {

                    global $wpdb;

                    $post_meta_all = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$landing_page_id" ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

                    if ( ! empty( $post_meta_all ) ) {
                        $sql_query_selects = [];
                        $sql_query_meta    = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

                        if ( in_array( $landing_page->post_type, $this->get_inherit_supported_post_type(), true ) ) {
                            foreach ( $post_meta_all as $meta_info ) {

                                $meta_key = $meta_info->meta_key;

                                if ( in_array( $meta_key, $exclude_metas, true ) ) {
                                    continue;
                                }
                                if ( strpos( $meta_key, 'wcf-' ) !== false ) {
                                    continue;
                                }

                                /**
                                 * Good to remove slashes before adding
                                 */
                                $meta_value = addslashes( $meta_info->meta_value );
                                if ( $meta_key === '_wp_page_template' ) {
                                    $meta_value = ( strpos( $meta_value, 'cartflows' ) !== false ) ? str_replace( 'cartflows', "wflp", $meta_value ) : $meta_value;
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


                        if ( in_array( $landing_page->post_type, $this->get_inherit_supported_post_type(), true ) ) {
                            $template = [
                                'selected'        => 'wp_editor_1',
                                'selected_type'   => 'wp_editor',
                                'template_active' => 'yes'
                            ];
                            update_post_meta( $duplicate_id, '_wflp_selected_design', $template );
                        }
                        do_action('wffn_step_duplicated',$duplicate_id);
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
    public function get_landing_pages( $term = '' ) {


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
        if ( 'wf-lp' === $page_now ) {
            wp_enqueue_script( 'wffn_lp_js', $this->url . 'assets/js/admin.js', [], time() );
            wp_localize_script( 'wffn_lp_js', 'wflp', $this->localize_data() );
            wp_localize_script( 'wffn_lp_js', 'wflp_localization', $this->localize_text_data() );
        }
    }

    public function localize_data() {
        $data                          = [];
        $design                        = [];
        $data['nonce_save_design']     = wp_create_nonce( 'wffn_lp_save_design' );
        $data['nonce_remove_design']   = wp_create_nonce( 'wffn_lp_remove_design' );
        $data['nonce_import_design']   = wp_create_nonce( 'wffn_lp_import_design' );
        $data['nonce_global_settings'] = wp_create_nonce( 'wffn_lp_global_settings_update' );
        $data['nonce_custom_settings'] = wp_create_nonce( 'wffn_lp_custom_settings_update' );
        $data['nonce_toggle_state']    = wp_create_nonce( 'wffn_lp_toggle_state' );
        $data['wflp_edit_nonce']       = wp_create_nonce( 'wflp_edit_landing' );
        $data['design_template_data']  = $this->design_template_data;
        $data['options']               = $this->get_option();
        $data['custom_options']        = $this->get_custom_option();
        $data['texts']                 = array(
            'settings_success'       => __( 'Changes Saved', 'funnel-builder' ),
            'copy_success'           => __( 'Link copied!', 'funnel-builder' ),
            'shortcode_copy_success' => __( 'Shortcode Copied!', 'funnel-builder' ),
        );

        $data['update_popups']         = array(

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
        $data['global_setting_fields'] = array(
            'legends_texts' => array(
                'global_css'    => __( 'Custom CSS', 'funnel-builder' ),
                'global_script' => __( 'External Scripts', 'funnel-builder' ),
            ),
            'fields'        => array(
                'css'    => array(
                    'label' => __( 'Custom CSS Tweaks', 'funnel-builder' ),
                ),
                'script' => array(
                    'label' => __( 'External JS Scripts', 'funnel-builder' ),
                    'hint'  => __( 'These scripts will be globally embedded in all the landing Pages.', 'funnel-builder' )
                ),
            ),
        );
        $data['custom_setting_fields'] = array(
            'legends_texts' => array(
                'custom_css' => __( 'Custom CSS', 'funnel-builder' ),
                'custom_js'  => __( 'External Scripts', 'funnel-builder' ),
            ),
            'fields'        => array(
                'custom_css' => array(
                    'label'       => __( 'Custom CSS Tweaks', 'funnel-builder' ),
                    'placeholder' => __( 'Paste your CSS code here', 'funnel-builder' ),
                ),
                'custom_js'  => array(
                    'label'       => __( 'Custom JS Tweaks', 'funnel-builder' ),
                    'placeholder' => __( 'Paste your code here', 'funnel-builder' ),
                ),
            ),
        );
        if ( 0 !== $this->edit_id ) {
            $post = get_post( $this->edit_id );

            $data['id']                   = $this->edit_id;
            $data['title']                = $post->post_title;
            $data['lp_title']             = $this->get_module_title();
            $data['status']               = $post->post_status;
            $data['content']              = $post->post_content;
            $data['view_url']             = get_the_permalink( $this->edit_id );
            $data['design_template_data'] = $this->design_template_data;
            $design                       = $this->get_page_design( $this->edit_id );

            $data['update_popups']['values'] = array(
                'title' => $post->post_title,
                'slug'  => $post->post_name,
            );
        }

        $design = array_merge( [
            'designs'         => $this->templates,
            'design_types'    => $this->template_type,
            'template_active' => "yes"
        ], $design, $data );

        return $design;
    }

    public function get_page_design( $page_id ) {
        $design_data = get_post_meta( $page_id, '_wflp_selected_design', true );
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
        $data = [
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

        return $data;
    }

    public function add_elementor_page_templates() {

        $all_templates = wp_get_theme()->get_post_templates();
        $path          = [

            'wflp-boxed.php'  => __( 'Woofunnels Boxed', 'funnel-builder' ),
            'wflp-canvas.php' => __( 'Woofunnels Canvas for Page Builder', 'funnel-builder' )
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
        $wflp_id       = $post->ID;
        $page_template = apply_filters( 'bwf_page_template', get_post_meta( $wflp_id, '_wp_page_template', true ), $wflp_id );

        $file         = '';
        $body_classes = [];

        switch ( $page_template ) {
            case 'wflp-boxed.php':
                $file           = $this->get_module_path() . 'templates/wflp-boxed.php';
                $body_classes[] = $page_template;
                break;

            case 'wflp-canvas.php':
                $file           = $this->get_module_path() . 'templates/wflp-canvas.php';
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
        return plugin_dir_path( WFFN_PLUGIN_FILE ) . 'modules/landing-pages/';
    }

    public function maybe_register_breadcrumb_nodes() {
        if ( WFFN_Core()->admin->is_wffn_flex_page( 'wf-lp' ) ) {
            BWF_Admin_Breadcrumbs::register_node( array(
                'text' => get_the_title( $this->edit_id ),
                'link' => '',
            ) );
        }
    }

    public function load_compatibility() {

        include_once $this->get_module_path() . 'compatibilities/page-builders/elementor/class-wffn-landing-pages-elementor.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
        $this->register_native_templates();
    }

    public function get_edit_id() {
        return $this->edit_id;
    }

    public function update_global_settings() {
        check_admin_referer( 'wffn_lp_global_settings_update', '_nonce' );
        $options = ( isset( $_POST['data'] ) && ( $_POST['data'] ) ) ? wp_unslash( $_POST['data'] ) : 0;   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $resp    = [];
        if ( is_array( $options ) ) {
            $options['css']    = isset( $options['css'] ) ? htmlentities( $options['css'] ) : '';
            $options['script'] = isset( $options['script'] ) ? htmlentities( $options['script'] ) : '';
        }
        $this->update_options( $options );
        $resp['status'] = true;
        $resp['msg']    = __( 'Settings Updated', 'funnel-builder' );
        $resp['data']   = '';
        wp_send_json( $resp );
    }

    public function update_custom_settings() {
        check_admin_referer( 'wffn_lp_custom_settings_update', '_nonce' );
        $options    = ( isset( $_POST['data'] ) && ( $_POST['data'] ) ) ? wp_unslash( $_POST['data'] ) : 0;   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $landing_id = isset( $_POST['landing_id'] ) ? wffn_clean( $_POST['landing_id'] ) : 0;

        $resp = [];
        if ( is_array( $options ) ) {
            $options['custom_css'] = isset( $options['custom_css'] ) ? htmlentities( $options['custom_css'] ) : '';
            $options['custom_js']  = isset( $options['custom_js'] ) ? htmlentities( $options['custom_js'] ) : '';
        }
        update_post_meta( $landing_id, 'wffn_step_custom_settings', $options );
        wp_update_post( get_post( $landing_id ) );
        $resp['status'] = true;
        $resp['msg']    = __( 'Settings Updated', 'funnel-builder' );
        $resp['data']   = '';
        wp_send_json( $resp );
    }

    public function update_options( $options ) {
        update_option( 'wffn_lp_settings', $options, true );
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
        check_ajax_referer( 'wffn_lp_save_design', '_nonce' );
        $wflp_id = isset( $_POST['wflp_id'] ) ? absint( wffn_clean( $_POST['wflp_id'] ) ) : 0;

        if ( $wflp_id > 0 ) {
            $selected_type = isset( $_POST['selected_type'] ) ? wffn_clean( $_POST['selected_type'] ) : '';
            $this->update_page_design( $wflp_id, [
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
        update_post_meta( $page_id, '_wflp_selected_design', $data );

        if ( isset( $data['selected_type'] ) && 'wp_editor' === $data['selected_type'] ) {
            update_post_meta( $page_id, '_wp_page_template', 'wflp-boxed.php' );
        } else {
            update_post_meta( $page_id, '_wp_page_template', 'wflp-canvas.php' );
        }

        return $data;
    }

    public static function send_resp( $data = array() ) {
        if ( ! is_array( $data ) ) {
            $data = [];
        }
        $data['nonce'] = wp_create_nonce( 'wflp_secure_key' );
        wp_send_json( $data );
    }

    public function remove_design() {
        $resp = array(
            'msg'    => '',
            'status' => false,
        );
        check_ajax_referer( 'wffn_lp_remove_design', '_nonce' );
        if ( isset( $_POST['wflp_id'] ) && $_POST['wflp_id'] > 0 ) {
            $wflp_id                     = absint( $_POST['wflp_id'] );
            $template                    = $this->default_design_data();
            $template['template_active'] = 'no';
            $this->update_page_design( $wflp_id, $template );
            do_action( 'wflp_template_removed', $wflp_id );
            do_action( 'woofunnels_module_template_removed', $wflp_id );

            $args = [
                'ID'           => $wflp_id,
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
        check_ajax_referer( 'wffn_lp_import_design', '_nonce' );
        $builder  = isset( $_POST['builder'] ) ? sanitize_text_field( $_POST['builder'] ) : '';
        $template = isset( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : '';
        $wflp_id  = isset( $_POST['wflp_id'] ) ? sanitize_text_field( $_POST['wflp_id'] ) : '';

        $is_builder_active = $this->maybe_activate_plugin( $builder );
        if ( $is_builder_active ) {
            $result = WFFN_Core()->importer->import_remote( $wflp_id, $builder, $template, $this->get_cloud_template_step_slug() );
        }

        if ( true === $result['success'] ) {
            $resp['status'] = true;
            $resp['msg']    = __( 'Importing of template finished', 'funnel-builder' );
        } else {
            $resp['error'] = $result['error'];
        }

        self::send_resp( $resp );
    }

    /**
     *
     * @param $plugin_init
     *
     * @return bool
     */
    public function maybe_activate_plugin( $builder ) {
        $plugin_init = ( 'elementor' === $builder ) ? 'elementor/elementor.php' : '';
        $activated   = false;
        if ( ! empty( $plugin_init ) ) {
            $plugin_status = WFFN_Core()->page_builders->get_plugin_status( $plugin_init );
            if ( 'activate' === $plugin_status ) {
                $activate  = activate_plugin( $plugin_init, '', false, true );
                $activated = ( is_wp_error( $activate ) ) ? $activated : true;
            } elseif ( is_plugin_active( $plugin_init ) ) {
                $activated = true;
            }
        }

        return $activated;
    }

    public function toggle_state() {
        check_ajax_referer( 'wffn_lp_toggle_state', '_nonce' );
        $resp = [
            'status' => false,
            'msg'    => __( 'Unable to change state', 'funnel-builder' ),
        ];

        $state   = isset( $_POST['toggle_state'] ) ? sanitize_text_field( $_POST['toggle_state'] ) : '';
        $wflp_id = isset( $_POST['wflp_id'] ) ? sanitize_text_field( $_POST['wflp_id'] ) : '';

        $status = ( 'true' === $state ) ? 'publish' : 'draft';

        wp_update_post( [ 'ID' => $wflp_id, 'post_status' => $status ] );

        $resp['status'] = true;
        $resp['msg']    = __( 'Status changed successfully', 'funnel-builder' );


        self::send_resp( $resp );
    }

    public function get_cloud_template_step_slug() {
        return 'landing';
    }

    public function update_landing_page() {
        check_ajax_referer( 'wflp_edit_landing', '_nonce' );
        $resp = [
            'status' => false,
            'msg'    => __( 'Unable to change state', 'funnel-builder' ),
            'title'  => '',
        ];

        $data      = isset( $_POST['data'] ) ? wffn_clean( json_decode( wffn_clean( wp_unslash( $_POST['data'] ) ), true ) ) : '';
        $wflp_id   = isset( $_POST['landing_id'] ) ? sanitize_text_field( $_POST['landing_id'] ) : '';
        $post_name = ( isset( $data['slug'] ) && ! empty( $data['slug'] ) ) ? $data['slug'] : $data['title'];

        $updated = wp_update_post( [ 'ID' => $wflp_id, 'post_title' => $data['title'], 'post_name' => sanitize_title( $post_name ) ] );
        if ( absint( $updated ) === absint( $wflp_id ) ) {
            $resp['status'] = true;
            $resp['title']  = $data['title'];
            $resp['msg']    = __( 'Title updated successfully', 'funnel-builder' );
        }
        self::send_resp( $resp );
    }

    public function get_status() {
        $post_lp = get_post( $this->get_edit_id() );

        return $post_lp->post_status;
    }

    public function get_module_title( $plural = false ) {
        return ( $plural ) ? __( 'Sales Pages', 'funnel-builder' ) : __( 'Sales Page', 'funnel-builder' );
    }

    public function set_page_template( $wflp_id, $module ) {
        if ( $this->get_cloud_template_step_slug() !== $module ) {
            return;
        }
        update_post_meta( $wflp_id, '_wp_page_template', 'wflp-boxed.php' );
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
    public function post_type_permalinks( $post_link, $post ) {

        $bwb_admin_setting = BWF_Admin_General_Settings::get_instance();

        if ( isset( $post->post_type ) && $this->get_post_type_slug() === $post->post_type && empty( trim( $bwb_admin_setting->get_option( 'landing_page_base' ) ) ) ) {


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

        // Add landing page step post type to existing post type array.
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
        array_push( $classes, 'wffn-page-template' );

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
            'wf-lp'    => array(
                'wffn_lp_js',
            ),
            'settings' => array(),
        ) );

        $this->no_conflict_mode( $wp_scripts, $wp_required_scripts, $wffn_required_scripts, 'scripts', 'wf-lp' );
    }

    public function get_template_settings() {
        include __DIR__ . '/admin-views/template-settings.php';
    }

    public function get_inherit_supported_post_type() {
        return apply_filters( 'wffn_sp_inherit_supported_post_type', array( 'cartflows_step', 'page' ) );
    }

}

if ( class_exists( 'WFFN_Core' ) ) {
    WFFN_Core::register( 'landing_pages', 'WFFN_Landing_Pages' );
}
