<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_Exporter
 * Handles All the methods about page builder activities
 */
class WFFN_Exporter {

	private static $ins = null;
	private $funnel = null;
	private $installed_plugins = null;

	public function __construct() {
		add_action( 'admin_init', [ $this, 'maybe_export' ] );
		add_action( 'admin_init', [ $this, 'maybe_export_single' ] );
	}

	/**
	 * @return WFFN_Admin|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function maybe_export() {

		if ( 'export' !== filter_input( INPUT_POST, 'wffn-action', FILTER_SANITIZE_STRING ) ) {
			return;
		}
		check_admin_referer( 'wffn-action-nonce', 'wffn-action-nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$funnels           = WFFN_Core()->admin->get_funnels();
		$funnels_to_export = [];
		foreach ( $funnels['items'] as $key => $funnel ) {
			$funnels_to_export[ $key ] = [];
			/**
			 * var WFFN_Funnel $get_funnel
			 */
			$get_funnel                         = $funnel['__funnel'];
			$funnels_to_export[ $key ]['title'] = $get_funnel->get_title();
			$funnels_to_export[ $key ]['steps'] = [];

			$steps = $get_funnel->get_steps( true );
			foreach ( $steps as $k => $step ) {
				$get_object                               = WFFN_Core()->steps->get_integration_object( $step['type'] );
				$step_export_data                         = $get_object->get_export_data( $step );
				$funnels_to_export[ $key ]['steps'][ $k ] = $step_export_data;
			}
		}
		$funnels_to_export = apply_filters( 'wffn_export_data', $funnels_to_export );
		nocache_headers();

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=wffn-funnels-export-' . gmdate( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo wp_json_encode( $funnels_to_export );
		exit;
	}

	public function maybe_export_single() {
		if ( 'wffn-export' !== filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING ) ) {
			return;
		}

		check_admin_referer( 'wffn-export', '_wpnonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$funnels_to_export = [];

		$funnels_to_export[0] = [];

		if ( ! isset( $_GET['id'] ) ) {
			return;
		}
		$get_funnel = new WFFN_Funnel( wffn_clean( $_GET['id'] ) );
		/**
		 * var WFFN_Funnel $get_funnel
		 */

		$funnels_to_export[0]['title'] = $get_funnel->get_title();
		$funnels_to_export[0]['steps'] = [];

		$steps = $get_funnel->get_steps( true );
		foreach ( $steps as $k => $step ) {
			$get_object                          = WFFN_Core()->steps->get_integration_object( $step['type'] );
			$step_export_data                    = $get_object->get_export_data( $step );
			$funnels_to_export[0]['steps'][ $k ] = $step_export_data;
		}
		$funnels_to_export = apply_filters( 'wfocu_export_data', $funnels_to_export );

		nocache_headers();

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=wffn-funnel-export-' . gmdate( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo wp_json_encode( $funnels_to_export );
		exit;
	}


}


if ( class_exists( 'WFFN_Core' ) ) {
	WFFN_Core::register( 'export', 'WFFN_Exporter' );
}
