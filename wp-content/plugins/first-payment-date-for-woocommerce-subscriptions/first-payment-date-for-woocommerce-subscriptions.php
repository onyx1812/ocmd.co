<?php
/*
Plugin Name: 	First payment date for WooCommerce Subscriptions
Plugin URI: 	https://codection.com/first-payment-date-for-wooCommerce-subscriptions
Description: 	This plugin allows you to set a first payment date for a subscription, using WooCommerce Subscriptions. So you will be able to set a specific date (not a generic trial period) for each product
Version: 		0.2.1
Author: 		Codection
Author URI: 	https://codection.com
License:        GPL2
License URI:    https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: 	first-payment-date-for-woocommerce-subscriptions
Domain Path: 	/languages
WC requires at least: 2.4.0
WC tested up to: 4.5.1
*/

function fpdws_init() {
  load_plugin_textdomain( 'first-payment-date-for-woocommerce-subscriptions', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'fpdws_init' );

add_filter( 'woocommerce_subscriptions_product_trial_length', 'fpdws_woocommerce_subscriptions_product_trial_length', 10, 2 );
function fpdws_woocommerce_subscriptions_product_trial_length( $subscription_trial_length, $product ){
	if( is_admin() || empty( $product ) )
		return $subscription_trial_length;

	$id = $product->get_id();
	if( $product->get_type() == 'variable-subscription' ){
		$variations = $product->get_children();
		$id = $variations[0];
	}

	$type = get_post_meta( $id, '_fpdws_date_first_payment_type', true );
	$earlier = new DateTime();

	if( $type == 'none' ){
		return $subscription_trial_length;
	}

	if( $type == 'fixed' || empty( $type ) ){
		$date = get_post_meta( $id, '_fpdws_date_first_payment', true );

		if( empty( $date ) )
			return $subscription_trial_length;

		$later = new DateTime( $date );
	}
	elseif( $type == 'calculated' ){
		$calculated = get_post_meta( $id, '_fpdws_date_first_payment_calculated', true );

		if( $calculated == 'first_day_next_month' )
			$later = new DateTime( 'first day of next month' );
		elseif( $calculated == 'first_day_next_year' )
			$later = new DateTime( ( date( 'Y' ) + 1 ) . '-01-01' );
	}

	return $later->diff($earlier)->format("%a");
}

add_filter( 'woocommerce_subscriptions_product_trial_period', 'fpdws_woocommerce_subscriptions_product_trial_period', 10, 2 );
function fpdws_woocommerce_subscriptions_product_trial_period( $subscription_trial_period, $product ){
	if( is_admin() || empty( $product ) )
		return $subscription_trial_period;

	$id = $product->get_id();
	if( $product->get_type() == 'variable-subscription' ){
		$variations = $product->get_children();
		$id = $variations[0];
	}

	$date = get_post_meta( $id, '_fpdws_date_first_payment', true );

	if( empty( $date ) || $date = 'none' )
		return $subscription_trial_period;
	
	return 'day';
}

function fpdws_es_suscripcion( $product ){
	return class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product );
}

