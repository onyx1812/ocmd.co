<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_AJAX_Controller
 * Handles All the request
 */
class WFFN_AJAX_Controller {

	public static function init() {

		/**
		 * Backend AJAX actions
		 */
		if ( is_admin() ) {
			self::handle_admin_ajax();
		}
	}

	/**
	 * Handling admin ajax
	 */
	public static function handle_admin_ajax() {
		add_action( 'wp_ajax_wffn_add_funnel', array( __CLASS__, 'add_funnel' ) );
		add_action( 'wp_ajax_wffn_delete_funnel', array( __CLASS__, 'delete_funnel' ) );
		add_action( 'wp_ajax_wffn_delete_substep', array( __CLASS__, 'delete_substep' ) );
		add_action( 'wp_ajax_wffn_activate_plugin', array( __CLASS__, 'activate_plugin' ) );
		add_action( 'wp_ajax_wffn_import_design', array( __CLASS__, 'import_design' ) );
		add_action( 'wp_ajax_wffn_get_import_status', array( __CLASS__, 'get_import_status' ) );
		add_action( 'wp_ajax_wffn_duplicate_funnel', array( __CLASS__, 'duplicate_funnel' ) );
	}

	/**
	 * Adding a new funnel
	 */
	public static function add_funnel() {
		check_admin_referer( 'wffn_add_funnel', '_nonce' );

		$resp = array(
			'status'    => false,
			'funnel_id' => 0,
		);

		$posted_content = isset( $_POST ) ? wffn_clean( $_POST ) : [];
		$posted_data    = isset( $posted_content['_data'] ) ? $posted_content['_data'] : [];

		$funnel = WFFN_Core()->admin->get_funnel( 0 );
		if ( $funnel instanceof WFFN_Funnel ) {
			$funnel_id = $funnel->add_funnel( $posted_data );
			if ( $funnel_id > 0 ) {
				$resp['funnel_id']    = $funnel_id;
				$resp['status']       = true;
				$resp['redirect_url'] = WFFN_Common::get_funnel_edit_link( $funnel_id );
			}
		}
		wp_send_json( $resp );
	}



	/**
	 * Deleting a funnel
	 */
	public static function delete_funnel() {
		check_admin_referer( 'wffn_delete_funnel', '_nonce' );

		$posted_content = isset( $_POST ) ? wffn_clean( $_POST ) : [];
		$funnel_id      = isset( $posted_content['funnel_id'] ) ? $posted_content['funnel_id'] : 0;
		$deleted        = false;

		if ( $funnel_id > 0 ) {
			$funnel  = WFFN_Core()->admin->get_funnel( $funnel_id );
			$deleted = $funnel->delete();
		}

		wp_send_json( array(
			'status'  => true,
			'deleted' => $deleted,
		) );
	}



	/**
	 * Adding a duplicate funnel
	 */
	public static function duplicate_funnel() {
		check_admin_referer( 'ajax_nonce_duplicate_funnel', '_nonce' );

		$resp      = array(
			'status'    => false,
			'funnel_id' => 0,
		);
		$funnel_id = isset( $_POST['funnel_id'] ) ? wffn_clean( $_POST['funnel_id'] ) : 0;

		if ( $funnel_id > 0 ) {
			$new_funnel = WFFN_Core()->admin->get_funnel();
			$funnel     = WFFN_Core()->admin->get_funnel( $funnel_id );

			if ( $new_funnel instanceof WFFN_Funnel ) {
				$new_funnel_id = $new_funnel->add_funnel( array(
					'title'  => $funnel->title . ' - ' . __( 'Copy', 'woofunnels-aero-checkout' ),
					'desc'   => $funnel->desc,
					'status' => 1,
				) );

				if ( $new_funnel_id > 0 ) {

					if ( isset( $funnel->steps ) && is_array( $funnel->steps ) ) {
						foreach ( $funnel->steps as $steps ) {
							$type        = $steps['type'];
							$step_id     = $steps['id'];
							$posted_data = array( 'duplicate_funnel_id' => $funnel_id );

							BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $new_funnel_id );
							if ( ! empty( $type ) ) {
								$get_step = WFFN_Core()->steps->get_integration_object( $type );
								if ( $get_step instanceof WFFN_Step ) {
									$get_step->duplicate_step( $new_funnel_id, $step_id, $posted_data );
								}
							}
						}
					}

					$resp['funnel_id'] = $new_funnel_id;
					$resp['status']    = true;
				}
			}
		}

		wp_send_json( $resp );
	}



	/**
	 * Ajax action to activate plugin
	 */
	public static function activate_plugin() {
		check_ajax_referer( 'wffn_activate_plugin', '_nonce' );

		$plugin_init = isset( $_POST['plugin_init'] ) ? sanitize_text_field( $_POST['plugin_init'] ) : '';
		$activate    = activate_plugin( $plugin_init, '', false, true );

		if ( is_wp_error( $activate ) ) {
			wp_send_json_error( array(
				'success' => false,
				'message' => $activate->get_error_message(),
				'init'    => $plugin_init,
			) );
		}

		wp_send_json_success( array(
			'success' => true,
			'message' => __( 'Plugin Successfully Activated', 'funnel-builder' ),
			'init'    => $plugin_init,
		) );
	}

	public static function import_design() {
		check_ajax_referer( 'wffn_import_design', '_nonce' );

		$funnel_data = false;

		$posted_data = isset( $_POST ) ? wffn_clean( $_POST ) : [];

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
			wp_send_json_error( array(
				'success' => false,
				'msg'     => isset( $funnel_data['error'] ) ? $funnel_data['error'] : $funnel_data,
				'message' => __( 'Unable to import 2', 'funnel-builder' ),
			) );
		}

		$funnel = WFFN_Core()->admin->get_funnel( (int) $funnel_id );
		if ( $funnel instanceof WFFN_Funnel ) {

			if ( $funnel->get_id() === 0 ) {
				$funnel_name = __( '(no title)', 'funnel-builder' );
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
			wp_send_json_success( array(
				'success'   => true,
				'progress'  => sprintf( __( 'Importing 1 of %d', 'funnel-builder' ), count( $funnel_data[0]['steps'] ) ),
				'funnel_id' => $funnel_id,
				'msg'       => __( 'Success', 'funnel-builder' )
			) );

		}

		/**
		 * return success
		 */
		wp_send_json_error( array(
			'success' => false,
			'msg'     => __( 'Funnel creation failed', 'funnel-builder' )
		) );
	}

	/**
	 * Constantly check if we have reached the success for the import design operation or we have the action still scheduled
	 */
	public static function get_import_status() {
		check_ajax_referer( 'wffn_get_import_status', '_nonce' );

		$funnel_id = filter_input( INPUT_POST, 'wffn_id', FILTER_SANITIZE_NUMBER_INT );
        $funnel_id = absint( $funnel_id );

			$funnel_id_db = get_option( '_wffn_scheduled_funnel_id', 0 );
			if ( $funnel_id_db > 0 ) {
				WooFunnels_Dashboard::$classes['BWF_Logger']->log( sprintf( 'Background template importer for funnel id %d is started in get_import_status', $funnel_id ), 'wffn_template_import' );
				WFFN_Core()->admin->wffn_updater->trigger();
				wp_send_json_error( array(
					'success' => false,
				) );
			} else {
				$redirect_url = WFFN_Common::get_funnel_edit_link( $funnel_id );

				wp_send_json_success( array(
					'success'  => true,
					'redirect' => $redirect_url,
				) );
			}

	}
}

WFFN_AJAX_Controller::init();
