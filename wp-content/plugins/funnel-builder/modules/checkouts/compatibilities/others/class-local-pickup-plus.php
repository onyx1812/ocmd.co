<?php

/**
 *
 * Comaptibility with local pickup plus by sky verge
 * location dropdown not creating
 *
 * Class WFACP_Local_Pickup_Plus_SkyVerge
 */
class WFACP_Local_Pickup_Plus_SkyVerge {
	public function __construct() {
		add_filter( 'wfacp_after_discount_added_to_item', [ $this, 'update_item' ] );
		add_filter( 'woocommerce_is_checkout', [ $this, 'make_checkout' ] );
		add_action( 'wfacp_internal_css', [ $this, 'restrict_our_fragments' ] );
		add_filter( 'wc_get_template', [ $this, 'remove_review_order_summary' ], 10, 2 );
	}

	public function update_item( $item ) {
		if ( $this->is_enabled() ) {
			$item['cart_item_key'] = $item['key'];
		}

		return $item;
	}

	private function is_enabled() {
		if ( class_exists( 'WC_Shipping_Local_Pickup_Plus' ) ) {
			return true;
		}

		return false;
	}

	public function make_checkout( $status ) {
		if ( $this->is_enabled() ) {
			$wc_ajax = filter_input( INPUT_GET, 'wc-ajax', FILTER_SANITIZE_STRING );
			if ( wp_doing_ajax() && ! is_null( $wc_ajax ) && false !== strpos( $wc_ajax, 'wfacp_' ) ) {
				$status = true;
			}
		}

		return $status;
	}

	public function remove_review_order_summary( $template, $template_name ) {
		if ( $this->is_enabled() ) {
			$wc_ajax = filter_input( INPUT_GET, 'wc-ajax', FILTER_SANITIZE_STRING );
			$wfacp_is_checkout_override = filter_input( INPUT_GET, 'wfacp_is_checkout_override', FILTER_SANITIZE_STRING );
			if ( wp_doing_ajax() && ( ! is_null( $wc_ajax ) && false !== strpos( $wc_ajax, 'wfacp_' ) || !is_null($wfacp_is_checkout_override) ) ) {
				if ( $template_name == 'checkout/review-order.php' ) {
					$template = null;
				}
			}
		}

		return $template;
	}


	public function restrict_our_fragments() {
		if ( ! $this->is_enabled() ) {
			return;
		}
		?>
        <script>
            window.addEventListener('load', function () {
                (function ($) {
                    wfacp_frontend.hooks.addFilter('wfacp_stop_updating_fragments', function (rsp, send_data) {
                        if (send_data.hasOwnProperty('message') && send_data.message.hasOwnProperty('error')) {
                            return rsp;
                        }
                        return true;
                    });

                    wfacp_frontend.hooks.addAction('wfacp_stop_updating_fragments', function (rsp, send_data) {
                        jQuery('body').trigger('update_checkout');
                    });
                    $('body').on('updated_checkout', function () {
                        var row = $('.wfacp_coupon_row');
                        if (row.length > 0) {
                            row.unblock();
                            row.parents('form').removeClass('processing');
                        }
                    });
                })(jQuery);
            });
        </script>
		<?php
	}
}

new WFACP_Local_Pickup_Plus_SkyVerge();