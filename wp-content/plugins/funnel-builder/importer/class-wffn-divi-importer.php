<?php

/**
 * Divi Importer
 *
 * @since 1.0.0
 */
class WFFN_Divi_Importer implements WFFN_Import_Export {

	public function __construct() {
		include_once __DIR__ . '/class-wffn-image-importer.php';
		add_action( 'wp_ajax_parse_divi_contents', array( $this, 'ajax_parse_divi_contents' ) );
		add_action( 'wp_ajax_nopriv_parse_divi_contents', array( $this, 'ajax_parse_divi_contents' ) );
	}

	public function import( $module_id, $export_content = '' ) {
			$status = $this->import_template_single( $module_id, $export_content );
			return $status;
	}

	public function import_template_single( $post_id, $content ) {

		wp_update_post( [ 'ID' => $post_id, 'post_content' => '' ] );
		delete_post_meta( $post_id, '_et_pb_use_builder' );

		if ( ! is_array( $content ) && is_string( $content ) ) {
			try {
				$content = json_decode( $content, true );
			} catch ( Exception $error ) {
				return false;
			}
		}

		$content = stripslashes( current( $content['data'] ) );

		update_post_meta( $post_id, 'divi_content', $content );
		$content = $this->get_content( $content );

		if ( false === strpos( $content, '[' ) ) {
			return false;
		}

		wp_update_post( [ 'ID' => $post_id, 'post_content' => $content ] );
		update_post_meta( $post_id, '_et_pb_use_builder', 'on' );

		return true;
	}

	function get_content( $content = '' ) {

		$all_links   = wp_extract_urls( $content );
		$image_links = array();
		$image_map   = array();

		// Not have any link.
		if ( empty( $all_links ) ) {
			return $content;
		}

		foreach ( $all_links as $link ) {
			if ( preg_match( '/\.(jpg|jpeg|png|gif)/i', $link ) ) {
				$image_links[] = $link;
			}
		}

		// Not have any image link.
		if ( empty( $image_links ) ) {
			return $content;
		}

		foreach ( $image_links as  $image_url ) {

			// Download remote image.
			$image            = array(
				'url' => $image_url,
				'id'  => wp_rand( 000, 999 ),
			);
			$downloaded_image = WFFN_Image_Importer::get_instance()->import( $image );

			// Old and New image mapping links.
			$image_map[ $image_url ] = $downloaded_image['url'];
		}

		// Replace old image links with new image links.
		foreach ( $image_map as $old_url => $new_url ) {
			$content = str_replace( $old_url, $new_url, $content );
		}

		return $content;

	}

	public function export( $module_id, $slug ) {
		$post = get_post( $module_id );

		return $post->post_content;
	}
}

if ( class_exists( 'WFFN_Template_Importer' ) ) {
	WFFN_Template_Importer::register( 'divi', new WFFN_Divi_Importer() );
}