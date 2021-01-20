<?php
if ( ! function_exists( 'wffn_handle_global_checkout' ) ) {
    function wffn_handle_global_checkout() {
        $get_checkout_step = WFFN_Core()->steps->get_integration_object( 'wc_checkout' );
        if ( $get_checkout_step instanceof WFFN_Step ) {
            $get_global_options = get_option( '_wfacp_global_settings' );
            $get_checkout_step->maybe_mark_funnel_as_global( $get_global_options );
        }


    }
}
