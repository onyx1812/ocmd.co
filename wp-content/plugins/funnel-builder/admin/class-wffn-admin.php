<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class to initiate admin functionalists
 * Class WFFN_Admin
 */
class WFFN_Admin {

    private static $ins = null;
    private $funnel = null;

    /**
     * @var WFFN_Background_Importer $updater
     */
    public $wffn_updater;

    /**
     * @var WFFN_Background_DB_Updater $updater
     */
    public $wffn_db_updater;

    /**
     * WFFN_Admin constructor.
     */
    public function __construct() {

        add_action( 'load-woofunnels_page_bwf_funnels', array( $this, 'setup_funnel' ) );

        /** Admin enqueue scripts*/
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ), 99 );
        add_action( 'admin_enqueue_scripts', array( $this, 'js_variables' ), 0 );
        add_action( 'admin_enqueue_scripts', array( $this, 'maybe_register_breadcrumb_nodes' ), 5 );

        add_action( 'admin_init', array( $this, 'check_db_version' ), 990 );

        add_action( 'wp_print_scripts', array( $this, 'no_conflict_mode_script' ), 1000 );
        add_action( 'admin_print_footer_scripts', array( $this, 'no_conflict_mode_script' ), 9 );
        add_action( 'admin_init', array( $this, 'maybe_create_starter_steps' ) );
        add_action( 'admin_init', array( $this, 'reset_wizard' ) );

        add_action( 'admin_head', array( $this, 'hide_from_menu' ) );
        add_action( 'in_admin_header', array( $this, 'maybe_remove_all_notices_on_page' ) );


        add_filter( 'get_pages', array( $this, 'add_landing_in_home_pages' ), 10, 2 );
        add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );

        add_action( 'admin_notices', array( $this, 'maybe_show_notices' ) );
        add_filter( 'plugin_action_links_' . WFFN_PLUGIN_BASENAME, array( $this, 'plugin_actions' ) );

        /** Initiate Background updater if action scheduler is not available for template importing */
        add_action( 'init', array( $this, 'wffn_maybe_init_background_updater' ), 110 );
        add_filter( 'bwf_general_settings_link', function () {
            return admin_url( 'admin.php?page=bwf_funnels&section=bwf_settings' );
        } );
        add_filter( 'woofunnels_show_reset_tracking', '__return_true', 999 );
        add_action( 'admin_head', array( $this, 'menu_highlight' ), 99999 );
        add_action( 'pre_get_posts', [ $this, 'load_page_to_home_page' ], 9999 );
	    add_action( 'admin_init', [ $this, 'maybe_delete_all_funnels' ]);
        add_action( 'admin_head', array( $this, 'maybe_update_database_update' ) );


    }


    /**
     * @return WFFN_Admin|null
     */
    public static function get_instance() {
        if ( null === self::$ins ) {
            self::$ins = new self();
        }

        return self::$ins;
    }

    public function register_admin_menu() {
        $steps = WFFN_Core()->steps->get_supported_steps();
        if ( count( $steps ) < 1 ) {
            return;
        }
        add_submenu_page( 'woofunnels', __( 'Dashboard', 'funnel-builder' ), __( 'Dashboard', 'funnel-builder' ), 'manage_options', 'bwf_dashboard', array(
            $this,
            'bwf_dashboard',
        ) );

        add_submenu_page( 'woofunnels', __( 'Funnels', 'funnel-builder' ), __( 'Funnels', 'funnel-builder' ), 'manage_options', 'bwf_funnels', array(
            $this,
            'bwf_funnels_funnels',
        ) );
    }



    public function bwf_dashboard() {
        include_once WFFN_PLUGIN_DIR . '/admin/views/funnel-dashboard.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
    }

    public function bwf_funnels_funnels() {
        $wffn_page    = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
        $wffn_section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
        $view_loaded  = apply_filters( 'wffn_admin_view_loaded', false, $wffn_page, $wffn_section );
        if ( ! $view_loaded && 'bwf_funnels' === $wffn_page ) {
            if ( ! empty( $wffn_section ) ) {
                if ( 'new' === $wffn_section ) {
                    include_once WFFN_PLUGIN_DIR . '/admin/views/flex-funnel-new.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
                } elseif ( 'import' === $wffn_section ) {
                    include_once WFFN_PLUGIN_DIR . '/admin/views/flex-import.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
                } elseif ( 'funnel' === $wffn_section ) {
                    include_once WFFN_PLUGIN_DIR . '/admin/views/flex-funnel-view.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
                } elseif ( 'bwf_settings' === $wffn_section ) {
                    BWF_Admin_General_Settings::get_instance()->__callback();
                } else {
                    include_once WFFN_PLUGIN_DIR . '/admin/views/flex-export.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
                }
            } else {
                include_once WFFN_PLUGIN_DIR . '/admin/views/funnels-listing-view.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
            }
        }
    }


    public function admin_enqueue_assets() {
        wp_enqueue_style( 'bwf-admin-font', $this->get_admin_url() . '/assets/css/bwf-admin-font.css', array(), WFFN_VERSION_DEV );

        if ( 'bwf_dashboard' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) {
            $this->load_react_app( 'dashboard' );
        }

        if ( $this->is_wffn_flex_page() ) {

            wp_enqueue_style( 'wffn-admin-swal', $this->get_admin_url() . '/assets/css/sweetalert2.css', array(), WFFN_VERSION_DEV );
            wp_enqueue_style( 'wffn-flex-admin', $this->get_admin_url() . '/assets/css/admin.css', array(), WFFN_VERSION_DEV );
            wp_enqueue_style( 'wffn-izimodal-style', $this->get_admin_url() . '/assets/iziModal/izimodal.css', array(), WFFN_VERSION_DEV );

            if ( false === $this->is_wffn_flex_page( 'funnel' ) ) {
                wp_enqueue_script( 'updates' );

                wp_enqueue_script( 'wffn-admin-ajax', $this->get_admin_url() . '/assets/js/wffn-ajax.js', [], WFFN_VERSION_DEV );
                wp_enqueue_script( 'wffn-admin-scripts', $this->get_admin_url() . '/assets/js/wffn-admin.js', array( 'underscore' ), WFFN_VERSION_DEV, true );
                wp_enqueue_script( 'wffn-izimodal-scripts', $this->get_admin_url() . '/assets/iziModal/iziModal.js', array(), WFFN_VERSION_DEV, true );
                wp_enqueue_script( 'wffn-sweetalert', $this->get_admin_url() . '/assets/js/wffn-sweetalert.min.js', array(), WFFN_VERSION_DEV, true );

                /**
                 * Including vuejs assets
                 */
                wp_enqueue_style( 'wffn-vue-multiselect', $this->get_admin_url() . '/assets/vuejs/vue-multiselect.min.css', array(), WFFN_VERSION_DEV );

                wp_enqueue_script( 'wffn-vuejs', $this->get_admin_url() . '/assets/vuejs/vue.min.js', array(), '2.6.10' );
                wp_enqueue_script( 'wffn-vue-vfg', $this->get_admin_url() . '/assets/vuejs/vfg.min.js', array(), '2.3.4' );
                wp_enqueue_script( 'wffn-vue-multiselect', $this->get_admin_url() . '/assets/vuejs/vue-multiselect.min.js', array(), WFFN_VERSION_DEV );
            }
            /**
             * Include Analytics assets
             */
            if ( WFFN_Core()->admin->is_wffn_flex_page( 'bwf_settings' ) ) {

                BWF_Admin_General_Settings::get_instance()->maybe_add_js();
            }

            if ( WFFN_Core()->admin->is_wffn_flex_page( 'wf-op' ) ) {
                wp_enqueue_style( 'jquery-ui' );
                wp_enqueue_script( 'jquery-ui-sortable' );
            }

            if ( WFFN_Core()->admin->is_wffn_flex_page( 'funnel' ) ) {
                $this->load_react_app( 'main' );
            }

            $data = array(
                'ajax_nonce_get_funnel_step_designs' => wp_create_nonce( 'wffn_get_funnel_step_designs' ),
                'ajax_nonce_add_step'                => wp_create_nonce( 'wffn_add_step' ),
                'ajax_nonce_duplicate_step'          => wp_create_nonce( 'wffn_duplicate_step' ),
                'ajax_nonce_add_substep'             => wp_create_nonce( 'wffn_add_substep' ),
                'ajax_nonce_delete_step'             => wp_create_nonce( 'wffn_delete_step' ),
                'ajax_nonce_delete_substep'          => wp_create_nonce( 'wffn_delete_substep' ),
                'ajax_nonce_add_funnel'              => wp_create_nonce( 'wffn_add_funnel' ),
                'ajax_nonce_duplicate_funnel'        => wp_create_nonce( 'ajax_nonce_duplicate_funnel' ),
                'ajax_nonce_delete_funnel'           => wp_create_nonce( 'wffn_delete_funnel' ),
                'ajax_nonce_update_funnel'           => wp_create_nonce( 'wffn_update_funnel' ),
                'ajax_nonce_switch_step_status'      => wp_create_nonce( 'wffn_switch_step_status' ),
                'ajax_nonce_switch_substep_status'   => wp_create_nonce( 'wffn_switch_substep_status' ),
                'ajax_nonce_reposition_substeps'     => wp_create_nonce( 'wffn_reposition_substeps' ),
            );
            $data = apply_filters( 'wffn_localized_nonces_data', $data );
            wp_localize_script( 'wffn-admin-scripts', 'wffnParams', $data );
            do_action( 'wffn_admin_assets', $this );
        }
    }

    public function load_react_app( $app_name = 'main' ) {
        $min               = 60 * get_option( 'gmt_offset' );
        $sign              = $min < 0 ? "-" : "+";
        $absmin            = abs( $min );
        $tz                = sprintf( "%s%02d:%02d", $sign, $absmin / 60, $absmin % 60 );
        $contact_page_data = array(
            'is_wc_active' => false,
            'date_format'  => get_option( 'date_format', 'F j, Y' ) . ' ' . get_option( 'time_format', 'g:i a' ),
            'is_pro'       => defined( 'WFFN_PRO_VERSION' ),
            'app_path'     => WFFN_Core()->get_plugin_url() . '/admin/views/contact/dist/',
            'timezone'     => $tz,
        );
        if ( class_exists( 'WooCommerce' ) ) {
            $currency                          = get_woocommerce_currency();
            $contact_page_data['currency']     = [
                'code'              => $currency,
                'precision'         => wc_get_price_decimals(),
                'symbol'            => html_entity_decode( get_woocommerce_currency_symbol( $currency ) ),
                'symbolPosition'    => get_option( 'woocommerce_currency_pos' ),
                'decimalSeparator'  => wc_get_price_decimal_separator(),
                'thousandSeparator' => wc_get_price_thousand_separator(),
                'priceFormat'       => html_entity_decode( get_woocommerce_price_format() ),
            ];
            $contact_page_data['is_wc_active'] = true;
            $contact_page_data['admin_url']    = esc_url( $this->get_admin_url() );
        }
        $frontend_dir = ( 0 === WFFN_REACT_ENVIRONMENT ) ? WFFN_REACT_DEV_URL : WFFN_Core()->get_plugin_url() . '/admin/views/contact/dist/';
        if ( class_exists( 'WooCommerce' ) ) {
            wp_dequeue_style( 'woocommerce_admin_styles' );
            wp_dequeue_style( 'wc-components' );
        }

        $assets_path = 1 === WFFN_REACT_ENVIRONMENT ? WFFN_PLUGIN_DIR . "/admin/views/contact/dist/$app_name.asset.php" : $frontend_dir . "/$app_name.asset.php";
        $assets      = file_exists( $assets_path ) ? include $assets_path : array(
            'dependencies' => array(
                'lodash',
                'moment',
                'react',
                'react-dom',
                'wp-api-fetch',
                'wp-components',
                'wp-compose',
                'wp-date',
                'wp-deprecated',
                'wp-dom',
                'wp-element',
                'wp-hooks',
                'wp-html-entities',
                'wp-i18n',
                'wp-keycodes',
                'wp-polyfill',
                'wp-primitives',
                'wp-url',
                'wp-viewport',
            ),
            'version'      => time(),
        );
        $deps        = ( isset( $assets['dependencies'] ) ? array_merge( $assets['dependencies'], array( 'jquery' ) ) : array( 'jquery' ) );
        $version     = $assets['version'];

        $script_deps = array_filter( $deps, function ( $dep ) {
            return false === strpos( $dep, 'css' );
        } );

        wp_enqueue_style( 'wp-components' );
        wp_enqueue_style( 'wffn_material_icons', 'https://fonts.googleapis.com/icon?family=Material+Icons+Outlined' );
        wp_enqueue_style( 'wffn-contact-admin', $frontend_dir . "/$app_name.css", array(), $version );

        wp_register_script( 'wffn-contact-admin', $frontend_dir . "/$app_name.js", $script_deps, $version, true );
        wp_localize_script( 'wffn-contact-admin', 'wffn_contacts_data', $contact_page_data );
        wp_enqueue_script( 'wffn-contact-admin' );
    }

    public function get_admin_url() {
        return WFFN_Core()->get_plugin_url() . '/admin';
    }

    public function get_admin_path() {
        return WFFN_PLUGIN_DIR . '/admin';
    }

    /**
     * @param string $section
     *
     * @return bool
     */
    public function is_wffn_flex_page( $section = '' ) {

        if ( isset( $_GET['page'] ) && $_GET['page'] === 'bwf_funnels' && 'list' === $section && null === filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'bwf_funnels' && ( '' === $section || 'all' === $section ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }

        if ( isset( $_GET['page'] ) && $_GET['page'] === 'bwf_funnels' && isset( $_GET['section'] ) && $_GET['section'] === $section ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }

        if ( ( isset( $_GET['page'] ) && $_GET['page'] === 'wf-lp' ) && ( $section === '' || $section === 'wf-lp' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }

        if ( ( isset( $_GET['page'] ) && $_GET['page'] === 'wf-op' ) && ( $section === '' || $section === 'wf-op' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }

        if ( ( isset( $_GET['page'] ) && $_GET['page'] === 'wf-oty' ) && ( $section === '' || $section === 'wf-oty' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }

        if ( ( isset( $_GET['page'] ) && $_GET['page'] === 'wf-ty' ) && ( $section === '' || $section === 'wf-ty' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }

        if ( ( isset( $_GET['page'] ) && $_GET['page'] === 'woofunnels-settings' ) && ( $section === '' || $section === 'woofunnels-settings' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return true;
        }

        return false;
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

        $wp_required_scripts   = array( 'admin-bar', 'common', 'jquery-color', 'utils', 'svg-painter', 'updates' );
        $wffn_required_scripts = apply_filters( 'wffn_no_conflict_scripts', array(
            'common'       => array(
                'wffn-admin-ajax',
                'wffn-izimodal-scripts',
                'wffn-sweetalert',
                'wffn-vuejs',
                'wffn-vue-vfg',
                'wffn-vue-multiselect',
                'wffn-admin-scripts',
            ),
            'steps'        => array(
                'wffn-sortable-js',
                'wffn-vue-sortable-admin',
            ),
            'funnel'       => array(
                'wffn-select2-js',
                'wffn-contact-admin',
            ),
            'wf-lp'        => array(
                'wffn_lp_js',
            ),
            'wf-ty'        => array(
                'wffn_tp_js',
            ),
            'bwf_settings' => array( 'bwf-general-settings' ),
        ) );
        $this->no_conflict_mode( $wp_scripts, $wp_required_scripts, $wffn_required_scripts, 'scripts' );
    }

    /**
     * Runs "no conflict mode".
     *
     * @param $wp_objects
     * @param $wp_required_objects
     * @param $wffn_required_scripts
     * @param string $type
     */
    public function no_conflict_mode( &$wp_objects, $wp_required_objects, $wffn_required_scripts, $type = 'scripts' ) {

        $current_page = trim( strtolower( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) );

        if ( 'bwf_funnels' !== $current_page ) {
            return;
        }

        $section      = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
        $page_objects = isset( $wffn_required_scripts[ $section ] ) ? $wffn_required_scripts[ $section ] : array();

        //disable no-conflict if $page_objects is false
        if ( $page_objects === false ) {
            return;
        }

        if ( ! is_array( $page_objects ) ) {
            $page_objects = array();
        }

        //merging wp scripts with wffn scripts
        $required_objects = array_merge( $wp_required_objects, $wffn_required_scripts['common'], $page_objects );

        //allowing addons or other products to change the list of no conflict scripts
        $required_objects = apply_filters( "wffn_noconflict_{$type}", $required_objects );

        $queue = array();
        foreach ( $wp_objects->queue as $object ) {
            if ( in_array( $object, $required_objects, true ) ) {
                $queue[] = $object;
            }
        }
        $wp_objects->queue = $queue;

        $required_objects = $this->add_script_dependencies( $wp_objects->registered, $required_objects );

        //unregistering scripts
        $registered = array();
        foreach ( $wp_objects->registered as $script_name => $script_registration ) {
            if ( in_array( $script_name, $required_objects, true ) ) {
                $registered[ $script_name ] = $script_registration;
            }
        }

        $wp_objects->registered = $registered;
    }

    /**
     * Adds script dependencies needed.
     *
     * @param $registered
     * @param $scripts
     *
     * @return array
     */
    public function add_script_dependencies( $registered, $scripts ) {

        //gets all dependent scripts linked to the $scripts array passed
        do {
            $dependents = array();
            foreach ( $scripts as $script ) {
                $deps = isset( $registered[ $script ] ) && is_array( $registered[ $script ]->deps ) ? $registered[ $script ]->deps : array();
                foreach ( $deps as $dep ) {
                    if ( ! in_array( $dep, $scripts, true ) && ! in_array( $dep, $dependents, true ) ) {
                        $dependents[] = $dep;
                    }
                }
            }
            $scripts = array_merge( $scripts, $dependents );
        } while ( ! empty( $dependents ) );

        return $scripts;
    }


    public function js_variables() {
        if ( $this->is_wffn_flex_page() ) {
            $steps_data               = WFFN_Common::get_steps_data();
            $substeps_data            = WFFN_Common::get_substeps_data();
            $substeps_data['substep'] = true;

            $funnel_data      = WFFN_Common::get_funnel_data();
            $funnel_delete    = WFFN_Common::get_funnel_delete_data();
            $funnel_duplicate = WFFN_Common::get_funnel_duplicate_data();

            $success_popups                      = WFFN_Common::get_success_popups();
            $success_popups['funnel_duplicated'] = __( 'Funnel successfully duplicated.', 'woofunnel-flex-funnels' );
            $success_popups['funnel_deleted']    = __( 'Funnel successfully deleted.', 'woofunnel-flex-funnels' );

            $success_popups['popup_type'] = 'info';
            $success_popups['subtitle']   = '';
            $success_popups['duplicated'] = __( 'Step successfully duplicated.', 'funnel-builder' );

            $loader_popups = WFFN_Common::get_loader_popups();

            $loader_popups['popup_type']                = 'loader';
            $loader_popups['subtitle']                  = __( 'Please wait it may take couple of moments...', 'funnel-builder' );
            $loader_popups['duplicate']['title']        = __( 'Duplicating the step', 'funnel-builder' );
            $loader_popups['duplicate_funnel']['title'] = __( 'Duplicating the step', 'funnel-builder' );

            $funnel    = $this->get_funnel();
            $funnel_id = $funnel->get_id();

            BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel_id );

            $funnel_steps  = $funnel->get_steps( true );
            $current_state = count( $funnel_steps ) > 0 ? 'second' : 'first';
            $upsell_exist  = function_exists( 'WFOCU_Core' );

            $isWelcome = ( ( $funnel_id !== 0 ) && ( is_array( $funnel_steps ) && count( $funnel_steps ) === 0 ) ) ? false : true;

            $data = array(
                'funnel_id'      => $funnel_id,
                'funnel_steps'   => $funnel_steps,
                'current_state'  => $current_state,
                'steps_data'     => $steps_data,
                'substeps'       => $substeps_data,
                'success_popups' => $success_popups,
                'loader_popups'  => $loader_popups,
                'upsell_exist'   => $upsell_exist,
                'isWelcome'      => $isWelcome,
                'icons'          => array(
                    'error_cross'   => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2" class="wffn_loader wffn_loader_error">
                        <circle fill="none" stroke="#ffb7bf" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" class="path circle"></circle>
                        <line fill="none" stroke="#e64155" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" class="path line"></line>
                        <line fill="none" stroke="#e64155" stroke-width="8" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2" class="path line"></line>
                    </svg>',
                    'success_check' => '<svg class="wffn_loader wffn_loader_ok" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                <circle class="path circle" fill="none" stroke="#baeac5" stroke-width="5" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"></circle>
                                <polyline class="path check" fill="none" stroke="#39c359" stroke-width="9" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "></polyline>
                            </svg>',
                    'delete_alert'  => '<div class="swal2-header wf_funnel-icon-without-swal"><div class="swal2-icon swal2-warning swal2-animate-warning-icon" style="display: flex;"><span class="swal2-icon-text">!</span></div></div>',
                ),
                'images'         => array(
                    'readiness_loader' => esc_url( $this->get_admin_url() ) . '/assets/img/readiness-loader.gif',
                    'check'            => esc_url( $this->get_admin_url() ) . '/assets/img/check.png',
                ),

                'duplicate_funnel_popup' => array(
                    'popup_type' => 'loader',
                    'title'      => __( 'Please wait while we duplicating your funnel', 'woofunnel-flex-funnels' ),
                    'submit_btn' => __( 'Yes, Duplicate', 'woofunnel-flex-funnels' ),
                    'funnel'     => $funnel_duplicate,
                    'substeps'   => array(
                        'title' => __( 'Are you sure you want to duplicate this {{SUBSTEP_TITLE}}?', 'woofunnel-flex-funnels' ),
                    ),
                ),

                'delete_popup' => array(
                    'popup_type' => 'alert',
                    'title'      => __( 'Are you sure you want to delete this Step?', 'funnel-builder' ),
                    'subtitle'   => __( 'It will also delete the analytics data associated with the step, Please disable the step if you want to keep the data.', 'funnel-builder' ),
                    'submit_btn' => __( 'Yes, Delete', 'funnel-builder' ),
                    'funnel'     => $funnel_delete,
                    'substeps'   => array(
                        'title' => __( 'Are you sure you want to delete this {{SUBSTEP_TITLE}}?', 'funnel-builder' ),

                    ),
                ),

                'add_step_form'         => array(
                    'popup_type'           => 'add_form',
                    'submit_btn'           => __( 'Create', 'funnel-builder' ),
                    'default_design_model' => array(
                        'title'       => '',
                        'design'      => 'scratch',
                        'design_name' => '',
                        'allDesigns'  => [],
                    ),
                    'not_found'            => __( 'Oops! No elements found. Consider changing the search query.', 'funnel-builder' ),
                    'label_texts'          => array(
                        'title'  => array(
                            'label'       => __( 'Title', 'funnel-builder' ),
                            'placeholder' => __( 'Enter Name', 'funnel-builder' ),
                        ),
                        'design' => array(
                            'values' => array(
                                array(
                                    'name'  => __( 'Create From Scratch', 'funnel-builder' ),
                                    'value' => 'scratch',
                                ),
                                array(
                                    'name'  => __( 'Copy from existing', 'funnel-builder' ),
                                    'value' => 'existing',
                                    // Search and use existing page design
                                ),
                            ),
                        ),
                    ),
                ),
                'choose_step_popup'     => array(
                    'popup_type'  => 'choose_step_popup',
                    'popup_title' => __( 'Select Step', 'funnel-builder' ),
                    'submit_btn'  => __( 'Continue', 'funnel-builder' ),
                ),
                'update_funnel'         => array(
                    'submit_btn'  => __( 'Update', 'funnel-builder' ),
                    'label_texts' => array(
                        'title' => array(
                            'label'       => __( 'Name', 'funnel-builder' ),
                            'placeholder' => __( 'Enter Name', 'funnel-builder' ),
                            'value'       => $funnel->get_title(),
                        ),
                        'desc'  => array(
                            'label'       => __( 'Description (optional)', 'funnel-builder' ),
                            'placeholder' => __( 'Enter Description (Optional)', 'funnel-builder' ),
                            'value'       => $funnel->get_desc(),
                        ),
                    ),
                ),
                'view_link'             => $funnel->get_view_link(),
                'funnel_home_link'      => admin_url( 'admin.php?page=bwf_funnels' ),
                'flex_links'            => array(
                    'edit'      => __( 'Edit', 'funnel-builder' ),
                    'view'      => __( 'View', 'funnel-builder' ),
                    'duplicate' => __( 'Duplicate', 'funnel-builder' ),
                    'delete'    => __( 'Delete', 'funnel-builder' ),
                ),
                'openReOrderSteps'      => array(
                    'popup_type'   => 'loader',
                    'title'        => 'Re-Ordering Steps...',
                    'afterSuccess' => array(
                        'popup_type' => 'info',
                        'title'      => 'Reordered Successfully',
                    ),
                    'subtitle'     => '',
                    'popup_title'  => __( 'Reorder Steps', 'funnel-builder' ),
                    'submit_btn'   => __( 'Continue', 'funnel-builder' ),
                ),
                'openEyeIcon'           => array(
                    'popup_type'   => 'loader',
                    'title'        => 'Getting Data...',
                    'afterSuccess' => array(
                        'popup_type' => 'info',
                        'title'      => 'Reordered Successfully',
                    ),
                    'modal'        => array(
                        'width' => 800,
                    ),
                    'subtitle'     => '',
                    'popup_title'  => __( 'Reorder Steps', 'funnel-builder' ),
                    'submit_btn'   => __( 'Continue', 'funnel-builder' ),
                ),
                'accordion_placeholder' => '<div class="top wffn-loading"><img src="' . esc_url( $this->get_admin_url() ) . '/assets/img/readiness-loader.gif"/></div>',
                'flexes'                => array(
                    'current_state' => 'second',
                    'funnel_data'   => $funnel_data,
                ),
            );

            $data['templates']               = $this->get_all_templates();
            $data['filters']                 = $this->get_template_filter();
            $data['currentStepsFilter']      = 'all';
            $data['templates_types']         = json_decode( '{"elementor":"Elementor","custom":"Custom"}' );
            $data['default_template_type']   = 'elementor';
            $data['nonce_import_design']     = wp_create_nonce( 'wffn_import_design' );
            $data['nonce_activate_plugin']   = wp_create_nonce( 'wffn_activate_plugin' );
            $data['nonce_get_import_status'] = wp_create_nonce( 'wffn_get_import_status' );

            $data['texts'] = array(
                'settings_success' => __( 'Changes saved', 'funnel-builder' ),
                'copy_success'     => __( 'Link copied!', 'funnel-builder' ),
            );

            $data['importer'] = [
                'activate_template' => [
                    'heading'     => __( 'Are you sure you want to Activate this funnel?', 'funnel-builder' ),
                    'sub_heading' => '',
                    'button_text' => __( 'Yes, activate this funnel!', 'funnel-builder' ),
                ],
                'add_template'      => [
                    'heading'     => __( 'Are you sure you want to import this funnels?', 'funnel-builder' ),
                    'sub_heading' => '',
                    'button_text' => __( 'Yes, Import this funnel!', 'funnel-builder' ),
                ],
            ];
            $data['i18n']     = [
                'plugin_activate' => __( 'Activating plugin...', 'funnel-builder' ),
                'plugin_install'  => __( 'Installing plugin...', 'funnel-builder' ),
                'preparingsteps'  => __( 'Preparing steps...', 'funnel-builder' ),
                'redirecting'     => __( 'Redirecting...', 'funnel-builder' ),
                'importing'       => __( 'Importing...', 'funnel-builder' ),
                'custom_import'   => __( 'Setting up your funnel...', 'funnel-builder' ),
                'ribbons'         => array(
                    'lite' => __( 'Lite', 'funnel-builder' ),
                    'pro'  => __( 'PRO', 'funnel-builder' )
                ),
            ];
            if ( wffn_is_wc_active() && false === $upsell_exist ) {
                $data['wc_upsells'] = [
                    'type'      => 'wc_upsells',
                    'group'     => WFFN_Steps::STEP_GROUP_WC,
                    'title'     => __( 'One Click Upsells', 'funnel-builder' ),
                    'desc'      => __( 'Deploy post purchase one click upsells to increase average order value', 'funnel-builder' ),
                    'dashicons' => 'dashicons-tag',
                    'lock_img'  => esc_url( $this->get_admin_url() ) . '/assets/img/lock.png',
                    'pro'       => true,
                ];
            }
            if ( ! $this->is_wffn_flex_page( 'list' ) ) {

                $data['pageBuildersTexts']   = WFFN_Core()->page_builders->localize_page_builder_texts();
                $data['pageBuildersOptions'] = WFFN_Core()->page_builders->get_plugins_groupby_page_builders();
            }
            ?>
			<script>window.wffn = <?php echo wp_json_encode( apply_filters( 'wffn_localize_admin', $data ) ); ?></script>
            <?php
        }
    }

    /**
     * Get the already setup funnel object
     * @return WFFN_Funnel
     */
    public function get_funnel( $funnel_id = 0 ) {
        if ( $funnel_id > 0 ) {
            if ( $this->funnel instanceof WFFN_Funnel && $funnel_id === $this->funnel->get_id() ) {
                return $this->funnel;
            }
            $this->initiate_funnel( $funnel_id );
        }
        if ( $this->funnel instanceof WFFN_Funnel ) {
            return $this->funnel;
        }
        $this->funnel = new WFFN_Funnel( $funnel_id );

        return $this->funnel;
    }

    /**
     * @param $funnel_id
     */
    public function initiate_funnel( $funnel_id ) {
        if ( ! empty( $funnel_id ) ) {
            $this->funnel = new WFFN_Funnel( $funnel_id, true );
            /**
             * IF we do not have any funnel set against this ID then die here
             */
            if ( empty( $this->funnel->get_id() ) ) {
                wp_die( esc_html__( 'No funnel exist with this id.', 'funnel-builder' ) );
            }
        }
    }

    public function get_all_templates() {
        $templates = WooFunnels_Dashboard::get_all_templates();
        $json_data = isset( $templates['funnel'] ) ? $templates['funnel'] : [];
        foreach ( $json_data as &$templates ) {
            if ( is_array( $templates ) ) {
                foreach ( $templates as $k => &$temp_val ) {
                    if ( isset( $temp_val['pro'] ) && 'yes' === $temp_val['pro'] ) {
                        $temp_val['license_exist'] = WFFN_Core()->admin->get_license_status();

                        /**
                         * Check if template is set to replace lite template
                         * if yes and license exists then replace lite, otherwise keep lite and unset pro
                         */
                        if ( isset( $temp_val['replace_to'] ) ) {
                            if ( false === $temp_val['license_exist'] ) {
                                unset( $templates[ $k ] );
                            } else {
                                unset( $templates[ $temp_val['replace_to'] ] );
                            }
                        }

                    }
                }
            }
        }

        $designs = [
            'custom' => [
                'custom_1' => [
                    'type'               => 'view',
                    'import'             => 'no',
                    'show_import_popup'  => 'no',
                    'slug'               => 'custom_1',
                    'build_from_scratch' => true,
                    "group"              => [ "sales", "optin" ]
                ],
            ],
        ];

        return array_merge( $json_data, $designs );

    }

    public static function get_template_filter() {

        $options = [
            'all'   => __( 'All', 'funnel-builder' ),
            'sales' => __( 'Sales', 'funnel-builder' ),
            'optin' => __( 'Optin', 'funnel-builder' ),
        ];

        return $options;
    }

    public function get_template_nice_name_by( $template, $template_group ) {
        $get_all = $this->get_all_templates();

        if ( ! isset( $get_all[ $template_group ] ) ) {
            return '';
        }
        if ( ! isset( $get_all[ $template_group ][ $template ] ) ) {
            return '';
        }

        return $get_all[ $template_group ][ $template ]['name'];

    }

    public function get_license_status() {
        $license_key    = WFFN_Core()->remote_importer->get_license_key();
        $license_status = empty( $license_key ) ? false : true;

        return apply_filters( 'wffn_check_license_status', $license_status );
    }

    /**
     * setup the funnel object as property if required
     */
    public function setup_funnel() {
        $funnel_id = filter_input( INPUT_GET, 'edit', FILTER_SANITIZE_NUMBER_INT );
        $this->initiate_funnel( $funnel_id );
    }

    /**
     * @hooked over `admin_enqueue_scripts`
     * Check the environment and register appropiate node for the breadcrumb to process
     * @since 1.0.0
     */
    public function maybe_register_breadcrumb_nodes() {
        $single_link = '';
        $funnel = null;
        /**
         * IF its experiment builder UI
         */
        if ( $this->is_wffn_flex_page( 'all' ) ) {

            $funnel = $this->get_funnel();

        } else {

            /**
             * its its a page where experiment page is a referrer
             */
            $get_ref = filter_input( INPUT_GET, 'wffn_funnel_ref', FILTER_SANITIZE_STRING );
	        $get_ref = apply_filters('maybe_setup_funnel_for_breadcrumb', $get_ref);
            if ( ! empty( $get_ref ) ) {
                $funnel      = $this->get_funnel( $get_ref );
                $single_link = WFFN_Common::get_funnel_edit_link( $funnel->get_id() );
            }

        }

        /**
         * Register nodes
         */
        if ( ! empty( $funnel ) ) {

            BWF_Admin_Breadcrumbs::register_node( array(
                'text' => __( 'Funnels' ),
                'link' => admin_url( 'admin.php?page=bwf_funnels' ),
            ) );

            BWF_Admin_Breadcrumbs::register_node( array(
                'text' => $funnel->get_title(),
                'link' => $single_link,
            ) );
            BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel->get_id() );

        }
    }

    public function get_tab_link( $tab ) {
        return $tab['link'];
    }

    public function get_date_format() {
        return get_option( 'date_format', '' ) . ' ' . get_option( 'time_format', '' );
    }

    /**
     * @return array
     */
    public function get_funnels() {
        $paged = isset( $_GET['paged'] ) ? absint( wffn_clean( $_GET['paged'] ) ) : 0;  // phpcs:ignore WordPress.Security.NonceVerification

        $limit = $this->posts_per_page();

        $search_str = isset( $_REQUEST['s'] ) ? wffn_clean( $_REQUEST['s'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

        $status = isset( $_REQUEST['status'] ) ? wffn_clean( $_REQUEST['status'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

        $sql_query = 'SELECT {table_name}.id as funnel_id FROM {table_name}';
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {

            if ( ICL_LANGUAGE_CODE === 'all' ) {
                $sql_query .= ' LEFT JOIN {table_name_meta} ON ( {table_name}.id = {table_name_meta}.bwf_funnel_id AND {table_name_meta}.meta_key = \'_lang\' )';
            } else {
                $sql_query .= ' INNER JOIN {table_name_meta} ON ( {table_name}.id = {table_name_meta}.bwf_funnel_id )';
            }
        }
        $sql_query .= ' WHERE 1=1';

        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            if ( ICL_LANGUAGE_CODE === 'all' ) {
                $sql_query .= ' AND ({table_name_meta}.bwf_funnel_id IS NULL)';
            } else {
                $sql_query .= ' AND ( {table_name_meta}.meta_key = \'_lang\' AND {table_name_meta}.meta_value = \'' . ICL_LANGUAGE_CODE . '\' )';
            }
        }

        if ( ! empty( $status ) && 'all' !== $status ) {
            $status    = ( 'live' === $status ) ? 1 : 0;
            $sql_query .= ' AND `status` = ' . "'$status'";
        }

        if ( ! empty( $search_str ) ) {
            $sql_query .= " AND `title` LIKE '%" . $search_str . "%' OR `desc` LIKE '%" . $search_str . "%'";
        }

        $sql_query .= " ORDER BY {table_name}.id DESC";

        $found_funnels = WFFN_Core()->get_dB()->get_results( $sql_query );

        $if_paged = false;
        if ( count( $found_funnels ) > $limit ) {
            $paged     = ( $paged > 0 ) ? ( $paged - 1 ) : $paged;
            $sql_query .= ' LIMIT ' . $limit * $paged . ', ' . $limit;
            $if_paged  = true;
        }

        $funnel_ids = ( $if_paged ) ? WFFN_Core()->get_dB()->get_results( $sql_query ) : $found_funnels;
        $items      = array();

        foreach ( $funnel_ids as $funnel_id ) {
            $funnel = new WFFN_Funnel( $funnel_id['funnel_id'] );
            $steps  = $funnel->get_steps();
            $view   = ( is_array( $steps ) && count( $steps ) > 0 ) ? get_permalink( $steps[0]['id'] ) : "";

            $row_actions = array();

            $row_actions['id'] = array(
                'action' => 'id',
                'text'   => 'ID: ' . $funnel->get_id(),
                'link'   => '',
                'attrs'  => '',
            );

            $row_actions['edit'] = array(
                'action' => 'edit',
                'text'   => __( 'Edit', 'funnel-builder' ),
                'link'   => WFFN_Common::get_funnel_edit_link( $funnel->get_id() ),
                'attrs'  => '',
            );

            $row_actions['view'] = array(
                'action' => 'view',
                'text'   => __( 'View', 'funnel-builder' ),
                'link'   => $view,
                'attrs'  => 'target="_blank"',
            );

            $row_actions['contacts'] = array(
                'action' => 'contacts',
                'text'   => __( 'Contact', 'funnel-builder' ),
                'link'   => WFFN_Common::get_funnel_edit_link( $funnel->get_id(), '/contacts' ),
                'attrs'  => '',
            );

            $row_actions['analytics'] = array(
                'action' => 'analytics',
                'text'   => __( 'Analytics', 'funnel-builder' ),
                'link'   => WFFN_Common::get_funnel_edit_link( $funnel->get_id(), '/analytics' ),
                'attrs'  => '',
            );

            $row_actions['duplicate'] = array(
                'action' => 'duplicate',
                'text'   => __( 'Duplicate', 'funnel-builder' ),
                'link'   => 'javascript:void(0);',
                'attrs'  => 'v-on:click="duplicateFunnel(' . $funnel->get_id() . ')" class="wffn-duplicate-funnel" data-funnel-id="' . $funnel->get_id() . '" id="wffn_duplicate_' . $funnel->get_id() . '"',
            );

            $row_actions['export'] = array(
                'action' => 'export',
                'text'   => __( 'Export', 'funnel-builder' ),
                'link'   => wp_nonce_url( admin_url( 'admin.php?action=wffn-export&id=' . $funnel->get_id() ), 'wffn-export' ),
                'attrs'  => '',
            );

            $row_actions['delete'] = array(
                'action' => 'delete',
                'text'   => __( 'Delete', 'funnel-builder' ),
                'link'   => 'javascript:void(0);',
                'attrs'  => 'v-on:click="deleteFunnel(' . $funnel->get_id() . ')" class="wffn-delete-funnel" data-funnel-id="' . $funnel->get_id() . '" id="wffn_delete_' . $funnel->get_id() . '"',
            );

            $items[] = array(
                'id'          => $funnel->get_id(),
                'title'       => $funnel->get_title(),
                'desc'        => $funnel->get_desc(),
                'date_added'  => $funnel->get_date_added(),
                'row_actions' => $row_actions,
                '__funnel'    => $funnel,
            );
        }

        $found_posts = array( 'found_posts' => count( $found_funnels ) );

        $found_posts['items'] = $items;

        return apply_filters( 'wffn_funnels_lists', $found_posts );
    }

    public function posts_per_page() {
        return 20;
    }


    public function maybe_create_starter_steps() {
        $page        = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
        $action      = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
        $funnel_name = filter_input( INPUT_GET, 'funnel_name', FILTER_SANITIZE_STRING );
        $funnel_id   = filter_input( INPUT_GET, 'funnel_id', FILTER_SANITIZE_STRING );
        if ( 'bwf_funnels' !== $page || 'wffn_maybe_create_starter_steps' !== $action ) {
            return;
        }

        check_admin_referer( 'wffn_maybe_create_starter_steps' );
        $funnel_name = ! empty( $funnel_name ) ? $funnel_name : __( '(no title)', 'funnel-builder' );

        $funnel = WFFN_Core()->admin->get_funnel( (int) $funnel_id );
        if ( $funnel instanceof WFFN_Funnel ) {
            if ( $funnel->get_id() === 0 ) {
                $funnel_id = $funnel->add_funnel( array(
                    'title'  => $funnel_name,
                    'desc'   => '',
                    'status' => 1,
                ) );

                if ( $funnel_id > 0 ) {
                    $funnel->id = $funnel_id;
                }
            }
        }

        if ( wffn_is_valid_funnel( $funnel ) ) {

            if ( $funnel_id > 0 ) {
                if ( defined( 'ICL_LANGUAGE_CODE' ) && 'all' !== ICL_LANGUAGE_CODE ) {
                    WFFN_Core()->get_dB()->update_meta( $funnel_id, '_lang', ICL_LANGUAGE_CODE );
                }
                wp_redirect( WFFN_Common::get_funnel_edit_link( $funnel_id ) );
                exit;
            } else {
                wp_die( esc_html__( 'Sorry, we are unable to create funnel due to some technical difficulties. Please contact support', 'funnel-builder' ) );
            }
        }

    }

    public function hide_from_menu() {
        global $submenu;
        foreach ( $submenu as $key => $men ) {
            if ( 'woofunnels' !== $key ) {
                continue;
            }
            foreach ( $men as $k => $d ) {
                if ( 'woofunnels-settings' === $d[2] ) {
                    unset( $submenu[ $key ][ $k ] );
                }
            }
        }
    }

    public function render_primary_nav() {
    }

    public function get_selected_nav_class() {
        if ( ! isset( $_GET['tab'] ) && 'bwf_funnels' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
            return 'nav-tab-active';
        }

        return '';
    }

    public function get_selected_nav_class_global() {
        if ( isset( $_GET['page'] ) && 'woofunnels-settings' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
            return 'nav-tab-active';
        }

        return '';
    }

    /**
     * Remove all the notices in our dashboard pages as they might break the design.
     */
    public function maybe_remove_all_notices_on_page() {
        if ( $this->is_wffn_flex_page() && false === $this->is_wffn_flex_page( 'list' ) ) {

            remove_all_actions( 'admin_notices' );
        }
    }


    /**
     * Adding landing pages in homepage display settings
     *
     * @param $pages
     * @param $args
     *
     * @return array
     */
    public function add_landing_in_home_pages( $pages, $args ) {
        if ( is_array( $args ) && isset( $args['name'] ) && 'page_on_front' !== $args['name'] ) {
            return $pages;
        }

        if ( is_array( $args ) && isset( $args['name'] ) && 'page_on_front' === $args['name'] ) {

            $landing_pages = get_posts( array( //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
                'post_type'   => WFFN_Core()->landing_pages->get_post_type_slug(),
                'numberposts' => 100,
                'post_status' => 'publish'
            ) );


            $optin_pages = get_posts( array( //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
                'post_type'   => WFOPP_Core()->optin_pages->get_post_type_slug(),
                'numberposts' => 100,
                'post_status' => 'publish'
            ) );




            $pages = array_merge( $pages, $landing_pages, $optin_pages );
        }

        return $pages;
    }


    public function admin_footer_text( $footer_text ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $footer_text;
        }
        $current_screen = get_current_screen();
        $wffn_pages     = array( 'woofunnels_page_bwf_funnels', 'woofunnels_page_wffn-settings' );


        // Check to make sure we're on a WooFunnels admin page.
        if ( isset( $current_screen->id ) && apply_filters( 'bwf_funnels_funnels_display_admin_footer_text', in_array( $current_screen->id, $wffn_pages, true ), $current_screen->id ) ) {
            // Change the footer text.

            $footer_text = __( 'Thanks for creating with WooFunnels. Need help? <a href="https://buildwoofunnels.com/support" target="_blank">Contact Support</a>', 'funnel-builder' );

        }

        return $footer_text;
    }

    public function maybe_show_notices() {
        global $wffn_notices;
        if ( ! is_array( $wffn_notices ) || empty( $wffn_notices ) ) {
            return;
        }

        foreach ( $wffn_notices as $notice ) {
            echo wp_kses_post( $notice );
        }
    }

    /**
     * Hooked over 'plugin_action_links_{PLUGIN_BASENAME}' WordPress hook to add deactivate popup support
     *
     * @param array $links array of existing links
     *
     * @return array modified array
     */
    public function plugin_actions( $links ) {
        $links['deactivate'] .= '<i class="woofunnels-slug" data-slug="' . WFFN_PLUGIN_BASENAME . '"></i>';

        return $links;
    }

    /**
     * Initiate WFFN_Background_Importer class if ActionScheduler class doesn't exist
     * @see woofunnels_maybe_update_customer_database()
     */
    public function wffn_maybe_init_background_updater() {
        if ( class_exists( 'WFFN_Background_Importer' ) ) {
            $this->wffn_updater = new WFFN_Background_Importer();
        }

        if ( class_exists( 'WFFN_Background_DB_Updater' ) ) {
            $this->wffn_db_updater = new WFFN_Background_DB_Updater();
        }
    }

    /**
     * @hooked over `admin_init`
     * This method takes care of template importing
     * Checks whether there is a need to import
     * Iterates over define callbacks and passes it to background updater class
     * Updates templates for all steps of the funnels
     */
    public function wffn_maybe_run_templates_importer() {
        if ( is_null( $this->wffn_updater ) ) {
            return;
        }
        $funnel_id = get_option( '_wffn_scheduled_funnel_id', 0 );

        if ( $funnel_id > 0 ) { // WPCS: input var ok, CSRF ok.
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'funnel-builder' ) );
            }

            $task = 'wffn_maybe_import_funnel_in_background';  //Scanning order table and updating customer tables
            $this->wffn_updater->push_to_queue( $task );
            WooFunnels_Dashboard::$classes['BWF_Logger']->log( '**************START Importing************', 'wffn_template_import' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
            $this->wffn_updater->save()->dispatch();
            WooFunnels_Dashboard::$classes['BWF_Logger']->log( 'First Dispatch completed', 'wffn_template_import' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
        }
    }

    /**
     * Delete wffn-wizard and redirect install
     */
    public function reset_wizard() {
        if ( current_user_can( 'manage_options' ) && isset( $_GET['wffn_show_wizard_force'] ) && 'yes' === $_GET['wffn_show_wizard_force'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

            delete_option( '_wffn_onboarding_completed' );

            wp_redirect( admin_url( 'admin.php?page=woofunnels&tab=' . WFFN_SLUG . '-wizard' ) );
            exit;

        }
    }

    /**
     * @return array
     */
    public function get_all_active_page_builders() {

        $page_builders = [];

        $page_builders = ['elementor', 'divi'];
        return $page_builders;
    }


    public function include_template_preview_helpers( $admin_instance, $identifier_variable ) { //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedParameter

        include_once WFFN_Core()->admin->get_admin_path() . '/views/commons/template-new.php';  //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
        include_once WFFN_Core()->admin->get_admin_path() . '/views/commons/template-preview.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
    }

    public function get_template_helper_settings_html( $admin_instance ) {

        if ( is_callable( [ $admin_instance, 'get_template_settings' ] ) ) {
            $admin_instance->get_template_settings();
        }
    }

    /**
     * Keep the menu open when editing the flows.
     * Highlights the wanted admin (sub-) menu items for the CPT.
     *
     * @since 1.0.0
     */
    public function menu_highlight() {
        global $submenu_file;

        if ( filter_input( INPUT_GET, 'wffn_funnel_ref', FILTER_SANITIZE_STRING ) ) {
            $submenu_file = 'admin.php?page=bwf_funnels'; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        }
    }

    /**
     * @param $query WP_Query
     */
    public function load_page_to_home_page( $query ) {
        if ( $query->is_main_query() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

            $post_type = $query->get( 'post_type' );

            $page_id = $query->get( 'page_id' );

            if ( empty( $post_type ) && ! empty( $page_id ) ) {
                $t_post = get_post( $page_id );
                if ( in_array( $t_post->post_type, [ WFFN_Core()->landing_pages->get_post_type_slug(), WFOPP_Core()->optin_pages->get_post_type_slug() ], true ) ) {
                    $query->set( 'post_type', get_post_type( $page_id ) );
                }
            }
        }
    }

	public function maybe_delete_all_funnels() {
		if ( $this->is_wffn_flex_page( 'list' ) ) {
			if ( ( isset( $_REQUEST['action'] ) && 'bulk_delete' === $_REQUEST['action'] ) || ( isset( $_REQUEST['action2'] ) && 'bulk_delete' === $_REQUEST['action2'] ) ) {
				check_admin_referer( 'wffn_bulk_del' );
				$funnels = isset ( $_REQUEST['funnels'] ) ? wffn_clean( $_REQUEST['funnels'] ) : array();
				if ( count( $funnels ) > 0 ) {
					foreach ( $funnels as $funnel_id ) {
						$funnel = WFFN_Core()->admin->get_funnel( $funnel_id );
						$funnel->delete();
					}
					$redirect_url = array( 'page' => 'bwf_funnels' );
					if ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] !== '' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$redirect_url['status'] = wffn_clean( $_REQUEST['status'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					}
					if ( isset( $_REQUEST['paged'] ) && $_REQUEST['paged'] > 1 ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$redirect_url['paged'] = wffn_clean( $_REQUEST['paged'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					}
					wp_redirect( add_query_arg( $redirect_url, admin_url( 'admin.php' ) ) );
					exit;
				}
			}
		}
    }
    public function check_db_version() {

        $get_db_version = get_option( '_wffn_db_version', '0.0.0' );

        if ( version_compare( WFFN_DB_VERSION, $get_db_version, '>' ) ) {



            include_once plugin_dir_path( WFFN_PLUGIN_FILE ) . 'admin/db/class-wffn-db-tables.php';
            $tables = WFFN_DB_Tables::get_instance();
            $tables->define_tables();
            $tables->add_if_needed();

        }

    }
    /**
     * @hooked over `admin_head`
     * This method takes care of database updating process.
     * Checks whether there is a need to update the database
     * Iterates over define callbacks and passes it to background updater class
     */
    public function maybe_update_database_update() {

        if ( is_null( $this->wffn_db_updater ) ) {

            /**
             * Update the option as tables are updated.
             */
            update_option( '_wfocu_db_version', WFOCU_DB_VERSION, true );

            return;
        }
        $task_list          = array(
            '3.2.0' => array( 'wffn_handle_global_checkout' ),
        );
        $current_db_version = get_option( '_wffn_db_version', '0.0.0' );
        $update_queued      = false;

        foreach ( $task_list as $version => $tasks ) {
            if ( version_compare( $current_db_version, $version, '<' ) ) {
                foreach ( $tasks as $update_callback ) {

                    $this->wffn_db_updater->push_to_queue( $update_callback );
                    $update_queued = true;
                }
            }
        }

        if ( $update_queued ) {

            $this->wffn_db_updater->save()->dispatch();
        }

        update_option( '_wffn_db_version', WFFN_DB_VERSION, true );

    }

}

if ( class_exists( 'WFFN_Core' ) ) {
    WFFN_Core::register( 'admin', 'WFFN_Admin' );
}