<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_Common
 * Handles Common Functions For Admin as well as front end interface
 */
class WFFN_Common {
    public static function init() {
    }

    /**
     * @param $arr
     */
    public static function pr( $arr ) {
        echo '<pre>';
        print_r( $arr );  // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
        echo '</pre>';
    }

    /**
     * @return array
     */
    public static function get_steps_data() {
        $steps        = WFFN_Core()->steps->get_supported_steps();
        $sorted_steps = self::sort_steps( $steps );
        $steps_data   = array();
        foreach ( $sorted_steps as $step ) {
            $steps_data[ $step->slug ] = $step->get_step_data();
        }

        return $steps_data;
    }

    /**
     * @param $steps
     *
     * @return mixed
     */
    public static function sort_steps( $steps ) {
        usort( $steps, function ( $a, $b ) {
            if ( $a->list_priority === $b->list_priority ) {
                return 0;
            }

            return ( $a->list_priority < $b->list_priority ) ? - 1 : 1;
        } );

        return $steps;
    }

    /**
     * @return array
     */
    public static function get_substeps_data() {
        $substeps      = WFFN_Core()->substeps->get_supported_substeps();
        $substeps_data = array();
        foreach ( $substeps as $substep ) {
            $substeps_data[ $substep->slug ] = $substep->get_substep_data();
        }

        return $substeps_data;
    }

    /**
     * @return array
     */
    public static function get_funnel_data() {
        $funnel = WFFN_Core()->admin->get_funnel();

        return $funnel->get_funnel_data();
    }

    /**
     * @return array
     */
    public static function get_funnel_delete_data() {
        $funnel = new WFFN_Funnel( '0' );

        return $funnel->get_delete_data();
    }

    /**
     * @return array
     */
    public static function get_funnel_duplicate_data() {
        $funnel = WFFN_Core()->admin->get_funnel();

        return $funnel->get_duplicate_data();
    }

    /**
     * @return array
     */
    public static function get_success_popups() {

        $popup_data = array();
        $funnel     = new WFFN_Funnel( 0 );

        $popup_data[ self::get_funnel_slug() ] = $funnel->get_popup_data();

        return $popup_data;
    }

    public static function get_funnel_slug() {
        return 'funnel';
    }

    /**
     * @return array
     */
    public static function get_loader_popups() {

        $loader_data = array();
        $funnel      = new WFFN_Funnel( 0 );

        $loader_data[ self::get_funnel_slug() ] = $funnel->get_loader_popup();

        return $loader_data;
    }


    public static function is_admin_action() {
        if ( is_admin() && ( 'bwf_funnels' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && 1 === filter_input( INPUT_POST, 'is_funnel_action', FILTER_SANITIZE_NUMBER_INT ) ) ) ) {
            return true;
        }

