<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class WFFN_Remote_Template_Importer
 * @package WFFN
 * @author XlPlugins
 */
class WFFN_Remote_Template_Importer {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get_error_message( $code ) {
        $messages = [
            'license-or-domain-invalid' => __( 'License or domain invalid', 'funnel-builder' ),
            'unauthorized-access'       => sprintf( __( 'Please check if you have valid license key. <a href=%s target="_blank">Go to Licenses</a>', 'funnel-builder' ), esc_url( admin_url( 'admin.php?page=woofunnels' ) ) ),
        ];
        if ( isset( $messages[ $code ] ) ) {
            return $messages[ $code ];
        }

        return $code;
    }

    /**
     * Import template remotely.
     * @return mixed
     */
    public function get_remote_template( $step, $template_id, $builder ) {

        if ( empty( $step ) || empty( $template_id ) || empty( $builder ) ) {
            return '';
        }

        $license = $this->get_license_key();

        $requestBody = array(
            "step"     => $step,
            "domain"   => $this->get_domain(),
            "license"  => $license,
            "template" => $template_id,
            "builder"  => $builder
        );
        $requestBody = wp_json_encode( $requestBody );

        $endpoint_url = $this->get_template_api_url();
        $response     = wp_remote_post( $endpoint_url, array(
            "body"    => $requestBody,
            "timeout" => 30,
            'headers' => array(
                'content-type' => 'application/json'
            )
        ) );

        WooFunnels_Dashboard::$classes['BWF_Logger']->log( 'Import $requestBody: ' . print_r( $requestBody, true ), 'wffn_template_import' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

        if ( $response instanceof WP_Error ) {
            return false;
        }

        $response = json_decode( $response['body'], true );
        if ( ! is_array( $response ) ) {
            return [ 'error' => __( 'Server Down', 'funnel-builder' ) ];
        }

        if ( isset( $response['error'] ) ) {
            return [ 'error' => self::get_error_message( $response['error'] ) ];
        }

        if ( ! isset( $response[ $step ] ) ) {
            return [ 'error' => __( 'No Template found', 'funnel-builder' ) ];
        }

        if ( 'funnel' === $step ) {
            if ( $response['funnel'] && ! empty( $response['funnel'] ) ) {
                foreach ( $response['funnel'][0]['steps'] as $key => &$step_response ) {
                    if ( $step_response['import_exists'] === "yes" ) {
                        if ( $step_response['type'] === 'wc_upsells' ) {
                            $directory = $builder . '/' . $step_response['type'] . '/' . $step_response['meta']['steps'][0]['template'];

                        } else {
                            $directory = $builder . '/' . $step_response['type'] . '/' . $step_response['template'];

                        }

                        if ( ! is_dir( WFFN_TEMPLATE_UPLOAD_DIR ) ) {
                            mkdir( WFFN_TEMPLATE_UPLOAD_DIR );  //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_mkdir
                        }

                        if ( ! is_dir( WFFN_TEMPLATE_UPLOAD_DIR . '/' . $builder ) ) {
                            mkdir( WFFN_TEMPLATE_UPLOAD_DIR . '/' . $builder ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_mkdir
                        }


                        if ( ! is_dir( WFFN_TEMPLATE_UPLOAD_DIR . '/' . $builder . '/' . $step_response['type'] ) ) {
                            mkdir( WFFN_TEMPLATE_UPLOAD_DIR . '/' . $builder . '/' . $step_response['type'] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_mkdir
                        }
                        $template_path = WFFN_TEMPLATE_UPLOAD_DIR . $directory . '.json';
                        file_put_contents( $template_path, $response['steps'][ $key ] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
                    }
                }
            }

            return $response[ $step ];
        }

        return $response[ $step ];
    }

    public function get_domain() {
        global $sitepress;
        $domain = site_url();

        if ( isset( $sitepress ) && ! is_null( $sitepress ) ) {
            $default_language = $sitepress->get_default_language();
            $domain           = $sitepress->convert_url( $sitepress->get_wp_api()->get_home_url(), $default_language );
        }
        $domain = str_replace( [ 'https://', 'http://' ], '', $domain );
        $domain = trim( $domain, '/' );

        return $domain;
    }

    /**
     * Get license key.
     * @return mixed
     */
    public function get_license_key() {
        $licenseKey      = false;
        $woofunnels_data = get_option( 'woofunnels_plugins_info' );
        if ( is_array( $woofunnels_data ) && count( $woofunnels_data ) > 0 && defined( 'WFFN_PRO_PLUGIN_BASENAME' ) ) {

            foreach ( $woofunnels_data as $key => $license ) {
                if ( is_array( $license ) && isset( $license['activated'] ) && $license['activated'] && sha1( WFFN_PRO_PLUGIN_BASENAME ) === $key && $license['data_extra']['software_title'] === $this->get_software_title() ) {
                    $licenseKey = $license['data_extra']['api_key'];
                    break;
                }
            }
        }

        return $licenseKey;
    }

    public function get_software_title() {
        return 'Funnel Builder Pro';
    }

    public function get_template_api_url() {
        return 'http://gettemplates.buildwoofunnels.com/';
    }
}

WFFN_Core::register( 'remote_importer', 'WFFN_Remote_Template_Importer' );