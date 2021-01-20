<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class contains all the thank you page related functionality
 * Class WFFN_Step_WC_Thankyou
 */
class WFFN_Step_WC_Thankyou extends WFFN_Step {

	private static $ins = null;
	public $slug = 'wc_thankyou';
	public $list_priority = 40;

	/**
	 * WFFN_Step_WC_Thankyou constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'wffn_wfty_filter_page_ids', array( $this, 'maybe_filter_thankyou' ), 10, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_add_script' ) );
		add_action( 'bwf_funnels_funnels_display_admin_footer_text', [ $this, 'maybe_show_footer_text' ], 10, 2 );
		add_filter( 'maybe_setup_funnel_for_breadcrumb', [ $this, 'maybe_funnel_breadcrumb' ] );
	}

	/**
	 * @return WFFN_Step_WC_Thankyou|null
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
		return array_unique( array_merge( parent::get_supports(), [ 'track_views', 'track_conversions', 'close_funnel' ] ) );
	}

	/**
	 * @param $steps
	 *
	 * @return array
	 */
	public function get_step_data() {
		return array(
			'type'        => $this->slug,
			'title'       => $this->get_title(),
			'popup_title' => sprintf( __( 'Add %s', 'funnel-builder' ), $this->get_title() ),
			'dashicons'   => 'dashicons-thumbs-up',
			'label_class' => 'bwf-st-c-badge-red',
			'substeps'    => array(),
		);
	}

	/**
	 * Return title of thank you step
	 */
	public function get_title() {
		return __( 'Thank You Page', 'funnel-builder' );
	}


	/**
	 * @param $step
	 *
	 * @return array
	 */
	public function get_step_designs( $term ) {
		$active_pages = WFFN_Core()->thank_you_pages->get_thank_you_pages( $term );
		$designs      = array();

		foreach ( $active_pages as $active_page ) {
			$post_type = get_post_type( $active_page->ID );

			if ( 'cartflows_step' === $post_type ) {
				$meta = get_post_meta( $active_page->ID, 'wcf-step-type', true );
				if ( 'thankyou' === $meta ) {
					$data      = array(
						'id'   => $active_page->ID,
						'name' => $active_page->post_title.' (#'.$active_page->ID.')',
					);
					$designs[] = $data;
				}
			} else {
				$data      = array(
					'id'   => $active_page->ID,
					'name' => $active_page->post_title.' (#'.$active_page->ID.')',
				);
				$designs[] = $data;
			}

		}

		return $designs;
	}

