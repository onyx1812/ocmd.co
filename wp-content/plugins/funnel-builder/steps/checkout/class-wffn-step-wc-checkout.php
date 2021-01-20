<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class contains all the Aero related funnel functionality
 * Class WFFN_Step_Aero_Checkout
 */
class WFFN_Step_WC_Checkout extends WFFN_Step {

    private static $ins = null;
    public $slug = 'wc_checkout';
    public $substeps = [ 'wc_order_bump' ];
    public $list_priority = 20;

    /**
     * WFFN_Step_WC_Checkout constructor.
     */
    public function __construct() {
        parent::__construct();
        add_action( 'wfacp_listing_handle_query_args', [ $this, 'exlude_from_query' ] );
        add_action( 'woocommerce_checkout_order_processed', [ $this, 'maybe_record_orders_in_session' ], 11 );
        add_filter( 'maybe_setup_funnel_for_breadcrumb', [ $this, 'maybe_funnel_breadcrumb' ] );
        add_action( 'wfacp_global_settings_updated', [ $this, 'maybe_mark_funnel_as_global' ], 10 );
    }

    /**
     * @return WFFN_Step_WC_Checkout|null
     */
    public static function get_instance() {
        if ( null === self::$ins ) {
            self::$ins = new self;
        }

        return self::$ins;
    }

    /**
     * @return array|void
     */
    public function get_supports() {
        return array_unique( array_merge( parent::get_supports(), [ 'open_link' ] ) );
    }

    /**
     * @param $steps
     *
     * @return array
     */
    public function get_step_data() {
        $substpes = WFFN_Core()->substeps->get_supported_substeps();
        $substpes = array_intersect( array_keys( $substpes ), $this->substeps );

        return array(
            'type'        => $this->slug,
            'title'       => $this->get_title(),
            'popup_title' => sprintf( __( 'Add %s', 'funnel-builder' ), $this->get_title() ),
            'dashicons'   => 'dashicons-cart',
            'label'       => __( 'No Products', 'funnel-builder' ),
            'label_class' => 'bwf-st-c-badge-red',
            'substeps'    => $substpes,
        );
    }

