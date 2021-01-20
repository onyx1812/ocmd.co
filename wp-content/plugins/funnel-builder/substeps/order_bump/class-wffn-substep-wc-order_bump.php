<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class contains all the Order bump related funnel functionality
 * Class WFFN_Substep_WC_Order_Bump
 */
class WFFN_Substep_WC_Order_Bump extends WFFN_Substep {

    private static $ins = null;
    public $slug = 'wc_order_bump';

    /**
     * WFFN_Substep_WC_Order_Bump constructor.
     */
    public function __construct() {
        parent::__construct();
        add_filter( 'wfob_bumps_from_external_base', array( $this, 'filter_bumps' ), 10, 2 );
        add_filter( 'wfob_add_control_meta_query', array( $this, 'exlude_from_query' ), 10, 1 );
        add_filter( 'maybe_setup_funnel_for_breadcrumb', [ $this, 'maybe_funnel_breadcrumb' ] );
    }

    /**
     * @return WFFN_Substep_WC_Order_Bump|null
     */
    public static function get_instance() {
        if ( null === self::$ins ) {
            self::$ins = new self;
        }

        return self::$ins;
    }


    /**
     * @param $substep
     *
     * @return array
     */
    public function get_substep_data() {
        return array(
            'type'        => $this->slug,
            'legend'      => $this->get_title(),
            'name'        => __( 'Name', 'funnel-builder' ),
            'products'    => __( 'Products', 'funnel-builder' ),
            'drag'        => __( 'Drag to reorder', 'funnel-builder' ),
            'popup_title' => __( 'Add Bump', 'funnel-builder' ),
            'add_btn'     => __( '+ Add Bump', 'funnel-builder' ),
            'submit_btn'  => __( 'Add', 'funnel-builder' ),
            'no_step'     => __( 'No Bumps in the Checkout.', 'funnel-builder' ),
            'no_product'  => __( 'Currently no product is added in this bump.', 'funnel-builder' ),
        );
    }

    /**
     * Return title of order_bump substep
     */
    public function get_title() {
        return __( 'Order Bump', 'funnel-builder' );
    }

    /**
     * @param $step
     *
     * @return array
     */
    public function get_substep_designs( $term ) {
        $designs = [];
        $args    = array(
            'post_type'      => WFOB_Common::get_bump_post_type_slug(),
            'post_status'    => 'any',
            'posts_per_page' => WFOB_Common::posts_per_page(),

        );

        if ( ! empty( $term ) ) {
            if ( is_numeric( $term ) ) {
                $args['p'] = $term;
            } else {
                $args['s'] = $term;
            }
        }

        $q = new WP_Query( $args );
        if ( $q->found_posts > 0 ) {
            foreach ( $q->posts as $bump_post ) {
                $designs[] = array(
                    'id'   => $bump_post->ID,
                    'name' => $bump_post->post_title . ' (#' . $bump_post->ID . ')',
                );
            }
        }

        return $designs;
    }

    /**
     * @param $decided_bumps
     * @param $posted_data
     *
     * @return array
     */
    public function filter_bumps( $decided_bumps, $posted_data ) {
        $funnel       = WFFN_Core()->data->get_session_funnel();
        $current_step = WFFN_Core()->data->get_current_step();


        if ( WFFN_Core()->data->has_valid_session() && ! empty( $current_step ) && wffn_is_valid_funnel( $funnel ) && ! empty( $posted_data['_wfacp_post_id'] ) && absint( $current_step['id'] ) === absint( $posted_data['_wfacp_post_id'] ) ) {

            $current_step['id'] = apply_filters( 'bwf_wffn_should_show_parent_bump', false ) ? apply_filters( 'wffn_maybe_get_ab_control', $current_step['id'] ) : $current_step['id'];

            return array_map( 'get_post', $this->maybe_get_substeps( $current_step, $funnel ) );
        }

        return $decided_bumps;
    }

    /**
     * @param $funnel_id
     * @param $step_id
     * @param $substep
     * @param $posted_data
     *
     * @return stdClass
     */
    public function add_substep( $funnel_id, $step_id, $substep, $posted_data ) {
        $title        = isset( $posted_data['title'] ) ? $posted_data['title'] : '';
        $duplicate_id = isset( $posted_data['design_name']['id'] ) ? $posted_data['design_name']['id'] : 0;

        if ( $step_id > 1 ) {
            $post                = array();
            $post['post_title']  = $title;
            $post['post_type']   = WFOB_Common::get_bump_post_type_slug();
            $post['post_status'] = 'publish';

            $menu_order = WFOB_Common::get_highest_menu_order();

            $post['menu_order'] = $menu_order + 1;

            if ( $duplicate_id > 0 ) {

                $substep_id = WFOB_Common::make_duplicate( $duplicate_id );
                wp_update_post( array(
                    'ID'          => $substep_id,
                    'post_title'  => $title,
                    'post_status' => 'publish',
                ) );

            } else {
                $substep_id = wp_insert_post( $post );
            }
            if ( ! is_wp_error( $substep_id ) && $substep_id > 0 ) {
                update_post_meta( $substep_id, '_wfob_version', WFOB_VERSION );

                $posted_data['id']              = $substep_id;
                $posted_data['_data']           = new stdClass();
                $posted_data['_data']->products = $this->get_products( $substep_id );
            }
        }

        return parent::add_substep( $funnel_id, $step_id, $substep, $posted_data );
    }

