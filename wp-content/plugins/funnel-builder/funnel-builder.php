<?php
/**
 * Plugin Name: WooFunnels Funnel Builder
 * Plugin URI: https://buildwoofunnels.com/wordpress-funnel-builder/
 * Description: Create high-converting sales funnels on WordPress that look professional by following a well-guided step-by-step process.
 * Version: 1.0.14
 * Author: BuildWooFunnels
 * Author URI: https://buildwoofunnels.com
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: funnel-builder
 *
 * Requires at least: 5.0.0
 * Tested up to: 5.6
 * Requires PHP: 7.0
 * WooFunnels: true
 *
 * Funnel Builder is free software.
 * You can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Funnel Builder is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Funnel Builder. If not, see <http://www.gnu.org/licenses/>.
 */

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

if ( ! class_exists( 'WFFN_Core' ) ) {

    /**
     * Class WFFN_Core
     */
    class WFFN_Core {

        /**
         * @var null
         */
        public static $_instance = null;

        /**
         * @var array
         */
        private static $_registered_entity = array(
            'active'   => array(),
            'inactive' => array(),
        );

        /**
         * @var WFFN_Admin
         */
        public $admin;

        public $assets;

        /**
         * @var WFFN_Steps
         */
        public $steps;

        /**
         * @var WFFN_Substeps
         */
        public $substeps;
        /**
         * @var WFFN_Data
         */
        public $data;

        /**
         * @var WFFN_Logger
         */
        public $logger;

        /**
         * @var WFFN_Importer
         */
        public $import;

        /**
         * @var WFFN_Remote_Template_Importer
         */
        public $remote_importer;

        /**
         * @var WFFN_Page_Builder_Manager
         */
        public $page_builders;

        /**
         * @var WFFN_Landing_Pages
         */
        public $landing_pages;

        /**
         * @var WFFN_Funnel_Contacts
         *
         */
        public $wffn_contacts;

        /**
         * @var WFFN_Thank_You_WC_Pages
         */
        public $thank_you_pages;

        /** @var WFFN_Funnels_DB */
        public $funnels_db = null;

        /** @var WFFN_Template_Importer */
        public $importer = null;

        /** @var WFFN_Public */
        public $public = null;

        /** @var WFFN_WP_User_AutoLogin */
        public $autologin = null;

        /**
         * WFFN_Core constructor.
         */
        public function __construct() {
            /**
             * Load important variables and constants
             */
            $this->define_plugin_properties();
            require_once( __DIR__ . '/start.php' );
            require __DIR__ . '/includes/wffn-functions.php';
            add_action( 'plugins_loaded', array( 'WooFunnel_Loader', 'include_core' ), - 1 );

            /**
             * Loads hooks
             */
            $this->load_hooks();
        }

        /**
         * Defining constants
         */
        public function define_plugin_properties() {
            define( 'WFFN_VERSION', '1.0.14' );
            define( 'WFFN_BWF_VERSION', '1.9.46' );
            define( 'WFFN_MIN_WC_VERSION', '3.5.0' );
            define( 'WFFN_MIN_WP_VERSION', '4.9.0' );
            define( 'WFFN_DB_VERSION', '3.2.0' );
            define( 'WFFN_SLUG', 'wffn' );
            define( 'WFFN_FULL_NAME', __( 'Funnel Builder', 'funnel-builder' ) );
            define( 'WFFN_PLUGIN_FILE', __FILE__ );
            define( 'WFFN_PLUGIN_DIR', __DIR__ );
            define( 'WFFN_PLUGIN_URL', untrailingslashit( plugin_dir_url( WFFN_PLUGIN_FILE ) ) );
            define( 'WFFN_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            define( 'WFFN_TEMPLATE_UPLOAD_DIR', WP_CONTENT_DIR . '/uploads/wffn_templates/' );
            ( defined( 'WFFN_IS_DEV' ) && true === WFFN_IS_DEV ) ? define( 'WFFN_VERSION_DEV', time() ) : define( 'WFFN_VERSION_DEV', WFFN_VERSION );
            ( ! defined( 'WFFN_REACT_ENVIRONMENT' ) ) ? define( 'WFFN_REACT_ENVIRONMENT', 1 ) : '';

        }

        /**
         * Load classes on plugins_loaded hook
         */
        public function load_hooks() {
            /**
             * Initialize Localization
             */
            add_action( 'init', array( $this, 'localization' ) );
            add_action( 'plugins_loaded', array( $this, 'load_classes' ), 1 );
            add_action( 'plugins_loaded', array( $this, 'register_classes' ), 1 );

            add_filter( 'enable_woofunnels_as_ds', '__return_true' );

            register_activation_hook( __FILE__, [ $this, 'plugin_activation_hook' ] );

            if ( ! class_exists( 'WFACP_Core' ) ) {
                require __DIR__ . '/modules/checkouts/woofunnels-aero-checkout-lite.php';
            }
            if ( ! class_exists( 'WFOPP_Core' ) ) {
                require __DIR__ . '/modules/optins/woofunnels-optins.php';
            }
        }

        /**
         *
         */
        public function load_classes() {
            /**
             * Loads all the admin
             */
            $this->load_admin();

            $this->load_includes();

            $this->load_modules();

            $this->load_steps();

            $this->load_commons();

            $this->load_analytics();

        }

        /**
         * Loads the admin
         */
        public function load_admin() {
            include_once __DIR__ . '/admin/class-wffn-admin.php';
            include_once __DIR__ . '/admin/class-wffn-funnels-listing-table.php';
            include_once __DIR__ . '/admin/class-bwf-admin-breadcrumbs.php';
            include_once __DIR__ . '/admin/class-bwf-admin-settings.php';
            include_once __DIR__ . '/admin/class-wffn-page-builder-manager.php';
            include_once __DIR__ . '/admin/class-wffn-exporter.php';
            include_once __DIR__ . '/admin/class-wffn-importer.php';
            include_once __DIR__ . '/admin/class-wffn-wizard.php';
            include_once __DIR__ . '/admin/rest-api/class-wffn-rest-funnels.php';
            include_once __DIR__ . '/admin/rest-api/class-wffn-rest-steps.php';
            include_once __DIR__ . '/admin/rest-api/class-wffn-rest-substeps.php';
        }

        /**
         * Load includes folder
         */
        public function load_includes() {

            require __DIR__ . '/includes/class-wffn-logger.php';
            require __DIR__ . '/includes/class-wffn-wp-user-autologin.php';
            require __DIR__ . '/includes/class-wffn-ajax-controller.php';
            require __DIR__ . '/includes/class-wffn-session-handler.php';
            require __DIR__ . '/includes/class-wffn-data.php';
            require __DIR__ . '/includes/class-wffn-funnels-db.php';
            require __DIR__ . '/includes/class-wffn-public.php';
            require __DIR__ . '/includes/class-wffn-funnel.php';
            require __DIR__ . '/includes/class-wffn-woofunnels-support.php';
            require __DIR__ . '/merge-tags/class-bwf-contact-tags.php';
            require __DIR__ . '/includes/class-wffn-funnel-contacts.php';
            require __DIR__ . '/includes/class-wffn-rest-controller.php';
            require __DIR__ . '/includes/class-wffn-rest-api-dashboard-endpoint.php';
        }

        /**
         * Include Modules (Landing & thankyou)
         */
        public function load_modules() {
            require __DIR__ . '/includes/class-wffn-module-common.php';
            require __DIR__ . '/modules/landing-pages/class-wffn-landing-pages.php';
            require __DIR__ . '/modules/thankyou-pages/class-wffn-thank-you-wc-pages.php';
            do_action( 'wffn_core_modules_loaded' );
        }

        /**
         * Include steps and substeps
         */
        public function load_steps() {
            require __DIR__ . '/includes/class-wffn-step-base.php';
            require __DIR__ . '/includes/class-wffn-step.php';
            require __DIR__ . '/includes/class-wffn-steps.php';
            require __DIR__ . '/includes/class-wffn-substep.php';
            require __DIR__ . '/includes/class-wffn-substeps.php';
        }

        /**
         * Includes common functions.
         */
        public function load_commons() {

            require __DIR__ . '/includes/class-wffn-common.php';

            require __DIR__ . '/importer/interface-import-export.php';
            require __DIR__ . '/importer/class-wffn-template-importer.php';
            require __DIR__ . '/importer/class-wffn-background-importer.php';
            require __DIR__ . '/admin/db/class-wffn-background-db-updater.php';
            require __DIR__ . '/admin/db/wffn-updater-functions.php';

            if ( defined( 'ELEMENTOR_VERSION' ) ) {
                require __DIR__ . '/importer/class-wffn-elementor-importer.php';
            }

            require __DIR__ . '/importer/class-wffn-wp-editor-importer.php';

            if ( defined( 'ET_BUILDER_PLUGIN_VERSION' ) ) {
                require __DIR__ . '/importer/class-wffn-divi-importer.php';
            }
            require __DIR__ . '/admin/includes/autonami/class-wffn-autonami.php';
            require __DIR__ . '/compatibilities/class-wffn-plugin-compatibilities.php';
            WFFN_Common::init();
        }

        public function load_analytics() {
            if ( class_exists( 'WFACP_Core' ) && wffn_is_wc_active() && ! class_exists( 'WFACP_Contacts_Analytics' ) ) {
                require_once __DIR__ . '/contact-analytics/class-wfacp-contacts-analytics.php';
            }
            if ( class_exists( 'WFOPP_Core' ) && ! class_exists( 'WFFN_Optin_Contacts_Analytics' ) ) {
                require_once __DIR__ . '/contact-analytics/class-wffn-optin-contacts-analytics.php';
            }
            if ( class_exists( 'WFOB_Core' ) && wffn_is_wc_active() && ! class_exists( 'WFOB_Contacts_Analytics' ) ) {
                require_once __DIR__ . '/contact-analytics/class-wfob-contacts-analytics.php';
            }
            if ( class_exists( 'WFOCU_Core' ) && wffn_is_wc_active() && ! class_exists( 'WFOCU_Contacts_Analytics' ) ) {
                require_once __DIR__ . '/contact-analytics/class-wfocu-contacts-analytics.php';
            }
        }

        /**
         * @return WFFN_Core|null
         */
        public static function get_instance() {
            if ( null === self::$_instance ) {
                self::$_instance = new self;
            }

            return self::$_instance;
        }

        public function localization() {
            load_plugin_textdomain( 'funnel-builder', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
        }

        /**
         * Register classes
         */
        public function register_classes() {

            $load_classes = self::get_registered_class();
            if ( is_array( $load_classes ) && count( $load_classes ) > 0 ) {
                foreach ( $load_classes as $access_key => $class ) {

                    $this->$access_key = $class::get_instance();
                }
                do_action( 'wffn_loaded' );
            }
        }

        /**
         * @return mixed
         */
        public static function get_registered_class() {
            return self::$_registered_entity['active'];
        }

        public static function register( $short_name, $class, $overrides = null ) { //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedParameter

            self::$_registered_entity['active'][ $short_name ] = $class;

        }

        /**
         * @return WFFN_Funnels_DB
         */
        public function get_dB() {
            if ( empty( $this->funnels_db ) ) {
                $class            = apply_filters( 'wffn_funnels_db_class', 'WFFN_Funnels_DB' );
                $this->funnels_db = new $class();
            }

            return $this->funnels_db;
        }

        public function plugin_activation_hook() {
            update_option( 'bwf_needs_rewrite', 'yes', true );
        }

        public function get_plugin_url() {
            return untrailingslashit( plugin_dir_url( WFFN_PLUGIN_FILE ) );
        }
    }
}
if ( ! function_exists( 'WFFN_Core' ) ) {
    /**
     * @return WFFN_Core|null
     */
    function WFFN_Core() {  //@codingStandardsIgnoreLine
        return WFFN_Core::get_instance();
    }
}

$GLOBALS['WFFN_Core'] = WFFN_Core();

