<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class WFFN_REST_Steps
 *
 * * @extends WP_REST_Controller
 */
class WFFN_REST_Steps extends WP_REST_Controller {

	public static $_instance = null;

	/**
	 * Route base.
	 *
	 * @var string
	 */

	protected $namespace = 'woofunnels-admin';
	protected $rest_base = 'funnels/(?P<funnel_id>[\d]+)/steps';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Register the routes for taxes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			'args' => array(
				'funnel_id' => array(
					'description' => __( 'Unique funnel id.', 'funnel-builder' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_step' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), $this->get_create_steps_collection() ),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<step_id>[\d]+)', array(
			'args' => array(
				'funnel_id' => array(
					'description' => __( 'Unique funnel id.', 'funnel-builder' ),
					'type'        => 'integer',
				),
				'step_id'   => array(
					'description' => __( 'Current step id.', 'funnel-builder' ),
					'type'        => 'integer',
				),
			),

			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_step' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ), $this->get_update_steps_collection() )
			),

			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_step' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => $this->get_delete_steps_collection()
			),

			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/funnels/step/search', array(

			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'search_entity' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => array(
					's'          => array(
						'description'       => __( 'search term', 'funnel-builder' ),
						'type'              => 'string',
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'type'       => array(
						'description'       => __( 'Type of step', 'funnel-builder' ),
						'type'              => 'string',
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'is_substep' => array(
						'description'       => __( 'if the query is for substep', 'funnel-builder' ),
						'default'           => false,
						'type'              => 'boolean',
						'sanitize_callback' => 'wffn_string_to_bool',
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
			),
		) );

	}



	public function create_step( $request ) {

		$resp = array(
			'status' => false,
			'data'   => new stdClass(),
		);

		$funnel_id = $request->get_param( 'funnel_id' );
		$funnel_id = ! empty( $funnel_id ) ? $funnel_id : 0;

		$type         = isset( $request['type'] ) ? $request['type'] : '';
		$title        = isset( $request['title'] ) ? $request['title'] : __( 'New Step', 'funnel-builder' );
		$design       = isset( $request['design'] ) ? $request['design'] : '';
		$duplicate_id = isset( $request['duplicate_id'] ) ? $request['duplicate_id'] : 0;
		$inherit_id   = isset( $request['inherit_from'] ) ? $request['inherit_from'] : 0;

		$posted_data              = array();
		$posted_data['funnel_id'] = $funnel_id;
		$posted_data['type']      = $type;


		BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel_id );

		if ( $funnel_id > 0 && ! empty( $type ) ) {
			$get_step = WFFN_Core()->steps->get_integration_object( $type );
			if ( $get_step instanceof WFFN_Step ) {
				if ( $inherit_id > 0 && '' !== $title ) {
					$posted_data['title']             = $title;
					$posted_data['design']            = $design;
					$posted_data['design_name']['id'] = $inherit_id;
					$posted_data['existing']          = 'true';
					$data                             = $get_step->duplicate_step( $funnel_id, $inherit_id, $posted_data );

				} elseif ( $duplicate_id > 0 ) {
					$posted_data['original_id']     = $duplicate_id;
					$posted_data['step_id']         = $duplicate_id;
					$posted_data['_data']           = [];
					$posted_data['_data']['title']  = $get_step->get_entity_title( $duplicate_id );
					$posted_data['_data']['desc']   = $get_step->get_entity_description( $duplicate_id );
					$posted_data['_data']['status'] = $get_step->get_entity_status( $duplicate_id );
					$posted_data['_data']['edit']   = $get_step->get_entity_edit_link( $duplicate_id );
					$posted_data['_data']['view']   = $get_step->get_entity_view_link( $duplicate_id );
					$data                           = $get_step->duplicate_step( $funnel_id, $duplicate_id, $posted_data );


				} else {
					$posted_data['title'] = $title;
					$data                 = $get_step->add_step( $funnel_id, $posted_data );
				}

				$step_data = array();

				if ( ! empty( $data ) ) {
					$funnel = new WFFN_Funnel( $funnel_id );
					$steps  = $funnel->get_steps();

					foreach ( $steps as $step ) {
						if ( $data->id === $step['id'] ) {
							$step_data = $get_step->populate_data_properties( $step, $funnel_id );
							break;
						}
					}
					$resp['data']   = $step_data;
					$resp['status'] = true;
				}

			}
		}

		return $resp;

	}

	public function get_create_steps_collection() {
		$params                 = array();
		$params['type']         = array(
			'description' => __( 'Step type.', 'funnel-builder' ),
			'type'        => 'string',
			'required'    => true,

		);
		$params['title']        = array(
			'description' => __( 'Step name.', 'funnel-builder' ),
			'type'        => 'string',

		);
		$params['design']       = array(
			'description' => __( 'Step Design.', 'funnel-builder' ),
			'type'        => 'string',
			'default'     => 'scratch'
		);
		$params['inherit_from'] = array(
			'description' => __( 'Inherit Step.', 'funnel-builder' ),
			'type'        => 'integer',
			'default'     => 0,
		);
		$params['duplicate_id'] = array(
			'description' => __( 'Duplicate Step.', 'funnel-builder' ),
			'type'        => 'integer',
			'default'     => 0,
		);

		return apply_filters( 'wffn_rest_create_steps_collection', $params );
	}

	public function update_step_permissions_check( $request ) {
		if ( 'PUT' !== $request->get_method() ) {
			return false;
		}

		return true;
	}

	public function update_step( $request ) {

		$resp = array(
			'status'   => false,
			'switched' => 0,
		);

		$funnel_id = $request->get_param( 'funnel_id' );
		$funnel_id = ! empty( $funnel_id ) ? $funnel_id : 0;

		$step_id    = $request->get_param( 'step_id' );
		$step_id    = ! empty( $step_id ) ? $step_id : 0;
		$type       = isset( $request['type'] ) ? $request['type'] : '';
		$new_status = isset( $request['new_status'] ) ? $request['new_status'] : 0;
		BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel_id );
		if ( $funnel_id === 0 || $step_id === 0 ) {
			return $resp;
		}

		if ( $funnel_id > 0 && ! empty( $type ) ) {
			$get_step = WFFN_Core()->steps->get_integration_object( $type );
			if ( $get_step instanceof WFFN_Step ) {
				$switched         = $get_step->switch_status( $step_id, $new_status );
				$resp['status']   = true;
				$resp['switched'] = $switched;
			}
		}

		return $resp;
	}

