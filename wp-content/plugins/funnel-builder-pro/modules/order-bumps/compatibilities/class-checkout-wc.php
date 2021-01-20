<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WFOB_Checkout_WC_Objectiv {
	private $already_printed = [];

	public function __construct() {
		add_filter( 'cfw_checkout_is_enabled', [ $this, 'actions' ], 989 );
	}

	public function actions( $status ) {
		add_action( 'wp_head', [ $this, 'css' ] );
		add_filter( 'wfob_print_placeholder', [ $this, 'disallow_multi_time_below_payment' ], 10, 2 );

		return $status;
	}

	public function disallow_multi_time_below_payment( $status, $slug ) {
		if ( isset( $this->already_printed[ $slug ] ) ) {
			return false;
		}
		$this->already_printed[ $slug ] = true;

		return $status;
	}

	public function css() {
		?>
        <style>
            .wfob_bump_wrapper * {
                box-sizing: border-box;
            }
        </style>
		<?php
	}

}

WFOB_Plugin_Compatibilities::register( new WFOB_Checkout_WC_Objectiv(), 'objectiv' );
