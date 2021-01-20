<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * COmpatibility with Theme alien swatches
 * Class WFOB_WC_swatches
 */
class WFOB_WC_Swatches {
	public function __construct() {
		/* checkout page */
		add_action( 'wp_footer', [ $this, 'actions' ] );

	}

	public function actions() {

		if ( function_exists( 'ta_wc_variation_swatches_constructor' ) && is_checkout() ) {
			?>
            <script>
                window.addEventListener('load', function () {
                    (function ($) {
                        $(document.body).on('wfob_quick_view_open', function () {
                            $('.variations_form').tawcvs_variation_swatches_form();
                        });
                    })(jQuery);
                });
            </script>
			<?php

		}

	}


}

WFOB_Plugin_Compatibilities::register( new WFOB_WC_Swatches(), 'swatches' );