function fpdws_first_date_payment() {
	global $post;

	$product = wc_get_product( $post->ID );

	if( !fpdws_es_suscripcion( $product ) )
		return;

	if( $product->has_child() )
		return;

	echo '<div class="options_group">';

	woocommerce_wp_select( array(
        'id'      => '_fpdws_date_first_payment_type',
        'label'   => __( 'First payment date type', 'first-payment-date-for-woocommerce-subscriptions' ),
        'options' => array( 
        	'none' => __( 'Use standard trial period', 'first-payment-date-for-woocommerce-subscriptions' ),
        	'fixed' => __( 'Fixed date', 'first-payment-date-for-woocommerce-subscriptions' ),
        	'calculated' => __( 'Calculated day', 'first-payment-date-for-woocommerce-subscriptions' ),
        ),
        'value'	  => get_post_meta( $product->get_id(), '_fpdws_date_first_payment_type', true ),
    ) );

	woocommerce_wp_text_input(
		array(
			'id'          => '_fpdws_date_first_payment',
			'label'       => __( 'Fixed first payment date', 'first-payment-date-for-woocommerce-subscriptions' ),
			'description' => __( 'Set the date when the first payment of this subscription has to be done.', 'first-payment-date-for-woocommerce-subscriptions' ),
			'input_class' => array( 'hasDatepicker' ),
			'value' 	  => get_post_meta( $product->get_id(), '_fpdws_date_first_payment', true ),
		 	'type' 		  => 'date',
		)
	 );
	 
	woocommerce_wp_select( array(
        'id'      => '_fpdws_date_first_payment_calculated',
        'label'   => __( 'First payment date', 'first-payment-date-for-woocommerce-subscriptions' ),
        'options' => array( 
        	'first_day_next_month' => __( 'First day next month', 'first-payment-date-for-woocommerce-subscriptions' ), 
        	'first_day_next_year' => __( 'First day next year', 'first-payment-date-for-woocommerce-subscriptions' ), 
        ),
        'value'	  => get_post_meta( $product->get_id(), '_fpdws_date_first_payment_calculated', true ),
    ) );

 	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'fpdws_first_date_payment' );

function fpdws_woocommerce_process_product_meta( $post_id ){
	if( isset( $_POST['_fpdws_date_first_payment_type'] ) )
		update_post_meta( $post_id, '_fpdws_date_first_payment_type', sanitize_text_field( $_POST['_fpdws_date_first_payment_type'] ) );

	if( isset( $_POST['_fpdws_date_first_payment'] ) )
		update_post_meta( $post_id, '_fpdws_date_first_payment', sanitize_text_field( $_POST['_fpdws_date_first_payment'] ) );

	if( isset( $_POST['_fpdws_date_first_payment_calculated'] ) )
		update_post_meta( $post_id, '_fpdws_date_first_payment_calculated', sanitize_text_field( $_POST['_fpdws_date_first_payment_calculated'] ) );
}
add_action( 'woocommerce_process_product_meta', 'fpdws_woocommerce_process_product_meta' );