    /**
     * Return title of Checkout step
     */
    public function get_title() {
        return __( 'Checkout Page', 'funnel-builder' );
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function get_step_designs( $term ) {
        $active_pages = $this->get_checkout_pages( $term );
        $designs      = array();
        foreach ( $active_pages as $active_page ) {
            $post_type = get_post_type( $active_page->ID );

            if ( 'cartflows_step' === $post_type ) {
                $meta = get_post_meta( $active_page->ID, 'wcf-step-type', true );
                if ( 'checkout' === $meta ) {
                    $data      = array(
                        'id'   => $active_page->ID,
                        'name' => $active_page->post_title . ' (#' . $active_page->ID . ')',
                    );
                    $designs[] = $data;
                }
            } else {
                $data      = array(
                    'id'   => $active_page->ID,
                    'name' => $active_page->post_title . ' (#' . $active_page->ID . ')',
                );
                $designs[] = $data;
            }

        }

        return $designs;
    }

    public function get_checkout_pages( $term ) {
        $args = array(
            'post_type'   => array( WFACP_Common::get_post_type_slug(), 'cartflows_step', 'page' ),
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

    /**
     * @param $funnel_id
     * @param $type
     * @param $posted_data
     *
     * @return stdClass
     */
    public function add_step( $funnel_id, $posted_data ) {
        $title = isset( $posted_data['title'] ) ? $posted_data['title'] : '';

        $step_id           = wp_insert_post( array(
            'post_type'   => WFACP_Common::get_post_type_slug(),
            'post_title'  => $title,
            'post_name'   => sanitize_title( $title ),
            'post_status' => 'publish'

        ) );
        $posted_data['id'] = ( $step_id > 0 ) ? $step_id : 0;

        return parent::add_step( $funnel_id, $posted_data );
    }

    /**
     * @param $funnel_id
     * @param $step_id
     * @param $type
     * @param $posted_data
     *
     * @return stdClass
     */
    public function duplicate_step( $funnel_id, $step_id, $posted_data ) {
        $duplicate_id      = $this->duplicate_checkout_page( $step_id );
        $posted_data['id'] = ( $duplicate_id > 0 ) ? $duplicate_id : 0;

        $post_status = ( isset( $posted_data['original_id'] ) && $posted_data['original_id'] > 0 ) ? get_post_status( $posted_data['original_id'] ) : 'publish';

        if ( $duplicate_id > 0 ) {
            $posted_data['id'] = $duplicate_id;
            $new_title         = isset( $posted_data['title'] ) ? $posted_data['title'] : '';
            $arr               = [ 'ID' => $duplicate_id, 'post_status' => $post_status ];

            if ( ! empty( $new_title ) ) {
                $arr['post_title'] = $new_title;
            }
            wp_update_post( $arr );
        }

        return parent::duplicate_step( $funnel_id, $step_id, $posted_data );
    }

    /**
     * Copy data from old checkout page to new checkout page
     *
     * @param $post_id
     *
     * @return int|null|WP_Error
     */

    public function duplicate_checkout_page( $checkout_page_id ) {

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

        if ( $checkout_page_id > 0 ) {
            $checkout_page = get_post( $checkout_page_id );
            if ( ! is_null( $checkout_page ) && ( $checkout_page->post_type === WFACP_Common::get_post_type_slug() || $checkout_page->post_type === 'cartflows_step' || $checkout_page->post_type === 'page' ) ) {
                $args         = [
                    'post_title'   => $checkout_page->post_title . ' - ' . __( 'Copy', 'funnel-builder' ),
                    'post_content' => $checkout_page->post_content,
                    'post_name'    => sanitize_title( $checkout_page->post_title . ' - ' . __( 'Copy', 'funnel-builder' ) ),
                    'post_type'    => WFACP_Common::get_post_type_slug(),
                ];
                $duplicate_id = wp_insert_post( $args );
                if ( ! is_wp_error( $duplicate_id ) ) {

                    global $wpdb;

                    $post_meta_all = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$checkout_page_id" ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

                    if ( ! empty( $post_meta_all ) ) {
                        $sql_query_selects = [];
                        $sql_query_meta    = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                        $content           = '';

                        if ( $checkout_page->post_type === 'cartflows_step' ) {


                            foreach ( $post_meta_all as $meta_info ) {

                                $meta_key = $meta_info->meta_key;

                                if ( ! in_array( $meta_key, $exclude_metas, true ) ) {
                                    if ( strpos( $meta_key, 'wcf-' ) === false ) {
                                        /**
                                         * Good to remove slashes before adding
                                         */
                                        $meta_value = addslashes( $meta_info->meta_value );
                                        if ( $meta_key === '_wp_page_template' ) {
                                            $meta_value = ( strpos( $meta_value, 'cartflows' ) !== false ) ? str_replace( 'cartflows', "wfacp", $meta_value ) : $meta_value;
                                        }
                                        if ( $meta_key === '_elementor_data' ) {
                                            $content = $meta_info->meta_value;
                                        }

                                        $sql_query_selects[] = "SELECT $duplicate_id, '$meta_key', '$meta_value'"; //db call ok; no-cache ok; WPCS: unprepared SQL ok.

                                    }
                                }
                            }
                        } else {
                            update_option( WFACP_SLUG . '_c_' . $duplicate_id, get_option( WFACP_SLUG . '_c_' . $checkout_page_id, [] ), 'no' );
                            foreach ( $post_meta_all as $meta_info ) {

                                $meta_key = $meta_info->meta_key;
                                /**
                                 * Good to remove slashes before adding
                                 */
                                $meta_value = addslashes( $meta_info->meta_value );

                                if ( $meta_key === '_elementor_data' ) {
                                    $content = $meta_info->meta_value;
                                }

                                $sql_query_selects[] = "SELECT $duplicate_id, '$meta_key', '$meta_value'"; //db call ok; no-cache ok; WPCS: unprepared SQL ok.
                            }
                        }

                        $sql_query_meta .= implode( ' UNION ALL ', $sql_query_selects ); //db call ok; no-cache ok; WPCS: unprepared SQL ok.

                        $wpdb->query( $sql_query_meta ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                        $template = [
                            'selected'      => 'wp_editor_1',
                            'selected_type' => 'wp_editor',
                        ];
                        update_post_meta( $duplicate_id, '_wftp_selected_design', $template );
                        if ( $content !== '' ) {
                            WFFN_Common::maybe_elementor_template( $checkout_page_id, $duplicate_id );

                            if ( $checkout_page->post_type === 'cartflows_step' || $checkout_page->post_type === 'page' ) {
                                $template = [
                                    'selected'        => 'elementor_1',
                                    'selected_type'   => 'elementor',
                                    'template_active' => 'yes'
                                ];
                                update_post_meta( $duplicate_id, '_wfacp_selected_design', $template );
                            }

                        }

                    }

                    return $duplicate_id;
                }
            }
        }

        return 0;
    }

    /**
     * @param $step_id
     *
     * @return mixed
     */
    public function get_entity_edit_link( $step_id ) {
        $link = parent::get_entity_edit_link( $step_id );
        if ( $step_id > 0 && get_post( $step_id ) instanceof WP_Post ) {
            $link = esc_url( BWF_Admin_Breadcrumbs::maybe_add_refs( add_query_arg( array(
                'page'     => 'wfacp',
                'wfacp_id' => $step_id,
            ), admin_url( 'admin.php' ) ) ) );
        }

        return $link;
    }

    /**
     * @param $step_id
     *
     * @return array
     */
    public function get_entity_tags( $step_id, $funnel_id ) {
        $wfacp_products = WFACP_Common::get_page_product( $step_id );
        $flags          = [];
        if ( count( $wfacp_products ) < 1 ) {
            $flags['no_product'] = array(
                'label'       => __( 'No Products', 'funnel-builder' ),
                'label_class' => 'bwf-st-c-badge-red',
            );
        }

        $substeps = $this->get_substeps( $funnel_id, $step_id );
        if ( ! defined( 'WFOB_PLUGIN_BASENAME' ) || ( count( $substeps ) < 1 ) || ( count( $substeps ) > 0 && ! isset( $substeps['wc_order_bump'] ) ) || ( isset( $substeps['wc_order_bump'] ) && ! is_array( $substeps['wc_order_bump'] ) ) || ( isset( $substeps['wc_order_bump'] ) && is_array( $substeps['wc_order_bump'] ) && count( $substeps['wc_order_bump'] ) < 1 ) ) {
            $flags['no_bump'] = array(
                'label'       => __( 'No Bumps', 'funnel-builder' ),
                'label_class' => 'bwf-st-c-badge-red',
            );
        }

        return $flags;
    }


    /**
     * @param $environment
     *
     * @return bool
     */
    public function claim_environment( $environment ) {
        /**
         * @todo we need to also take care of the embed forms here
         */
        if ( 'wfacp_checkout' !== $environment['post_type'] ) {
            return false;
        }
        if ( $this->is_disabled( $this->get_entity_status( $environment['id'] ) ) ) {
            return false;
        }

        return true;
    }

    /**
     * @param $environment
     *
     * @return bool|WFFN_Funnel
     */
    public function get_funnel_to_run( $environment ) {
        $get_checkout_page = $environment['id'];
        $get_funnel_id     = get_post_meta( $get_checkout_page, '_bwf_in_funnel', true );
        $get_funnel        = new WFFN_Funnel( $get_funnel_id );

        return $get_funnel;
    }

    /**
     * Save Order ID in the session to use later while treating with thankyou step
     *
     * @param $order_id
     */
    public function maybe_record_orders_in_session( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order instanceof WC_Order ) {
            return;
        }
        $get_checkout_id = $order->get_meta( '_wfacp_post_id', true );
        if ( $get_checkout_id < 1 ) {
            $get_checkout_id = isset( $_POST['_wfacp_post_id'] ) ? $_POST['_wfacp_post_id'] : 0; //phpcs:ignore
        }
        $funnel       = WFFN_Core()->data->get_session_funnel();
        $current_step = WFFN_Core()->data->get_current_step();
        if ( WFFN_Core()->data->has_valid_session() && ! empty( $current_step ) && wffn_is_valid_funnel( $funnel ) ) {
            if ( absint( $current_step['id'] ) !== absint( $get_checkout_id ) ) {
                return;
            }
            WFFN_Core()->data->set( 'wc_order', $order_id )->save();
        }

    }


    public function _get_export_metadata( $step ) {
        $new_all_meta = WFACP_Core()->export->get_acp_array_for_json( $step['id'] );

        $new_all_meta = $this->maybe_have_substeps_export( $new_all_meta, $step );

        return $new_all_meta;
    }

    public function maybe_have_substeps_export( $new_all_meta, $step ) {
        $sub_steps = [];
        if ( isset( $step['substeps'] ) && ! empty( $step['substeps'] ) ) {
            foreach ( $step['substeps'] as $key => $substeps ) {
                $sub_steps[ $key ]  = [];
                $get_substep_object = WFFN_Core()->substeps->get_integration_object( $key );
                if ( ! empty( $get_substep_object ) ) {
                    foreach ( $substeps as $substep ) {

                        $sub_steps[ $key ][] = $get_substep_object->_get_export_metadata( $substep );

                    }
                }
            }
        }
        $new_all_meta['substeps'] = $sub_steps;

        return $new_all_meta;
    }

    public function _process_import( $funnel_id, $step_data ) {
        $substeps = [];
        if ( isset( $step_data['meta']['substeps'] ) ) {
            $substeps = $step_data['meta']['substeps'];
            unset( $step_data['meta']['substeps'] );
        }
        $status = 'draft';

        if ( isset( $step_data['status'] ) && 1 === $step_data['status'] ) {
            $status = 'publish';
        }

        $meta = [];
        if ( isset( $step_data['meta']['meta'] ) ) {
            $meta = $step_data['meta']['meta'];
        }

        $post_content = ( isset( $step_data['post_content'] ) && ! empty( $step_data['post_content'] ) ) ? $step_data['post_content'] : '';
        $args         = array( 'title' => $step_data['title'], 'post_status' => $status, 'post_content' => $post_content, 'meta' => $meta );
        if ( isset( $step_data['meta']['customizer_meta'] ) ) {
            $args['customizer_meta'] = $step_data['meta']['customizer_meta'];
        }
        $ids = WFACP_Core()->import->import_from_json_data( array( $args ) );


        $posted_data = [ 'title' => $step_data['title'], 'id' => $ids[0] ];
        parent::add_step( $funnel_id, $posted_data );
        if ( ! empty( $substeps ) ) {
            foreach ( $substeps as $key => $substep ) {
                $get_substep_object = WFFN_Core()->substeps->get_integration_object( $key );
                if ( ! empty( $get_substep_object ) ) {
                    foreach ( $substep as $substep_single ) {
                        $imported_substep_id = $get_substep_object->_process_import( $substep_single );
                        $this->add_substep( $funnel_id, $ids[0], $key, array( 'id' => $imported_substep_id ) );

                    }
                }
            }
        }
        if ( isset( $step_data['template'] ) && ! empty( $step_data['template'] ) ) {
            update_post_meta( $ids[0], '_tobe_import_template', $step_data['template'] );
            update_post_meta( $ids[0], '_tobe_import_template_type', $step_data['template_type'] );
        }
    }

    public function has_import_scheduled( $id ) {
        $template = get_post_meta( $id, '_tobe_import_template', true );
        if ( ! empty( $template ) ) {
            return array(
                'template'      => $template,
                'template_type' => get_post_meta( $id, '_tobe_import_template_type', true )

            );
        }

        return false;
    }

    public function do_import( $id ) {
        $template = get_post_meta( $id, '_tobe_import_template', true );

        return WFACP_Core()->importer->import( $id, get_post_meta( $id, '_tobe_import_template_type', true ), $template );
    }

    public function update_template_data( $id, $data ) {
        $data['template_active'] = 'yes';
        WFACP_Common::update_page_design( $id, $data );
        delete_post_meta( $id, '_tobe_import_template' );
        delete_post_meta( $id, '_tobe_import_template_type' );
    }

    /**
     * @param $type
     * @param $step_id
     * @param $new_status
     *
     * @return bool
     */
    public function switch_status( $step_id, $new_status ) {
        $switched = false;
        if ( $step_id > 0 ) {
            $newstatus = empty( $new_status ) ? 'draft' : 'publish';
            $args      = [
                'ID'          => $step_id,
                'post_status' => $newstatus,
            ];

            $meta       = get_post_meta( $step_id, '_wp_page_template', true );
            $updated_id = wp_update_post( $args );

            if ( intval( $step_id ) === intval( $updated_id ) ) {
                $switched = true;
            }

            update_post_meta( $step_id, '_wp_page_template', $meta );
            WFACP_Common::save_publish_checkout_pages_in_transient();
        }

        return $switched;
    }

    /**
     * @param $get_ref
     *
     * @return mixed
     */
    public function maybe_funnel_breadcrumb( $get_ref ) {
        $step_id = filter_input( INPUT_GET, 'wfacp_id', FILTER_SANITIZE_STRING );
        if ( empty( $get_ref ) && ! empty( $step_id ) ) {
            $funnel_id = get_post_meta( $step_id, '_bwf_in_funnel', true );
            if ( ! empty( $funnel_id ) && abs( $funnel_id ) > 0 ) {
                return $funnel_id;
            }
        }

        return $get_ref;
    }

    public function maybe_mark_funnel_as_global( $new_val ) {
        $new_val = maybe_unserialize( $new_val );
        if ( is_array( $new_val ) && array_key_exists( 'override_checkout_page_id', $new_val ) && '' !== $new_val['override_checkout_page_id'] ) {
            $get_funnel    = get_post_meta( $new_val['override_checkout_page_id'], '_bwf_in_funnel', true );
            $funnel_object = new WFFN_Funnel( $get_funnel );
            WFFN_Core()->get_dB()->delete_multiple( "DELETE from {table_name_meta} WHERE `meta_key` = '_is_global'" );
            if ( wffn_is_valid_funnel( $funnel_object ) ) {
                WFFN_Core()->get_dB()->update_meta( $get_funnel, '_is_global', 'yes' );
            }
        }
    }
}

if ( class_exists( 'WFFN_Core' ) && class_exists( 'WFACP_Core' ) && wffn_is_wc_active() ) {

    if ( ! version_compare( WFACP_VERSION, '1.9.3', '>' ) ) {
        wffn_show_notice( array( 'pname' => WFACP_FULL_NAME ), 'version_mismatch' );

        return;
    }
    WFFN_Core()->steps->register( WFFN_Step_WC_Checkout::get_instance() );
}
