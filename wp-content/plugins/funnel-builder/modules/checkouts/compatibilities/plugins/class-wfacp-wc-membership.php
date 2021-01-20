<?php

/**
 * Class WFACP_WC_MemberShip   WooCommerce Memberships By SkyVerge
 * Page Redirect in ajax when coupon apply
 *
 *
 */
class WFACP_Compatibility_WC_MemberShip {
	public function __construct() {
		add_filter( 'pre_option_wc_memberships_redirect_page_id', [ $this, 'send_null_page_id' ] );
		add_filter( 'wp_redirect_status', [ $this, 'remove_redirect_action' ] );
	}

	public function remove_redirect_action( $status ) {
		$wc_ajax = filter_input( INPUT_GET, 'wc-ajax', FILTER_SANITIZE_STRING );
		if ( class_exists( 'WC_Memberships_Loader' ) && ! is_null( $wc_ajax ) && false !== strpos( $wc_ajax, 'wfacp' ) ) {
			$status = false;
		}

		return $status;
	}

	public function send_null_page_id( $status ) {
		$wc_ajax = filter_input( INPUT_GET, 'wc-ajax', FILTER_SANITIZE_STRING );
		if ( class_exists( 'WC_Memberships_Loader' ) && ! is_null( $wc_ajax ) && false !== strpos( $wc_ajax, 'wfacp' ) ) {
			$status = null;
		}

		return $status;
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_WC_MemberShip(), 'wc_membership' );
