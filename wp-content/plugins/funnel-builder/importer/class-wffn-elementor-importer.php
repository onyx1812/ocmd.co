<?php

/**
 * Elementor template library local source.
 *
 * Elementor template library local source handler class is responsible for
 * handling local Elementor templates saved by the user locally on his site.
 *
 * @since 1.0.0
 */
class WFFN_Elementor_Importer extends Elementor\TemplateLibrary\Source_Local implements WFFN_Import_Export {
	public function __construct() {
		add_action( 'woofunnels_module_template_removed', [ $this, 'delete_elementor_data' ] );
	}

	public function import( $module_id, $export_content = '' ) {
		//phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$status = $this->import_template_single( $module_id, $export_content );

		return $status;
	}

	/**
	 *  Import single template
	 *
	 * @param int $post_id post ID.
	 */
	public function import_template_single( $post_id, $content ) {
		wp_update_post( [
			'ID'           => $post_id,
			'post_content' => '',
		] );
		delete_post_meta( $post_id, '_et_pb_use_builder' );
		if ( empty( $content ) ) {
			$this->clear_cache();

			return true;
		}

		if ( ! is_array( $content ) && is_string( $content ) ) {
			try {
				$content = json_decode( $content, true );
			} catch ( Exception $error ) {
				return false;
			}
		}

		if ( isset( $content['content'] ) ) {
			$content = $content['content'];
		}
		//go ahead and import the content
		$content = $this->process_export_import_content( $content, 'on_import' );
		if ( empty( $content ) ) {

			return false;
		}
		// Update content.
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_data', $content );

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
		}

		$this->clear_cache();

		return true;
	}

	public function clear_cache() {
		Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	public function export( $module_id, $slug ) {
		$data = get_post_meta( $module_id, '_elementor_data', true );

		return $data;
	}

	public function delete_elementor_data( $post_id ) {
		delete_post_meta( $post_id, '_elementor_version' );
		delete_post_meta( $post_id, '_elementor_template_type' );
		delete_post_meta( $post_id, '_elementor_edit_mode' );
		delete_post_meta( $post_id, '_elementor_data' );
		delete_post_meta( $post_id, '_elementor_controls_usage' );
		delete_post_meta( $post_id, '_elementor_css' );
	}
}

if ( class_exists( 'WFFN_Template_Importer' ) && defined( 'ELEMENTOR_VERSION' ) ) {
	WFFN_Template_Importer::register( 'elementor', new WFFN_Elementor_Importer() );
}
