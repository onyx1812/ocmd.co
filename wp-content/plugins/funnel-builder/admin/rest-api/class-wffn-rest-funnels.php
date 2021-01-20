<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class WFFN_REST_Funnels
 *
 * * @extends WP_REST_Controller
 */
class WFFN_REST_Funnels extends WP_REST_Controller {

	public static $_instance = null;

	/**
	 * Route base.
	 *
	 * @var string
	 */

	protected $namespace = 'woofunnels-admin';
	protected $rest_base = 'funnels';
	protected $rest_base_id = 'funnels/(?P<funnel_id>[\d]+)/';

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
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_funnel' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), $this->get_create_funnels_collection() ),
			),
		) );


		register_rest_route( $this->namespace, '/' . $this->rest_base_id, array(
			'args' => array(
				'funnel_id' => array(
					'description' => __( 'Unique funnel id.', 'funnel-builder' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_funnel' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => [],
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_funnel' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => array(
					'funnel_id'   => array(
						'description'       => __( 'Funnel ID', 'funnel-builder' ),
						'type'              => 'string',
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'title'       => array(
						'description'       => __( 'title', 'funnel-builder' ),
						'type'              => 'string',
						'validate_callback' => 'rest_validate_request_arg',
					),
					'description' => array(
						'description'       => __( 'description', 'funnel-builder' ),
						'type'              => 'string',
						'validate_callback' => 'rest_validate_request_arg',
					),
					'steps'       => array(
						'description'       => __( 'steps', 'funnel-builder' ),
						'type'              => 'string',
						'validate_callback' => 'rest_validate_request_arg',
						'sanitize_callback' => array( $this, 'sanitize_custom' ),
					),
				),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<funnel_id>[\d]+)/import-status', array(
			'args' => array(
				'funnel_id' => array(
					'description' => __( 'Unique funnel id.', 'funnel-builder' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'funnel_import_status' ),
				'permission_callback' => array( $this, 'get_api_permission_check' ),
				'args'                => [],
			),

			'schema' => array( $this, 'get_public_item_schema' ),
		) );

	}

	public function get_api_permission_check() {

		return  current_user_can( 'manage_options' );
	}

	public function create_funnel( $request ) {

		$resp        = array(
			'status' => false,
			'msg'    => __( 'Funnel creation failed', 'funnel-builder' )
		);
		$posted_data = array();

		$posted_data['funnel_id']     = isset( $request['funnel_id'] ) ? $request['funnel_id'] : 0;
		$posted_data['funnel_name']   = ( isset( $request['title'] ) && ! empty( $request['title'] ) ) ? $request['title'] : '';
		$posted_data['builder']       = isset( $request['template_group'] ) ? $request['template_group'] : '';
		$posted_data['template_type'] = isset( $request['template_type'] ) ? $request['template_type'] : '';
		$posted_data['template']      = isset( $request['template'] ) ? $request['template'] : '';

		if ( $posted_data['builder'] === '' || $posted_data['template'] === '' ) {
			return $resp;

		}

		if ( 'scratch' === $posted_data['template'] ) {
			$funnel_name = ! empty( $posted_data['funnel_name'] ) ? $posted_data['funnel_name'] : __( '(no title)', 'funnel-builder' );


			$funnel = WFFN_Core()->admin->get_funnel( $posted_data['funnel_id'] );
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

					$redirect_link = WFFN_Common::get_funnel_edit_link( $funnel_id );

					$resp['status']        = true;
					$resp['funnel']        = $funnel;
					$resp['redirect_link'] = $redirect_link;

				} else {
					$resp['error'] = __( 'Sorry, we are unable to create funnel due to some technical difficulties. Please contact support', 'funnel-builder' );
				}
			}
		} else {
			return $this->import_design( $posted_data );
		}

		return $resp;

	}

	public function import_design( $posted_data ) {

		$resp = array(
			'success' => false,
			'msg'     => __( 'Funnel creation failed', 'funnel-builder' )
		);

		$funnel_data = false;

		$template    = isset( $posted_data['template'] ) ? $posted_data['template'] : '';
		$builder     = isset( $posted_data['builder'] ) ? $posted_data['builder'] : '';
		$funnel_name = isset( $posted_data['funnel_name'] ) ? $posted_data['funnel_name'] : '';
		$funnel_id   = isset( $posted_data['funnel_id'] ) ? $posted_data['funnel_id'] : '';

		if ( ! empty( $template ) && ! empty( $builder ) ) {
			$funnel_data = WFFN_Core()->remote_importer->get_remote_template( 'funnel', $template, $builder );
		}

		/**
		 * CHeck if we successfully parsed the step data
		 */
		if ( ! is_array( $funnel_data ) || ( is_array( $funnel_data ) && isset( $funnel_data['error'] ) ) ) {
			$resp['msg'] = isset( $funnel_data['error'] ) ? $funnel_data['error'] : $funnel_data;

			return $resp;
		}

		$funnel = WFFN_Core()->admin->get_funnel( (int) $funnel_id );
		if ( $funnel instanceof WFFN_Funnel ) {

			if ( $funnel->get_id() === 0 ) {
				$funnel_name = empty( $funnel_name ) ? WFFN_Core()->admin->get_template_nice_name_by( $template, $builder ) : $funnel_name;
				$funnel_id   = $funnel->add_funnel( array(
					'title'  => $funnel_name,
					'desc'   => '',
					'status' => 1,
				) );
			}

			if ( defined( 'ICL_LANGUAGE_CODE' ) && 'all' !== ICL_LANGUAGE_CODE ) {
				WFFN_Core()->get_dB()->update_meta( $funnel_id, '_lang', ICL_LANGUAGE_CODE );
			}

			/**
			 * Lets do the data import which will first create the steps and their respective entities
			 */
			$funnel_data[0]['id'] = $funnel_id;

			WFFN_Core()->import->import_from_json_data( $funnel_data );


            update_option( '_wffn_scheduled_funnel_id', $funnel_id );
            WooFunnels_Dashboard::$classes['BWF_Logger']->log( sprintf( 'Background template importer for funnel id %d is started', $funnel_id ), 'wffn_template_import' );
            WFFN_Core()->admin->wffn_maybe_run_templates_importer();


			/**
			 * return success
			 */

			$status = $this->get_import_status( $funnel_id );

			if ( true === $status['success'] ) {
				$resp['success']   = true;
				$resp['funnel_id'] = $funnel_id;
				$resp['msg']       = __( 'Success', 'funnel-builder' );
			}

			$resp = array_merge( $resp, $status );


		}

		/**
		 * return success
		 */
		return $resp;
	}

	public function funnel_import_status_permissions_check( $request ) {

		if ( 'GET' !== $request->get_method() ) {
			return false;
		}

		return true;
	}

	public function funnel_import_status( $request ) {

		$resp = array(
			'success' => false,
		);

		$funnel_id = $request->get_param( 'funnel_id' );
		$funnel_id = ! empty( $funnel_id ) ? $funnel_id : 0;

		if ( $funnel_id === 0 ) {
			return $resp;
		}


        $funnel_id_db = get_option( '_wffn_scheduled_funnel_id', 0 );
        if ( $funnel_id_db > 0 ) {
            WooFunnels_Dashboard::$classes['BWF_Logger']->log( sprintf( 'Background template importer for funnel id %d is started in get_import_status', $funnel_id ), 'wffn_template_import' );
            WFFN_Core()->admin->wffn_updater->trigger();
            $resp['success'] = false;
        } else {
            $redirect_url = WFFN_Common::get_funnel_edit_link( $funnel_id );

            $resp['success']  = true;
            $resp['redirect'] = $redirect_url;

        }


		return $resp;
	}
	public function get_create_funnels_collection() {
		$params                   = array();
		$params['template_group'] = array(
			'description'       => __( 'Choose template group.', 'funnel-builder' ),
			'type'              => 'string',
			'required'          => true,
			'default'           => 'custom',
			'enum'              => array( 'elementor', 'custom' ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',

		);
		$params['template_type']  = array(
			'description'       => __( 'Choose template type.', 'funnel-builder' ),
			'type'              => 'string',
			'required'          => true,
			'default'           => 'all',
			'enum'              => array( 'all', 'sales', 'optin' ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',

		);
		$params['template']       = array(
			'description' => __( 'Choose template.', 'funnel-builder' ),
			'type'        => 'string',
			'required'    => true,

		);
		$params['title']          = array(
			'description' => __( 'Step name.', 'funnel-builder' ),
			'type'        => 'string',

		);
		$params['funnel_id']      = array(
			'description' => __( 'Funnel id.', 'funnel-builder' ),
			'type'        => 'integer',
			'default'     => 0,
		);

		return apply_filters( 'wffn_rest_create_funnels_collection', $params );
	}

	public function sanitize_custom( $data ) {

		return json_decode( $data, true );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_funnel( WP_REST_Request $request ) {
		$funnel_id = $request->get_param( 'funnel_id' );

		$funnel = new WFFN_Funnel( $funnel_id );

		if ( $funnel->get_id() === 0 ) {
			return new WP_Error( 'woofunnels_rest_funnel_not_exists', __( 'Invalid funnel ID.', 'funnel-builder' ), array( 'status' => 404 ) );
		}
		BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel_id );

		$return = array(
			'id'          => $funnel->get_id(),
			'title'       => $funnel->get_title(),
			'description' => $funnel->get_desc(),
			'link'        => $funnel->get_view_link(),
			'steps'       => $funnel->get_steps( true )
		);

		return rest_ensure_response( $return );
	}

	/**
	 * @param WP_REST_Request $request
	 */
	public function update_funnel( WP_REST_Request $request ) {

		$funnel_id = $request->get_param( 'funnel_id' );

		$funnel = new WFFN_Funnel( $funnel_id );

		if ( $funnel->get_id() === 0 ) {
			return new WP_Error( 'woofunnels_rest_funnel_not_exists', __( 'Invalid funnel ID.', 'funnel-builder' ), array( 'status' => 404 ) );
		}

		$title = $request->get_param( 'title' );
		if ( $title ) {
			$funnel->set_title( $title );
		}

        $description = $request->get_param( 'description' );
        if ( ! empty( $description ) ) {
            $funnel->set_desc( $description );
        } else {
            $funnel->set_desc( '' );
        }
		BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel_id );

		/**
		 * Handle steps reordering
		 */
		$steps = $request->get_param( 'steps' );
		if ( $steps ) {
			$funnel->reposition_steps( $steps );
		}
		$funnel->save();
		$return = array(
			'id'          => $funnel->get_id(),
			'title'       => $funnel->get_title(),
			'description' => $funnel->get_desc(),
			'link'        => $funnel->get_view_link(),
			'steps'       => $funnel->get_steps( true )
		);

		return rest_ensure_response( $return );
	}


}

if ( ! function_exists( 'wffn_rest_funnels' ) ) {

	function wffn_rest_funnels() {  //@codingStandardsIgnoreLine
		return WFFN_REST_Funnels::get_instance();
	}
}

wffn_rest_funnels();