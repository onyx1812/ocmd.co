<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class contains all the Landing page related funnel functionality
 * Class WFFN_Step_Landing
 */
class WFFN_Step_Landing extends WFFN_Step {

	private static $ins = null;
	public $slug = 'landing';
	public $list_priority = 10;

	/**
	 * WFFN_Step_Landing constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'bwf_funnels_funnels_display_admin_footer_text', [ $this, 'maybe_show_footer_text' ], 10, 2 );
		add_filter( 'maybe_setup_funnel_for_breadcrumb', [ $this, 'maybe_funnel_breadcrumb' ] );
	}

	/**
	 * @return WFFN_Step_Landing|null
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
		return array_unique( array_merge( parent::get_supports(), [ 'open_link', 'track_views', 'track_conversions' ] ) );
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
			'dashicons'   => 'dashicons-welcome-widgets-menus',
			'label_class' => 'bwf-st-c-badge-red',
			'substeps'    => array(),
		);
	}

	/**
	 * Return title of Landing step
	 */
	public function get_title() {
		return __( 'Sales Page', 'funnel-builder' );
	}



	/**
	 * @param $step
	 *
	 * @return array
	 */
	public function get_step_designs( $term ) {
		$active_pages = WFFN_Core()->landing_pages->get_landing_pages( $term );

		$designs = array();
		foreach ( $active_pages as $active_page ) {
			$post_type = get_post_type( $active_page->ID );

			if ( 'cartflows_step' === $post_type ) {
				$meta = get_post_meta( $active_page->ID, 'wcf-step-type', true );
				if ( 'landing' === $meta ) {
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
	 * @param $type
	 * @param $posted_data
	 *
	 * @return stdClass
	 */
	public function add_step( $funnel_id, $posted_data ) {
		$title = isset( $posted_data['title'] ) ? $posted_data['title'] : '';

		$step_id = wp_insert_post( array(
			'post_type'    => WFFN_Core()->landing_pages->get_post_type_slug(),
			'post_title'   => $title,
			'post_name'    => sanitize_title( $title ),
			'post_status'  => 'publish',
			'post_content' => isset( $posted_data['post_content'] ) ? $this->get_content_images($posted_data['post_content'] ) : '',
		) );

		$posted_data['id'] = ( $step_id > 0 ) ? $step_id : 0;
		if ( $step_id > 0 ) {
			update_post_meta( $step_id, '_wp_page_template', 'wflp-boxed.php' );
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
		$duplicate_id      = WFFN_Core()->landing_pages->duplicate_landing_page( $step_id );
		$posted_data['id'] = 0;

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
	 * @return bool
	 */
	public function claim_environment( $environment ) {
		if ( 'wffn_landing' !== $environment['post_type'] ) {
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
		$get_landing_page = $environment['id'];
		$get_funnel_id    = get_post_meta( $get_landing_page, '_bwf_in_funnel', true );
		$get_funnel       = new WFFN_Funnel( $get_funnel_id );

		return $get_funnel;
	}

	/**
	 * @param $step_id
	 *
	 * @return mixed
	 */
	public function get_entity_edit_link( $step_id ) {
		return esc_url( BWF_Admin_Breadcrumbs::maybe_add_refs( add_query_arg( [
			'page'    => 'wf-lp',
			'edit'    => $step_id,
			'section' => 'design',
		], admin_url( 'admin.php' ) ) ) );
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

	/**
	 * @param $id
	 *
	 * @return array|bool
	 */
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
		WFFN_Core()->landing_pages->update_page_design( $id, $data );
	}

	public function do_import( $id ) {
		$template = get_post_meta( $id, '_tobe_import_template', true );

		return WFFN_Core()->importer->import_remote( $id, get_post_meta( $id, '_tobe_import_template_type', true ), $template, 'landing' );
	}

	public function maybe_show_footer_text( $existing, $current_screen ) {
		return ( $current_screen === 'woofunnels_page_wf-lp' ) ? true : $existing;
	}

	/**
	 * Find the next url to open in the funnel
	 *
	 * @param $landing_id
	 * @param $funnel_id
	 *
	 * @return bool|false|string
	 */
	public function get_next_url( $landing_id, $funnel_id ) {

		$get_funnel = WFFN_Core()->admin->get_funnel( $funnel_id );

		$get_next_step   = WFFN_Core()->data->get_next_step( $get_funnel, $landing_id );
		$get_step_object = WFFN_Core()->steps->get_integration_object( $get_next_step['type'] );

		if ( ! empty( $get_step_object ) && $get_step_object->supports( 'open_link' ) ) {

			$properties = $get_step_object->populate_data_properties( $get_next_step, $funnel_id );

			if ( $get_step_object->is_disabled( $get_step_object->get_enitity_data( $properties['_data'], 'status' ) ) ) {

				return $this->get_next_url( $get_next_step['id'], $funnel_id );
			}

			return $get_step_object->get_url( $get_next_step['id'] );
		}

		return false;
	}

	public function mark_step_converted( $step_data ) {
		$landing_id = isset( $step_data['id'] ) ? $step_data['id'] : 0;
		if ( $landing_id > 0 ) {
			WFCO_Model_Report_views::update_data( gmdate( 'Y-m-d', current_time( 'timestamp' ) ), $landing_id, 3 );
		}
		do_action( 'wffn_event_step_converted', $landing_id, $step_data );
		do_action( 'wffn_event_step_converted_' . $this->slug, $landing_id, $step_data );
	}

	public function mark_step_viewed() {
		$current_page = WFFN_Core()->data->get_current_step();
		$landing_id   = isset( $current_page['id'] ) ? $current_page['id'] : 0;
		if ( $landing_id > 0 ) {
			WFFN_Core()->logger->log( __FUNCTION__ . ':: ' . $landing_id );
			WFCO_Model_Report_views::update_data( gmdate( 'Y-m-d', current_time( 'timestamp' ) ), $landing_id, 2 );
		}
		do_action( 'wffn_event_step_viewed', $landing_id, $current_page );
		do_action( 'wffn_event_step_viewed_' . $this->slug, $landing_id, $current_page );
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

if ( class_exists( 'WFFN_Core' ) ) {
	WFFN_Core()->steps->register( WFFN_Step_Landing::get_instance() );
}