	/**
	 * @param $funnel_id
	 * @param $step
	 * @param $posted_data
	 *
	 * @return stdClass
	 */
	public function add_step( $funnel_id, $posted_data ) {
		$title               = isset( $posted_data['title'] ) ? $posted_data['title'] : '';
		$thank_you_page_data = array(
			'post_type'   => WFFN_Core()->thank_you_pages->get_post_type_slug(),
			'post_title'  => $title,
			'post_name'   => sanitize_title( $title ),
			'post_status' => 'publish',
			'post_content' => isset( $posted_data['post_content'] ) ? $this->get_content_images($posted_data['post_content'] ) : '',
		);
		$step_id             = WFFN_Core()->thank_you_pages->insert_thank_you_page( $thank_you_page_data );
		$posted_data['id']   = ( $step_id > 0 ) ? $step_id : 0;

		if ( $step_id > 0 ) {
			update_post_meta( $step_id, '_wp_page_template', 'wftp-boxed.php' );
		}

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
		$duplicate_id      = WFFN_Core()->thank_you_pages->duplicate_thank_you_page( $step_id );
		$posted_data['id'] = ( $duplicate_id > 0 ) ? $duplicate_id : 0;

		$post_status = ( isset( $posted_data['original_id'] ) && $posted_data['original_id'] > 0 ) ? get_post_status( $posted_data['original_id'] ) : 'publish';

		if ( $duplicate_id > 0 ) {
			$posted_data['id'] = $duplicate_id;
			$new_title = isset( $posted_data['title'] ) ? $posted_data['title'] : '';
			$arr = [ 'ID' => $duplicate_id, 'post_status' => $post_status ];

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
	 * @return bool|WFFN_Funnel
	 */
	public function get_funnel_to_run( $environment ) {
		$get_thankyou_page = $environment['id'];
		$get_funnel_id     = get_post_meta( $get_thankyou_page, '_bwf_in_funnel', true );
		$get_funnel        = new WFFN_Funnel( $get_funnel_id );

		return $get_funnel;
	}

	/**
	 * @param $thankyou_page_ids
	 * @param $order
	 *
	 * @return array
	 */
	public function maybe_filter_thankyou( $thankyou_page_ids ) {

		$funnel          = WFFN_Core()->data->get_session_funnel();
		$current_step    = WFFN_Core()->data->get_current_step();
		$current_step_id = isset( $current_step['id'] ) ? $current_step['id'] : 0;
		if ( $current_step_id > 0 ) {
			$current_step['id'] = apply_filters( 'wffn_maybe_get_ab_control', $current_step_id );
		}

		if ( WFFN_Core()->data->has_valid_session() && ! empty( $current_step ) && wffn_is_valid_funnel( $funnel ) && $this->validate_environment( $current_step ) ) {
			return $this->maybe_get_thankyou( $current_step, $funnel );
		}

		return $thankyou_page_ids;
	}

	/**
	 * @param $current_step
	 *
	 * @return bool
	 */
	public function validate_environment( $current_step, $order = false ) {

		if ( ! $order instanceof WC_Order ) {
			$orderID = WFFN_Core()->data->get( 'wc_order' );
			$order   = wc_get_order( $orderID );
		}

		if ( ! $order instanceof WC_Order ) {
			WFFN_Core()->logger->log( 'No Order found.' );

			return false;
		}
        $order->read_meta_data();
		$get_checkout_id = $order->get_meta( '_wfacp_post_id', true );
		$current_step    = WFFN_Core()->data->get_current_step();
		$wfacp_id        = $get_checkout_id;


		if ( absint( $current_step['id'] ) === absint( $wfacp_id ) ) {
			return true;
		}
		return false;
	}

	/**
	 * @param $current_step
	 * @param $funnel
	 *
	 * @return array
	 */
	public function maybe_get_thankyou( $current_step, $funnel ) {
		$found_step         = false;
		$all_funnels        = [];
		$targets_step_found = false;
		foreach ( $funnel->steps as $key => $step ) {

			/**
			 * continue till we found the current step
			 */
			if ( absint( $current_step['id'] ) === absint( $step['id'] ) ) {
				$found_step = $key;
				continue;
			}

			/**
			 * Continue if we have not found the current step yet
			 */
			if ( false === $found_step ) {
				continue;
			}
			/**
			 * if step is not the type after the current step then break the loop
			 */
			if ( false !== $found_step && $this->slug !== $step['type'] && true === $targets_step_found ) {
				break;
			}
			if ( false !== $found_step && $this->slug !== $step['type'] ) {
				continue;
			}
			/**
			 * if we have found the current step and type is upsell then connect
			 */
			if ( false !== $found_step && $this->slug === $step['type'] ) {
				$properties = $this->populate_data_properties( $step, $funnel->get_id() );

				if ( $this->is_disabled( $this->get_enitity_data( $properties['_data'], 'status' ) ) ) {
					continue;
				}

				$all_funnels[]      = $step['id'];
				$targets_step_found = true;
				continue;
			}
		}

		return $all_funnels;
	}

	/**
	 * @param $step_id
	 *
	 * @return mixed
	 */
	public function get_entity_edit_link( $step_id ) {
		return esc_url( BWF_Admin_Breadcrumbs::maybe_add_refs( add_query_arg( [
			'page'    => 'wf-ty',
			'edit'    => $step_id,
			'section' => 'design',
		], admin_url( 'admin.php' ) ) ) );
	}

	public function maybe_add_script() {

		if ( WFFN_Core()->thank_you_pages->is_wfty_page() === true ) {

			$funnel       = WFFN_Core()->data->get_session_funnel();
			$current_step = WFFN_Core()->data->get_current_step();
			$order        = WFFN_Core()->thank_you_pages->data->get_order();
			if ( WFFN_Core()->data->has_valid_session() && ! empty( $current_step ) && wffn_is_valid_funnel( $funnel ) && $this->validate_environment( $current_step, $order ) ) {
				WFFN_Core()->data->set( 'current_step', [
					'id'   => WFFN_Core()->thank_you_pages->thankyoupage_id,
					'type' => $this->slug,
				] );
				WFFN_Core()->data->save();

				/**
				 * Setup the funnel result array to make sure js work clean
				 */
				WFFN_Core()->public->funnel_setup_result = array(
					'success'      => true,
					'current_step' => [
						'id'   => WFFN_Core()->thank_you_pages->thankyoupage_id,
						'type' => $this->slug,
					],
					'hash'         => WFFN_Core()->data->get_transient_key(),
					'next_link'    => '',
				);
				WFFN_Core()->public->maybe_add_script();
			}
		}
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
		WFFN_Core()->thank_you_pages->update_page_design( $id, $data );
	}

	public function do_import( $id ) {
		$template = get_post_meta( $id, '_tobe_import_template', true );

		return WFFN_Core()->importer->import_remote( $id, get_post_meta( $id, '_tobe_import_template_type', true ), $template, 'wc_thankyou' );
	}

	public function maybe_show_footer_text( $existing, $current_screen ) {
		return ( $current_screen === 'woofunnels_page_wf-ty' ) ? true : $existing;
	}

	public function mark_step_viewed() {
		$current_page = WFFN_Core()->data->get_current_step();

		$thankyou_id = isset( $current_page['id'] ) ? $current_page['id'] : 0;
		if ( $thankyou_id > 0 ) {
			$this->increase_thankyou_visit_wc_session_view( $thankyou_id );
		}
		do_action( 'wffn_event_step_viewed', $thankyou_id, $current_page );
		do_action( 'wffn_event_step_viewed_' . $this->slug, $thankyou_id, $current_page );
	}

	public function increase_thankyou_visit_wc_session_view( $thankyou_id ) {
		if ( $thankyou_id < 1 ) {
			return;
		}
		WFCO_Model_Report_views::update_data( gmdate( 'Y-m-d', current_time( 'timestamp' ) ), $thankyou_id, 5 );
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
}

if ( class_exists( 'WFFN_Core' ) && ! empty( WFFN_Core()->thank_you_pages ) ) {
    WFFN_Core()->steps->register( WFFN_Step_WC_Thankyou::get_instance() );
}