function fpdws_woocommerce_product_after_variable_attributes( $loop, $variation_data, $variation ) {
	echo '<div class="options_group form-row form-row-full">';
		woocommerce_wp_select( array(
			'id'      => '_fpdws_date_first_payment_type_' . $variation->ID,
			'name'      => '_fpdws_date_first_payment_type[' . $variation->ID . ']',
			'label'   => __( 'First payment date type', 'first-payment-date-for-woocommerce-subscriptions' ),
			'options' => array( 
	        	'none' => __( 'Use standard trial period', 'first-payment-date-for-woocommerce-subscriptions' ),
	        	'fixed' => __( 'Fixed date', 'first-payment-date-for-woocommerce-subscriptions' ),
	        	'calculated' => __( 'Calculated day', 'first-payment-date-for-woocommerce-subscriptions' ),
	        ),
			'value'	  => get_post_meta( $variation->ID, '_fpdws_date_first_payment_type', true ),
		) );
	
		woocommerce_wp_text_input(
			array(
				'id'          => '_fpdws_date_first_payment_' . $variation->ID,
				'name'          => '_fpdws_date_first_payment[' . $variation->ID . ']',
				'label'       => __( 'First payment date', 'first-payment-date-for-woocommerce-subscriptions' ),
				'desc_tip'    => true,
				'description' => __( 'Set the date when the first payment of this subscription has to be done.', 'first-payment-date-for-woocommerce-subscriptions' ),
				'type' 		  => 'date',
				'value' 	  => get_post_meta( $variation->ID, '_fpdws_date_first_payment', true )
		) );
		 
		woocommerce_wp_select( array(
			'id'      => '_fpdws_date_first_payment_calculated' . $variation->ID,
			'name'      => '_fpdws_date_first_payment_calculated[' . $variation->ID . ']',
			'label'   => __( 'First payment date', 'first-payment-date-for-woocommerce-subscriptions' ),
			'options' => array( 
	        	'first_day_next_month' => __( 'First day next month', 'first-payment-date-for-woocommerce-subscriptions' ), 
	        	'first_day_next_year' => __( 'First day next year', 'first-payment-date-for-woocommerce-subscriptions' ), 
	        ),
			'value'	  => get_post_meta( $variation->ID, '_fpdws_date_first_payment_calculated', true ),
		) );

	echo '</div>';

	?>	
	<script>
	jQuery( document ).ready( function( $ ){
		function fpwds_hide_show<?php echo $variation->ID;?>(){
			var type = $( "#_fpdws_date_first_payment_type_<?php echo $variation->ID;?>" ).val();
			
			if( type == 'none' ){
				$( "._fpdws_date_first_payment_<?php echo $variation->ID;?>_field" ).hide();
				$( "._fpdws_date_first_payment_calculated<?php echo $variation->ID;?>_field" ).hide();
			}
			else if( type == 'fixed' ){
				$( "._fpdws_date_first_payment_<?php echo $variation->ID;?>_field" ).show();
				$( "._fpdws_date_first_payment_calculated<?php echo $variation->ID;?>_field" ).hide();
			}				
			else if( type == 'calculated' ){
				$( "._fpdws_date_first_payment_calculated<?php echo $variation->ID;?>_field" ).show();
				$( "._fpdws_date_first_payment_<?php echo $variation->ID;?>_field" ).hide();
			}				
		}

		$( "#_fpdws_date_first_payment_type_<?php echo $variation->ID;?>" ).change( function(){
			fpwds_hide_show<?php echo $variation->ID;?>();
		} );

		fpwds_hide_show<?php echo $variation->ID;?>();
	} );
	</script>
	<?php
}
add_action( 'woocommerce_product_after_variable_attributes', 'fpdws_woocommerce_product_after_variable_attributes', 10, 3 );

function fpdws_woocommerce_save_product_variation( $post_id ){
	update_post_meta( $post_id, '_fpdws_date_first_payment_type', sanitize_text_field( $_POST['_fpdws_date_first_payment_type'][ $post_id ] ) );
	update_post_meta( $post_id, '_fpdws_date_first_payment', sanitize_text_field( $_POST['_fpdws_date_first_payment'][ $post_id ] ) );
	update_post_meta( $post_id, '_fpdws_date_first_payment_calculated', sanitize_text_field( $_POST['_fpdws_date_first_payment_calculated'][ $post_id ] ) );
}
add_action( 'woocommerce_save_product_variation', 'fpdws_woocommerce_save_product_variation', 10, 2 );

function fpdws_footer_scripts(){
	global $pagenow;

	if( !in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) )
		return;
	?>
	<script>
	jQuery( document ).ready( function( $ ){
		function fpwds_hide_show(){
			var type = $( '#_fpdws_date_first_payment_type' ).val();
			
			if( type == 'none' ){
				$( '._fpdws_date_first_payment_field' ).hide();
				$( '._fpdws_date_first_payment_calculated_field' ).hide();
			}
			else if( type == 'fixed' ){
				$( '._fpdws_date_first_payment_field' ).show();
				$( '._fpdws_date_first_payment_calculated_field' ).hide();
			}				
			else if( type == 'calculated' ){
				$( '._fpdws_date_first_payment_calculated_field' ).show();
				$( '._fpdws_date_first_payment_field' ).hide();
			}				
		}

		$( '#_fpdws_date_first_payment_type' ).change( function(){
			fpwds_hide_show();
		} );

		fpwds_hide_show();
	} );
	</script>
	<?php
}
add_action( 'admin_footer', 'fpdws_footer_scripts' );