    /**
     * @param $substep_id
     *
     * @return array
     */
    public function get_products( $substep_id ) {
        $products      = get_post_meta( $substep_id, '_wfob_selected_products', true );
        $bump_products = array();

        $discount_html = WFFN_Common::get_discount_type_keys();

        foreach ( ( is_array( $products ) && count( $products ) > 0 ) ? $products : array() as $product ) {

            $bump_products[ $product['id'] ] = array(
                'title'           => $product['title'],
                'qty'             => $product['quantity'],
                'discount_html'   => $discount_html[ $product['discount_type'] ],
                'discount_amount' => $product['discount_amount'],
            );
        }

        return $bump_products;
    }

    /**
     * @param $funnel_id
     * @param $step_id
     * @param $duplicate_step_id
     * @param $subtype
     * @param $substep_id
     * @param $substep_key
     * @param $duplicated_substeps
     *
     * @return mixed
     */
    public function duplicate_single_substep( $funnel_id, $step_id, $duplicate_step_id, $subtype, $substep_id, $substep_key = 0, $duplicated_substeps = [] ) {
        $duplicate_substep_id = WFOB_Common::make_duplicate( $substep_id );

        if ( $duplicate_substep_id > 0 ) {
            $post_status = get_post_status( $substep_id );
            wp_update_post( [ 'ID' => $duplicate_substep_id, 'post_status' => $post_status ] );
        }

        $duplicated_substeps[ $subtype ][ $substep_key ] = array();

        $duplicated_substeps[ $subtype ][ $substep_key ]['id']    = $duplicate_substep_id;
        $duplicated_substeps[ $subtype ][ $substep_key ]['_data'] = new stdClass();

        $duplicated_substeps[ $subtype ][ $substep_key ]['_data']->products = $this->get_products( $duplicate_substep_id );

        return parent::duplicate_single_substep( $funnel_id, $step_id, $duplicate_step_id, $subtype, $substep_id, $substep_key, $duplicated_substeps );
    }

    /**
     * @param $substep_arr
     *
     * @return array
     */
    public function populate_substeps_data_properties( $substep_arr ) {
        $substeps = array();
        foreach ( is_array( $substep_arr ) ? $substep_arr : array() as $substep_id ) {
            $substep_data                  = array();
            $substep_data['id']            = $substep_id;
            $substep_data['tags']          = $this->get_substep_entity_tags( $substep_id );
            $substep_data['_data']         = new stdClass();
            $substep_data['_data']->title  = $this->get_entity_title( $substep_id );
            $substep_data['_data']->edit   = $this->get_entity_edit_link( $substep_id );
            $substep_data['_data']->view   = $this->get_entity_view_link( $substep_id );
            $substep_data['_data']->status = $this->get_entity_status( $substep_id );
            $substeps[]                    = $substep_data;
        }

        return $substeps;
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
                'page'    => 'wfob',
                'section' => 'products',
                'wfob_id' => $step_id,
            ), admin_url( 'admin.php' ) ) ) );
        }

        return $link;
    }


    public function _get_export_metadata( $substep ) {
        return WFOB_Core()->export->get_bump_array_for_json( $substep['id'] );
    }

    public function _process_import( $substep ) {
        $iport_id = WFOB_Core()->import->import_from_json_data( [ $substep ] );

        return $iport_id[0];
    }

    /**
     * @param $substep_id
     *
     * @return array
     */
    public function get_substep_entity_tags( $substep_id ) {
        $product_meta = get_post_meta( $substep_id, '_wfob_selected_products', true );
        $flags        = [];
        if ( ! is_array( $product_meta ) || count( $product_meta ) < 1 ) {
            $flags['no_product'] = array(
                'label'       => __( 'No Products', 'funnel-builder' ),
                'label_class' => 'bwf-st-c-badge-red',
            );
        }

        return $flags;
    }

    /**
     * @param $get_ref
     *
     * @return mixed
     */
    public function maybe_funnel_breadcrumb( $get_ref ) {
        $step_id = filter_input( INPUT_GET, 'wfob_id', FILTER_SANITIZE_STRING );
        if ( empty( $get_ref ) && ! empty( $step_id ) ) {
            $funnel_id = get_post_meta( $step_id, '_bwf_in_funnel', true );
            if ( ! empty( $funnel_id ) && abs( $funnel_id ) > 0 ) {
                return $funnel_id;
            }
        }

        return $get_ref;
    }
}

if ( class_exists( 'WFFN_Core' ) && class_exists( 'WFOB_Core' ) ) {
    WFFN_Core()->substeps->register( WFFN_Substep_WC_Order_Bump::get_instance() );
}