	public function get_update_steps_collection() {
		$params               = array();
		$params['type']       = array(
			'description' => __( 'Step type.', 'funnel-builder' ),
			'type'        => 'string',
			'required'    => true,
		);
		$params['new_status'] = array(
			'description' => __( 'Set step status.', 'funnel-builder' ),
			'type'        => 'integer',
			'default'     => 0,

		);

		return apply_filters( 'wffn_rest_update_steps_collection', $params );
	}

	public function delete_step_permissions_check( $request ) {
		if ( 'DELETE' !== $request->get_method() ) {
			return false;
		}

		return true;
	}

	public static function delete_step( $request ) {

		$resp = array(
			'status' => false,
		);

		$funnel_id = $request->get_param( 'funnel_id' );
		$funnel_id = ! empty( $funnel_id ) ? $funnel_id : 0;

		$step_id = $request->get_param( 'step_id' );
		$step_id = ! empty( $step_id ) ? $step_id : 0;
		$type    = isset( $request['type'] ) ? $request['type'] : '';

		if ( $funnel_id === 0 || $step_id === 0 ) {
			return $resp;
		}

		if ( $funnel_id > 0 && ! empty( $type ) ) {
			$get_step = WFFN_Core()->steps->get_integration_object( $type );
			if ( $get_step instanceof WFFN_Step ) {
				$deleted        = $get_step->delete_step( $funnel_id, $step_id );
				$resp['status'] = ( $deleted > 0 ) ? true : false;

			}
		}

		return $resp;
	}

	public function get_delete_steps_collection() {
		$params         = array();
		$params['type'] = array(
			'description' => __( 'Step type.', 'funnel-builder' ),
			'type'        => 'string',
			'required'    => true,
		);

		return apply_filters( 'wffn_rest_delete_steps_collection', $params );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function search_entity( WP_REST_Request $request ) {
		$search     = $request->get_param( 's' );
		$type       = $request->get_param( 'type' );
		$is_substep = $request->get_param( 'is_substep' );
		if ( true === $is_substep ) {
			$get_substep = WFFN_Core()->substeps->get_integration_object( $type );
			$designs     = $get_substep->get_substep_designs( $search );
		} else {
			$get_step = WFFN_Core()->steps->get_integration_object( $type );
			$designs  = $get_step->get_step_designs($search);
		}

		return rest_ensure_response( $designs );
	}

	public function get_api_permission_check() {
		return current_user_can( 'manage_options' );
	}


}

if ( ! function_exists( 'wffn_rest_steps' ) ) {

	function wffn_rest_steps() {  //@codingStandardsIgnoreLine
		return WFFN_REST_Steps::get_instance();
	}
}

wffn_rest_steps();