        return false;
    }

    public static function get_fonts_list() {
        $fonts        = [];
        $fontpath     = plugin_dir_path( WFFN_PLUGIN_FILE ) . '/assets/live/fonts/fonts.json';
        $google_fonts = json_decode( file_get_contents( $fontpath ) );     //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
        $web_fonts    = apply_filters( 'wfacp_customizer_fonts', $google_fonts );
        $fonts[]      = array(
            'id'   => 'default',
            'name' => __( 'Default', 'funnel-builder' )
        );
        foreach ( $web_fonts as $web_font_family ) {

            if ( $web_font_family !== 'Open Sans' ) {
                $fonts[] = array(
                    'id'   => $web_font_family,
                    'name' => $web_font_family,
                );
            }
        }

        return $fonts;

    }

    public static function get_font_weights() {
        return array(
            array(
                'id'   => '400',
                'name' => __( 'Normal 400', 'funnel-builder' )
            ),
            array(
                'id'   => '700',
                'name' => __( 'Bold 700', 'funnel-builder' )
            ),
        );
    }

    public static function search_page( $term, $post_types ) {
        global $wpdb;
        $like_term     = '%' . $wpdb->esc_like( $term ) . '%';
        $post_statuses = array( 'publish' );
        $query         = $wpdb->prepare( "SELECT DISTINCT posts.ID FROM {$wpdb->posts} posts WHERE ( posts.post_title LIKE %s or posts.ID = %s )	AND posts.post_type IN ('" . implode( "','", $post_types ) . "') AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "') ORDER BY posts.post_parent ASC, posts.post_title ASC", $like_term, $like_term ); //phpcs:ignore

        $post_ids = $wpdb->get_col( $query ); //phpcs:ignore

        if ( is_numeric( $term ) ) {
            $post_id    = absint( $term );
            $post_ids[] = $post_id;
        }

        return wp_parse_id_list( $post_ids );
    }

    public static function admin_user() {
        $admin_email = get_option( 'admin_email' );
        $user        = get_user_by( 'email', $admin_email );

        return $user;
    }

    public static function maybe_elementor_template( $page_id, $new_page_id ) {
        $contents = get_post_meta( $page_id, '_elementor_data', true );
        $data     = [
            '_elementor_version'       => get_post_meta( $page_id, '_elementor_version', true ),
            '_elementor_template_type' => get_post_meta( $page_id, '_elementor_template_type', true ),
            '_elementor_edit_mode'     => get_post_meta( $page_id, '_elementor_edit_mode', true ),

        ];
        foreach ( $data as $meta_key => $meta_value ) {
            update_post_meta( $new_page_id, $meta_key, $meta_value );
        }

        if(!function_exists('wp_read_video_metadata')) {
            require_once ABSPATH . '/wp-admin/includes/media.php';
        }
        $instance = new WFFN_Elementor_Importer();
        if ( ! is_null( $instance ) ) {
            if ( is_array( $contents ) ) {
                $contents = wp_json_encode( $contents );

            }
            $instance->import( $new_page_id, $contents );
        }
    }

    /**
     * Remove action for without instance method  class found and return object of class
     *
     * @param $hook
     * @param $cls string
     * @param string $function
     *
     * @return |null
     */
    public static function remove_actions( $hook, $cls, $function = '' ) {

        global $wp_filter;
        $object = null;
        if ( class_exists( $cls ) && isset( $wp_filter[ $hook ] ) && ( $wp_filter[ $hook ] instanceof WP_Hook ) ) {
            $hooks = $wp_filter[ $hook ]->callbacks;
            foreach ( $hooks as $priority => $reference ) {
                if ( is_array( $reference ) && count( $reference ) > 0 ) {
                    foreach ( $reference as $index => $calls ) {
                        if ( isset( $calls['function'] ) && is_array( $calls['function'] ) && count( $calls['function'] ) > 0 ) {
                            if ( is_object( $calls['function'][0] ) ) {
                                $cls_name = get_class( $calls['function'][0] );
                                if ( $cls_name === $cls && $calls['function'][1] === $function ) {
                                    $object = $calls['function'][0];
                                    unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $index ] );
                                }
                            } elseif ( $index === $cls . '::' . $function ) {
                                $object = $cls;
                                unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $cls . '::' . $function ] );
                            }
                        }
                    }
                }
            }
        } elseif ( function_exists( $cls ) && isset( $wp_filter[ $hook ] ) && ( $wp_filter[ $hook ] instanceof WP_Hook ) ) {

            $hooks = $wp_filter[ $hook ]->callbacks;
            foreach ( $hooks as $priority => $reference ) {
                if ( is_array( $reference ) && count( $reference ) > 0 ) {
                    foreach ( $reference as $index => $calls ) {
                        $remove = false;
                        if ( $index === $cls ) {
                            $remove = true;
                        } elseif ( isset( $calls['function'] ) && $cls === $calls['function'] ) {
                            $remove = true;
                        }
                        if ( true === $remove ) {
                            unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $cls ] );
                        }
                    }
                }
            }
        }

        return $object;

    }

    public static function get_discount_type_keys() {

        $discounted = [
            'fixed_on_reg'          => sprintf( __( '%s Fixed Amount on Regular Price', 'funnel-builder' ), get_woocommerce_currency_symbol() ),
            'fixed_on_sale'         => sprintf( __( '%s Fixed Amount on Sale Price', 'funnel-builder' ), get_woocommerce_currency_symbol() ),
            'percentage_on_reg'     => __( '% on Regular Price', 'funnel-builder' ),
            'percentage_on_sale'    => __( '% on Sale Price', 'funnel-builder' ),
            'fixed_discount_reg'    => sprintf( __( '%s Fixed Amount on Regular Price', 'woofunnels-aero-checkout' ), get_woocommerce_currency_symbol() ),
            'fixed_discount_sale'   => sprintf( __( '%s Fixed Amount on Sale Price', 'woofunnels-aero-checkout' ), get_woocommerce_currency_symbol() ),
            'percent_discount_reg'  => __( '% on Regular Price', 'woofunnels-aero-checkout' ),
            'percent_discount_sale' => __( '% on Sale Price', 'woofunnels-aero-checkout' ),
        ];

        return $discounted;

    }

    public static function get_funnel_edit_link( $funnel_id, $path = '/funnel' ) {
        if ( empty( $funnel_id ) ) {
            return '#';
        }

        return add_query_arg( array(
            'page'    => 'bwf_funnels',
            'section' => 'funnel',
            'path'    => $path,
            'edit'    => $funnel_id,
        ), admin_url( 'admin.php' ) );
    }

    public static function get_step_edit_link( $step_id, $type ) {

        if ( empty( $step_id ) || empty( $type ) ) {
            return '#';
        }

        switch ( $type ) {
            case 'landing':
                $step_args = [
                    'page'    => 'wf-lp',
                    'section' => 'design',
                    'edit'    => $step_id,
                ];
                break;
            case 'thankyou':
                $step_args = [
                    'page'    => 'wf-ty',
                    'section' => 'design',
                    'edit'    => $step_id,
                ];
                break;
            case 'aero':
                $step_args = [
                    'page'     => 'wfacp',
                    'wfacp_id' => $step_id,
                ];
                break;
            case 'upsell':
                $step_args = [
                    'page'    => 'upstroke',
                    'section' => 'offers',
                    'edit'    => $step_id,
                ];
                break;
            case 'bump':
                $step_args = [
                    'page'    => 'wfob',
                    'section' => 'products',
                    'wfob_id' => $step_id,
                ];
                break;
            case 'optin':
                $step_args = [
                    'page'    => 'wf-op',
                    'section' => 'design',
                    'edit'    => $step_id,
                ];
                break;
            default:
                return '#';
                break;
        }

        return add_query_arg( $step_args, admin_url( 'admin.php' ) );
    }

    public static function modify_content_emogrifier( $content ) {
        if ( empty( $content ) ) {
            return $content;
        }

        $content = self::prepare_email_content( $content );

        if ( false === self::supports_emogrifier() ) {
            return $content;
        }

        ob_start();
        include WFFN_PLUGIN_DIR . '/includes/libraries/email-styles.php'; //phpcs:ignore
        $css = ob_get_clean();

        $emogrifier_class = '\\Pelago\\Emogrifier';
        if ( ! class_exists( $emogrifier_class ) ) {
            include_once WFFN_PLUGIN_DIR . '/includes/libraries/class-emogrifier.php'; //phpcs:ignore
        }
        try {
            /** @var \Pelago\Emogrifier $emogrifier */
            $emogrifier = new $emogrifier_class( $content, $css );
            $content    = $emogrifier->emogrify();

            return $content;
        } catch ( Exception $e ) {
            BWF_Logger::get_instance()->log( 'Optin test email failure. Message: ' . $e->getMessage(), 'send_email_emogrifier' );
        }

        return $content;
    }

    /**
     * Return if emogrifier library is supported.
     *
     * @return bool
     * @since 3.5.0
     */
    public static function supports_emogrifier() {
        return class_exists( 'DOMDocument' ) && version_compare( PHP_VERSION, '5.5', '>=' );
    }

    public static function prepare_email_content( $content ) {
        $has_body = stripos( $content, '<body' ) !== false;

        /** Check if body tag exists */
        if ( ! $has_body ) {
            return '<html><head></head><body><div id="body_content">' . $content . '</div></body></html>';
        }

        $pattern     = "/<body(.*?)>(.*?)<\/body>/is";
        $replacement = '<body$1><div id="body_content">$2</div></body>';

        return preg_replace( $pattern, $replacement, $content );
    }

    /**
     * Check if funnel builder PRO version is active and license is active
     * @return mixed|void
     */
    public static function wffn_is_funnel_pro_active() {
        return WFFN_Core()->admin->get_license_status();
    }

    public static function get_starter_steps_config() {

        return array(
            array(
                'type'   => 'landing',
                'status' => 1,
                'title'  => __( 'Sales Page', 'funnel-builder' ),
                'meta'   => array(),
            ),
            array(
                'type'   => 'wc_checkout',
                'status' => 1,
                'title'  => __( 'Checkout Page', 'funnel-builder' ),
                'meta'   => array( 'meta' => array( '_wfacp_version' => WFACP_VERSION ) ),
            ),
            array(
                'type'   => 'wc_upsells',
                'status' => 1,
                'title'  => __( 'Upsells', 'funnel-builder' ),
                'meta'   => array(
                    'steps' => array(
                        array(
                            "title" => "Upsell Offer 1",
                            "state" => "1",
                            "type"  => "upsell",
                            'meta'  => array(
                                '_offer_type'    => 'upsell',
                                '_wfocu_setting' => array(
                                    'products' => new stdClass(),
                                    'fields'   => new stdClass(),
                                    'template' => '',

                                ),
                            ),
                        ),
                    ),
                )
            ),
            array(
                'type'   => 'wc_thankyou',
                'status' => 1,
                'title'  => __( 'Thank you Page', 'funnel-builder' ),
                'meta'   => array(),
            ),
        );
    }

	/**
	 * @return string
	 */
	public static function get_wffn_container_attrs() {

		$attributes  = apply_filters( 'wffn_container_attrs', array() );
		$attrs_string = '';

		foreach ( $attributes as $key => $value ) {

			if ( ! $value ) {
				continue;
			}

			if ( true === $value ) {
				$attrs_string .= esc_html( $key ) . ' ';
			} else {
				$attrs_string .= sprintf( '%s=%s ', esc_html( $key ), esc_attr( $value ) );
			}
		}

		return $attrs_string;
	}

	/**
	 * @param $wpdb
	 * @param $str
	 *
	 * @return array|bool
	 */
	public static function maybe_wpdb_error( $wpdb ) {
		$status = array(
			'db_error' => false,
		);

		if ( ! empty( $wpdb->last_error ) ) {
			$status = array(
				'db_error'  => true,
				'msg'       => $wpdb->last_error,
				'query'     => $wpdb->last_query,
				'backtrace' => wp_debug_backtrace_summary() //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
			);

			WFFN_Core()->logger->log( "Get wpdb last error for query : " . print_r( $status, true ) ); // phpcs:ignore
		}

		return $status;
	}
}
