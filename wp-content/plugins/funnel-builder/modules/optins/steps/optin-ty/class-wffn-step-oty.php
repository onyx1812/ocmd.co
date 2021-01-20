<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class contains all the thank you optin page related funnel functionality
 * Class WFFN_Step_Oty
 */
class WFFN_Step_Oty extends WFFN_Step {

	private static $ins = null;
	public $slug = 'optin_ty';
	public $list_priority = 18;

	/**
	 * WFFN_Step_Oty constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'bwf_funnels_funnels_display_admin_footer_text', [ $this, 'maybe_show_footer_text' ], 10, 2 );
		add_filter( 'maybe_setup_funnel_for_breadcrumb', [ $this, 'maybe_funnel_breadcrumb' ] );
        add_action( 'wp_enqueue_scripts', array( $this, 'maybe_add_script' ) );
	}

	/**
	 * @return WFFN_Step_Oty|null
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
		return array_unique( array_merge( parent::get_supports(), ['track_views', 'track_conversions', 'close_funnel' ] ) );
	}

	/**
	 * @return array
	 */
	public function get_step_data() {
		return array(
			'type'        => $this->slug,
			'title'       => $this->get_title(),
			'popup_title' => __( 'Add Thank You Optin Page', 'funnel-builder' ),
			'dashicons'   => 'dashicons-thumbs-up',
			'label'       => __( 'No Products', 'funnel-builder' ),
			'label_class' => 'wf_funnel_btn_red',
			'desc'        => __( 'Set up thank you optin pages with your favorite page builder', 'funnel-builder' ),
			'substeps'    => array(),
			'color'       => '#656565',
		);
	}

	/**
	 * Return title of Thank You optin step
	 */
	public function get_title() {
		return __( 'Optin Thank You', 'funnel-builder' );
	}

	/**
	 * @return array
	 */
	public function get_popup_data() {
		return array(
			'title'   => __( 'Great! thank you optin page has been successfully added', 'funnel-builder' ),
			'deleted' => __( 'Optin thank you optin page has been successfully deleted.', 'funnel-builder' ),
		);
	}

	/**
	 * @return array
	 */
	public function get_loader_popup() {
		return array(
			'title' => __( 'Adding the Thank You Optin Page', 'funnel-builder' ),
		);
	}

	/**
	 * @param $step
	 *
	 * @return array
	 */
	public function get_step_designs( $term ) {
		$active_pages = WFOPP_Core()->optin_ty_pages->get_oty_pages( $term );
		$designs      = array();

		foreach ( $active_pages as $active_page ) {
			$post_type = get_post_type( $active_page->ID );

			if ( 'cartflows_step' === $post_type ) {
				$meta = get_post_meta( $active_page->ID, 'wcf-step-type', true );
				if ( 'thankyou' === $meta ) {
					$data = array(
						'id'   => $active_page->ID,
						'name' => $active_page->post_title.' (#'.$active_page->ID.')',
					);
				}
			} else {
				$data = array(
					'id'   => $active_page->ID,
					'name' => $active_page->post_title.' (#'.$active_page->ID.')',
				);

			}
			$designs[] = $data;
		}

		return $designs;
	}

	/**
	 * @param $funnel_id
	 * @param $posted_data
	 *
	 * @return stdClass
	 */
	public function add_step( $funnel_id, $posted_data ) {
		$title = isset( $posted_data['title'] ) ? $posted_data['title'] : '';

		$step_id = wp_insert_post( array(
			'post_type'   => WFOPP_Core()->optin_ty_pages->get_post_type_slug(),
			'post_title'  => $title,
			'post_name'   => sanitize_title( $title ),
			'post_status' => 'publish',
			'post_content' => isset( $posted_data['post_content'] ) ? $this->get_content_images($posted_data['post_content'] ) : '',
		) );

		$posted_data['id'] = ( $step_id > 0 ) ? $step_id : 0;
		if ( $step_id > 0 ) {
			update_post_meta( $step_id, '_wp_page_template', 'wfoty-boxed.php' );
		}

		return parent::add_step( $funnel_id, $posted_data );
	}

	/**
	 * @param $funnel_id
	 * @param $step_id
	 * @param $posted_data
	 *
	 * @return stdClass
	 */
	public function duplicate_step( $funnel_id, $step_id, $posted_data ) {
		$duplicate_id      = WFOPP_Core()->optin_ty_pages->duplicate_oty_page( $step_id );
		$posted_data['id'] = ( $duplicate_id > 0 ) ? $duplicate_id : 0;

		if ( $duplicate_id > 0 ) {
			$posted_data['id'] = $duplicate_id;
			$new_title = isset( $posted_data['title'] ) ? $posted_data['title'] : '';
			$arr = [ 'ID' => $duplicate_id ];

			if ( ! empty( $new_title ) ) {
				$arr['post_title'] = $new_title;
			}
			wp_update_post( $arr );
		}

		return parent::duplicate_step( $funnel_id, $duplicate_id, $posted_data );
	}

