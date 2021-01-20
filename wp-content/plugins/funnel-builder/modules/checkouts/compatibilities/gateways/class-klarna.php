<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFACP_Klarna {
	public function __construct() {
		add_action( 'wfacp_internal_css', [ $this, 'disable_pop_state' ] );
		add_action( 'woocommerce_checkout_order_processed', [ $this, 'attach_hooks' ] );
		add_filter( 'wfacp_unset_shipping_first_last_name_when_ship_to_different_address_not_checked', [ $this, 'do_not_unset_first_and_last_name' ] );
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'remove_kco_event' ], 100 );
	}

	public function attach_hooks() {
		add_filter( 'woocommerce_get_checkout_url', [ $this, 'change_checkout_url' ] );
	}

	public function remove_kco_event() {
		//klarna payments
		if ( isset( $_REQUEST['wfacp_id'] ) && $_REQUEST['wfacp_id'] > 0 ) {
			WFACP_Common::remove_actions( 'woocommerce_checkout_cart_item_quantity', 'KCO', 'add_quantity_field' );
			WFACP_Common::remove_actions( 'woocommerce_checkout_cart_item_quantity', 'CSI_Frontend', 'checkout_cart_item_name' );
		}
	}

	public function change_checkout_url( $url ) {
		$payment_method = filter_input( INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING );

		if ( in_array( $payment_method, [ 'klarna_payments', 'klarna_payments_pay_later', 'klarna_payments_pay_over_time' ] ) ) {
			if ( isset( $_REQUEST['_wfacp_post_id'] ) && $_REQUEST['_wfacp_post_id'] > 0 ) {
				$is_global = filter_input( INPUT_GET, 'wfacp_is_checkout_override', FILTER_SANITIZE_STRING );
				$aero_id   = filter_input( INPUT_POST, '_wfacp_post_id', FILTER_SANITIZE_STRING );
				$global_id = WFACP_Common::get_checkout_page_id();
				if ( 'yes' == $is_global ) {
					$global_post = get_post( $global_id );
					if ( ! is_null( $global_post ) && $global_post->post_type !== WFACP_Common::get_post_type_slug() ) {
						$url = get_the_permalink( $global_id );
					}
				} else {
					if ( isset( $_REQUEST['wfacp_embed_form_page_id'] ) ) {
						$t_id = filter_input( INPUT_POST, 'wfacp_embed_form_page_id', FILTER_SANITIZE_STRING );
						$url  = get_the_permalink( $t_id );
					} else {
						$url = get_the_permalink( $aero_id );
					}
				}
			}
		}

		return $url;
	}

	public function disable_pop_state() {
		?>
        <script>
            window.addEventListener('load', function () {
                wfacp_frontend.hooks.addFilter('disable_pop_state', function (status, newHash) {
                    if ('' !== newHash && newHash.indexOf('#kp') > -1) {
                        status = true;
                    }
                    return status;
                });
            })
        </script>
		<?php
	}

	public function do_not_unset_first_and_last_name( $status ) {
		if ( isset( $_REQUEST['payment_method'] ) && 'klarna_payments' == $_REQUEST['payment_method'] ) {
			$status = false;
		}

		return $status;
	}
}

new WFACP_Klarna();
