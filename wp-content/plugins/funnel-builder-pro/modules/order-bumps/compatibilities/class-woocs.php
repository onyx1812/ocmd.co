<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WFOB_Compatibility_With_WOOCS {

	public function __construct() {
		add_filter( 'wfob_product_switcher_price_data', [ $this, 'wfob_product_switcher_price_data' ], 10, 2 );
	}

	public function is_enable() {
		if ( isset( $GLOBALS['WOOCS'] ) && $GLOBALS['WOOCS'] instanceof WOOCS ) {

			return true;
		}

		return false;
	}



	/**
	 * @param $price_data
	 * @param $pro WC_Product;
	 *
	 * @return mixed
	 */
	public function wfob_product_switcher_price_data( $price_data, $pro ) {
		if ( ! $this->is_enable() ) {
			return $price_data;
		}
		$price_data['regular_org'] = $pro->get_regular_price();
		$price_data['price']       = $pro->get_price();

		return $price_data;
	}


}

WFOB_Plugin_Compatibilities::register( new WFOB_Compatibility_With_WOOCS(), 'woocs' );




