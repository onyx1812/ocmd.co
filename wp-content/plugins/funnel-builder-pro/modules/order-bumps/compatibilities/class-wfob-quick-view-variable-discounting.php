<?php

class WFOB_Apply_Discount_Quick_View {
	private $item_key = '';
	private $item_data = [];
	private $wfob_id = '';

	public function __construct() {
		add_action( 'wfob_qv_images', [ $this, 'prepare_data' ] );
		add_filter( 'woocommerce_product_variation_get_price', array( $this, 'wcct_trigger_get_price' ), 999, 2 );
		add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'wcct_trigger_get_price' ), 999, 2 );
		add_action( 'wp_head', [ $this, 'css' ] );
	}

	public function css() {
		?>
        <style>
            .wfob_qv-main .single_variation_wrap .woocommerce-variation-price {
                display: block;
            }

            #wfob_qr_model_wrap .wfob_qr_wrap .wfob_qv-summary p.price {
                display: none;
            }
        </style>
		<?php

	}

	public function prepare_data() {
		if ( isset( $_REQUEST['wfob_id'] ) ) {
			$this->wfob_id  = absint( $_REQUEST['wfob_id'] );
			$this->item_key = $_REQUEST['item_key'];
			$bump_products  = WFOB_Common::get_bump_products( $this->wfob_id );

			if ( isset( $bump_products[ $this->item_key ] ) ) {
				$this->item_data = $bump_products[ $this->item_key ];
			}

		}

	}

	public function wcct_trigger_get_price( $get_price, $product_global ) {
		if ( ! $product_global instanceof WC_Product ) {
			return $get_price;
		}
		if ( empty( $this->item_data ) ) {
			return $get_price;
		}
		$id = $product_global->get_parent_id();
		if ( isset( $this->item_data['variable'] ) && 'yes' == $this->item_data['variable'] && $this->item_data['id'] == $id ) {
			$new_price = $this->get_price( $product_global, $this->item_data );
			if ( ! is_null( $new_price ) ) {
				$get_price = $new_price;
			}
		}


		return $get_price;

	}

	private function get_price( $pro, $data ) {
		if ( ! $pro instanceof WC_Product ) {
			return null;
		}
		$qty             = 1;
		$raw_data        = $pro->get_data();
		$discount_type   = trim( $data['discount_type'] );
		$raw_data        = apply_filters( 'wfob_product_raw_data', $raw_data, $pro );
		$regular_price   = apply_filters( 'wfob_discount_regular_price_data', $raw_data['regular_price'] );
		$price           = apply_filters( 'wfob_discount_price_data', $raw_data['price'] );
		$discount_amount = floatval( apply_filters( 'wfob_discount_amount_data', $data['discount_amount'], $discount_type ) );
		$discount_data   = [
			'wfob_product_rp'      => $regular_price * $qty,
			'wfob_product_p'       => $price * $qty,
			'wfob_discount_amount' => $discount_amount,
			'wfob_discount_type'   => $discount_type,
		];

		if ( 'fixed_discount_sale' == $discount_type || 'fixed_discount_reg' == $discount_type ) {
			$discount_data['wfob_discount_amount'] = $discount_amount * $qty;
		}

		$new_price = WFOB_Common::calculate_discount( $discount_data );
		if ( ! is_null( $new_price ) ) {
			return $new_price;
		}

		return null;
	}
}

new WFOB_Apply_Discount_Quick_View();