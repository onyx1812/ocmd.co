<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_Importer
 * Handles All the methods about page builder activities
 */
class WFFN_Importer {

	private static $ins = null;
	private $funnel = null;
	private $installed_plugins = null;
	public $is_imported = false;

	public function __construct() {
		add_action( 'admin_init', [ $this, 'maybe_import' ] );
	}

	/**
	 * @return WFFN_Importer|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	/**
	 * Import our exported file
	 *
	 * @since 1.1.4
	 */
	function maybe_import() {

		if ( 'import' !== filter_input( INPUT_POST, 'wffn-action', FILTER_SANITIZE_STRING ) ) {
			return;
		}
		check_admin_referer( 'wffn-action-nonce', 'wffn-action-nonce' );


		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_FILES['file']['name'] ) ) {
			return;
		}
		if ( ! isset( $_FILES['file']['tmp_name'] ) ) {
			return;
		}
		$filename  = wffn_clean( $_FILES['file']['name'] );
		$file_info = explode( '.', $filename );
		$extension = end( $file_info );

		if ( 'json' !== $extension ) {
			wp_die( esc_html__( 'Please upload a valid .json file', 'wffn' ) );
		}

		$file = wffn_clean( $_FILES['file']['tmp_name'] );

		if ( empty( $file ) ) {
			wp_die( esc_html__( 'Please upload a file to import', 'wffn' ) );
		}

		// Retrieve the settings from the file and convert the JSON object to an array.
		$funnels = json_decode( file_get_contents( $file ), true ); //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown

		$this->import_from_json_data( $funnels );

		$this->is_imported = true;
	}

	public function import_from_json_data( $funnels ) {
		$imported_funnels = [];
		if ( $funnels ) {

			foreach ( $funnels as $funnel ) {
				$funnel_id = isset( $funnel['id'] ) ? $funnel['id'] : 0;
				if ( $funnel_id < 1 ) {
					$funnel_title = $funnel['title'] . ' Copy';
					// Create post object.
					$new_funnel_args = apply_filters( 'wffn_funnel_importer_args', array(
						'title'  => $funnel_title,
						'status' => 1,
					) );

					// Insert the post into the database.
					$funnel_obj = new WFFN_Funnel( $funnel_id );
					if ( $funnel_obj instanceof WFFN_Funnel ) {
						$funnel_id = $funnel_obj->add_funnel( $new_funnel_args );
					}
					do_action( 'wffn_funnel_imported', $funnel_id, $new_funnel_args, $funnels );
				}

				if ( $funnel['steps'] && $funnel_id > 0 ) {
					foreach ( $funnel['steps'] as $step ) {
						$get_type_object = WFFN_Core()->steps->get_integration_object( $step['type'] );
						if ( $get_type_object instanceof WFFN_Step ) {
							$get_type_object->_process_import( $funnel_id, $step );
						}
					}
				}

				array_push( $imported_funnels, $funnel_id );
				$funnel_id = 0;
			}
		}

		return $imported_funnels;
	}
}

if ( class_exists( 'WFFN_Core' ) ) {
	WFFN_Core::register( 'import', 'WFFN_Importer' );
}
