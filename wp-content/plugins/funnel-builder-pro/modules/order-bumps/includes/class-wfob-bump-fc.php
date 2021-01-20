<?php
/**
 * Created by PhpStorm.
 * User: sandeep
 * Date: 18/9/18
 * Time: 12:56 PM
 */

final class WFOB_Bump {

	private $wfob_id = 0;
	private $products = [];
	private $design_data = [];
	private $settings = [];

	public function __construct( $wfob_id ) {

		if ( $wfob_id > 0 ) {
			$this->wfob_id = $wfob_id;
			$this->setup_data();
			add_action( 'wp_footer', [ $this, 'print_css_at_footer' ] );
		}
	}

	/**
	 * Setup product and design data
	 */
	private function setup_data() {
		$post = get_post( $this->wfob_id );
		if ( is_null( $post ) ) {
			return;
		}

		$this->products = WFOB_Common::get_prepared_products( $this->wfob_id );
		$this->settings = WFOB_Common::get_setting_data( $this->wfob_id );
		$design_data    = WFOB_Common::get_design_data_meta( $this->wfob_id );

		if ( empty( $design_data ) ) {
			$design_data       = WFOB_Common::get_default_model_data();
			$this->design_data = $design_data[ WFOB_Common::$design_default_layout ];
		} else {
			$this->design_data = $design_data;
		}
	}

	/**
	 * get Bump Id
	 * @return int
	 */
	public function get_id() {
		return $this->wfob_id;
	}

	/**
	 * Return all design Data of bump products
	 * @return array
	 */
	public function get_design_data() {
		return $this->design_data;
	}

	/**
	 * Return Prepared bumps products
	 * @return array
	 */

	public function get_bump_product() {
		return $this->products;
	}


	/**
	 * Return Prepared bumps products
	 * @return array
	 */

	public function get_bump_settings() {
		return $this->settings;
	}


	/**
	 * Print all bump ui at checkout page
	 */
	public function print_bump() {
		if ( ! $this->have_bumps() ) {
			return '';
		}
		$max_bumps = WFOB_Bump_Fc::maximumn_bump_print();
		$print_css = false;
		//WFACP_Common::pr( $this->products );
		$design_data = $this->get_design_data();
		foreach ( $this->products as $product_key => $data ) {
			if ( '' !== $max_bumps && $max_bumps > 0 && count( WFOB_Bump_Fc::$number_of_bump_print ) >= $max_bumps ) {
				break;
			}
			if ( isset( $data['stock'] ) && true == $data['stock'] ) {
				$data['item_key'] = $product_key;
				$print_css        = true;

				if ( $design_data['layout'] == 'layout_3' || $design_data['layout'] == 'layout_4' ) {
					$status = include WFOB_SKIN_DIR . '/layout_3.php';
				} else {
					$status = include WFOB_SKIN_DIR . '/layout-1.php';
				}
				if ( 'success' == $status ) {
					WFOB_Bump_Fc::$number_of_bump_print[ $product_key ] = 1;
				}
			}
		}
		$public = WFOB_Public::get_instance();
		if ( true == $print_css && false == $public->show_on_load() ) {
			include WFOB_SKIN_DIR . '/style.php';
		}
	}

	/**
	 * Return bump product is exist or not
	 * @return bool
	 */
	public function have_bumps() {
		return ( ( is_array( $this->products ) ) ? count( $this->products ) > 0 : false );
	}


	public function get_position() {
		$display_hook       = $this->settings['order_bump_position_hooks'];
		$available_position = WFOB_Common::get_bump_position( true );
		if ( isset( $available_position[ $display_hook ] ) ) {
			$position = $available_position[ $display_hook ];

			return $position['id'];
		}

		return WFOB_Common::default_bump_position();
	}

	public function print_css_at_footer() {
		$public = WFOB_Public::get_instance();
		if ( $public->show_on_load() ) {
			include WFOB_SKIN_DIR . '/style.php';
		}
	}
}

/**
 * Bump Factory Class
 * Class WFOB_Bump_Fc
 */
abstract class WFOB_Bump_Fc {

	public static $number_of_bump_print = [];
	public static $maximumn_bump_print = '';
	private static $wfob_data = [];

	public static function create( $wfob_id ) {

		if ( $wfob_id > 0 ) {
			self::$wfob_data[ $wfob_id ] = new WFOB_Bump( $wfob_id );

			return self::$wfob_data[ $wfob_id ];
		}

		return null;
	}

	/**
	 *
	 */
	public static function maximumn_bump_print() {

		if ( '' != self::$maximumn_bump_print ) {
			return self::$maximumn_bump_print;
		}
		$data = WFOB_Common::get_global_setting();

		self::$maximumn_bump_print = isset( $data['number_bump_per_checkout'] ) ? absint( $data['number_bump_per_checkout'] ) : 0;

		return self::$maximumn_bump_print;
	}

	public static function get_bumps() {
		return self::$wfob_data;
	}

	public static function get_bump( $wfob_id ) {
		return isset( self::$wfob_data[ $wfob_id ] ) ? self::$wfob_data[ $wfob_id ] : null;
	}

}