	/**
	 * @param $environment
	 *
	 * @return bool
	 */
	public function claim_environment( $environment ) {
		if ( 'wffn_oty' !== $environment['post_type'] ) {
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
		$get_oty_page  = $environment['id'];
		$get_funnel_id = get_post_meta( $get_oty_page, '_bwf_in_funnel', true );
		$get_funnel    = new WFFN_Funnel( $get_funnel_id );

		return $get_funnel;
	}

	/**
	 * @param $step_id
	 *
	 * @return mixed
	 */
	public function get_entity_edit_link( $step_id ) {
		return esc_url( BWF_Admin_Breadcrumbs::maybe_add_refs( add_query_arg( [
			'page'    => 'wf-oty',
			'edit'    => $step_id,
			'section' => 'design',
		], admin_url( 'admin.php' ) ) ) );
	}

	public function get_color() {
		return '#1ec9e4';
	}

	public function _get_export_metadata( $step ) {
		$new_all_meta = get_post_meta( $step['id'] );

		return $new_all_meta;
	}

	public function _process_import( $funnel_id, $step_data ) {
		$posted_data = [ 'title' => $step_data['title'], 'status' => $step_data['status'], 'post_content' => $step_data['post_content'] ];
		$data        = $this->add_step( $funnel_id, $posted_data );
		$this->copy_metadata( $data->id, $step_data['meta'] );

		if ( isset( $step_data['meta']['_elementor_data'] ) ) {
			$content        = $step_data['meta']['_elementor_data'];
			$obj            = new WFFN_Elementor_Importer();
			$elementor_data = is_string( $content ) ? $content : wp_json_encode( $content );
			$obj->import( $data->id, $elementor_data );
		}

		if ( isset( $step_data['meta']['_wp_page_template'] ) ) {
			update_post_meta( $data->id, '_wp_page_template', $step_data['meta']['_wp_page_template'] );
		}

		if ( isset( $step_data['template'] ) && ! empty( $step_data['template'] ) ) {
			update_post_meta( $data->id, '_tobe_import_template', $step_data['template'] );
			update_post_meta( $data->id, '_tobe_import_template_type', $step_data['template_type'] );
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

	public function update_template_data( $id, $data ) {
		WFOPP_Core()->optin_ty_pages->update_page_design( $id, $data );
	}

	public function do_import( $id ) {
		$template = get_post_meta( $id, '_tobe_import_template', true );

		return WFFN_Core()->importer->import_remote( $id, get_post_meta( $id, '_tobe_import_template_type', true ), $template, 'optin_ty' );
	}

	public function maybe_show_footer_text( $existing, $current_screen ) {
		return ( $current_screen === 'woofunnels_page_wf-oty' ) ? true : $existing;
	}

	public function mark_step_viewed() {
		$current_page = WFFN_Core()->data->get_current_step();
		$oty_id       = isset( $current_page['id'] ) ? $current_page['id'] : 0;
		if ( $oty_id > 0 ) {
			WFFN_Core()->logger->log( __FUNCTION__ . ':: ' . $oty_id );
			$this->increase_oty_visit_session_view( $oty_id );
		}
		do_action( 'wffn_event_step_viewed', $oty_id, $current_page );
		do_action( 'wffn_event_step_viewed_' . $this->slug, $oty_id, $current_page );
	}

	/**
	 * @param $step_data
	 */
	public function mark_step_converted( $step_data ) {
		$oty_id = isset( $step_data['id'] ) ? $step_data['id'] : 0;
		if ( $oty_id > 0 ) {
			WFCO_Model_Report_views::update_data( gmdate( 'Y-m-d', current_time( 'timestamp' ) ), $oty_id, 11 );
		}
		do_action( 'wffn_event_step_converted', $oty_id, $step_data );
		do_action( 'wffn_event_step_converted_' . $this->slug, $oty_id, $step_data );
	}

	public function increase_oty_visit_session_view( $oty_id ) {
		if ( $oty_id < 1 ) {
			return;
		}
		WFCO_Model_Report_views::update_data( gmdate( 'Y-m-d', current_time( 'timestamp' ) ), $oty_id, 10 );
	}

	/**
	 * @param $get_ref
	 *
	 * @return mixed
	 */
	public function maybe_funnel_breadcrumb( $get_ref ) {
		$step_id = filter_input( INPUT_GET, 'edit', FILTER_SANITIZE_STRING );
		if ( empty( $get_ref ) && ! empty( $step_id ) ) {
			$funnel_id = get_post_meta( $step_id, '_bwf_in_funnel', true );
			if ( ! empty( $funnel_id ) && abs( $funnel_id ) > 0 ) {
				return $funnel_id;
			}
		}
		return $get_ref;
	}

    public function maybe_add_script() {

        if ( WFOPP_Core()->optin_ty_pages->is_wfoty_page() === true ) {

            $funnel       = WFFN_Core()->data->get_session_funnel();
            $current_step = WFFN_Core()->data->get_current_step();
            if ( WFFN_Core()->data->has_valid_session() && ! empty( $current_step ) && wffn_is_valid_funnel( $funnel ) ) {
                WFFN_Core()->data->set( 'current_step', [
                    'id'   => WFOPP_Core()->optin_ty_pages->op_thankyoupage_id,
                    'type' => $this->slug,
                ] );
                WFFN_Core()->data->save();

                /**
                 * Setup the funnel result array to make sure js work clean
                 */
                WFFN_Core()->public->funnel_setup_result = array(
                    'success'      => true,
                    'current_step' => [
                        'id'   => WFOPP_Core()->optin_ty_pages->op_thankyoupage_id,
                        'type' => $this->slug,
                    ],
                    'hash'         => WFFN_Core()->data->get_transient_key(),
                    'next_link'    => '',
                );
                WFFN_Core()->public->maybe_add_script();
            }
        }
    }
}

if ( class_exists( 'WFFN_Core' ) ) {
	WFFN_Core()->steps->register( WFFN_Step_Oty::get_instance() );
}
