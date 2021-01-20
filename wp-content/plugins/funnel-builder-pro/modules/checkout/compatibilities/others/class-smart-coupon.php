<?php

/**
 * By Store Apps
 * Class WFACP_Compatibility_With_Smart_Coupons
 */
class WFACP_Compatibility_With_Smart_Coupons {
	private $process = false;

	public function __construct() {
		add_action( 'wfacp_internal_css', [ $this, 'js' ] );
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'get_data' ], 5 );
		add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'unset_fragments' ], 900 );
	}

	private function is_enabled() {
		return class_exists( 'WC_Smart_Coupons' );
	}

	public function get_data( $data ) {
		if ( false == $this->is_enabled() ) {
			return $data;
		}
		if ( empty( $data ) ) {
			return $data;
		}
		parse_str( $data, $post_data );
		if ( empty( $post_data ) || ! isset( $post_data['wfacp_input_hidden_data'] ) || empty( $post_data['wfacp_input_hidden_data'] ) ) {
			return $data;
		}

		$bump_action_data = json_decode( $post_data['wfacp_input_hidden_data'], true );

		if ( empty( $bump_action_data ) ) {
			return $data;
		}
		if ( isset( $bump_action_data['unset_fragments'] ) ) {
			$this->process = true;
		}
	}

	public function unset_fragments( $fragments ) {
		if ( false == $this->process ) {
			return $fragments;
		}
		foreach ( $fragments as $k => $fragment ) {
			if ( false !== strpos( $k, 'wfacp' ) && true == apply_filters( 'wfacp_unset_our_fragments_by_smart_coupon', true, $k ) ) {
				unset( $fragments[ $k ] );
			}
		}
		unset( $fragments['cart_total'] );

		return $fragments;
	}

	public function js() {
		if ( false == $this->is_enabled() ) {
			return;
		}
		?>
        <script>
            window.addEventListener('load', function () {
                (function ($) {
                    wfacp_frontend.hooks.addFilter('wfacp_before_ajax_data_apply_coupon_field', set_custom_data);
                    wfacp_frontend.hooks.addFilter('wfacp_before_ajax_data_apply_coupon_main', set_custom_data);
                    wfacp_frontend.hooks.addAction('wfacp_ajax_apply_coupon_field', trigger_checkout);
                    wfacp_frontend.hooks.addAction('wfacp_ajax_apply_coupon_main', trigger_checkout);

                    function set_custom_data(data) {
                        data['unset_fragments'] = 'yes';
                        return data;
                    }

                    function trigger_checkout(rsp) {
                        if (rsp.hasOwnProperty('message')) {
                            var message = rsp.message;
                            if (!message.hasOwnProperty('error')) {
                                $(document.body).trigger('update_checkout');
                            }
                        }
                    }
                })(jQuery);
            });
        </script>
		<?php
	}
}

WFACP_Plugin_Compatibilities::register( new WFACP_Compatibility_With_Smart_Coupons(), 'smart_coupon' );
