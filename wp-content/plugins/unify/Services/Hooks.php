<?php

/**
 * All Hooks
 * @package CodeClouds\Unify
 */

/**
 * Loads assets
 */
add_action('admin_enqueue_scripts', ['CodeClouds\Unify\Actions\Assets', 'load_admin_assets_unify_connections']);

/**
 * Custom Post Type
 */
add_action('init', ['CodeClouds\Unify\Actions\Block', 'unify_connections']);

add_action('admin_menu', ['CodeClouds\Unify\Actions\Menu', 'add_settings_to_menu']);
add_action('admin_menu', ['CodeClouds\Unify\Actions\Menu', 'alter_menu_label']);

add_action('add_meta_boxes', ['CodeClouds\Unify\Actions\Block', 'add_unify_connections_metaboxes']);

add_action('save_post', ['CodeClouds\Unify\Actions\Block', 'save_unify_connections_metaboxes'], 1, 2);

/**
 * Add connection ID field to product
 */
add_action('woocommerce_product_options_related', ['CodeClouds\Unify\Actions\Product', 'product_options_grouping']);

add_action('woocommerce_process_product_meta', ['CodeClouds\Unify\Actions\Product', 'save_connection_id']);

/**
 * Product column
 */
add_filter("manage_edit-product_columns", ['CodeClouds\Unify\Actions\Product', 'woo_product_extra_columns']);

add_action("manage_posts_custom_column", ['CodeClouds\Unify\Actions\Product', 'woo_product_extra_columns_content']);

/**
 * Payment Gateway
 */
add_action('plugins_loaded', ['CodeClouds\Unify\Actions\Gateway', 'init']);

add_filter('woocommerce_payment_gateways', ['CodeClouds\Unify\Actions\Gateway', 'add_unify_gateway_class']);

add_action('woocommerce_checkout_fields', ['CodeClouds\Unify\Actions\Checkout', 'checkout_validation']);

add_action('woocommerce_checkout_process', ['CodeClouds\Unify\Actions\Checkout', 'process_unify_payment']);

/**
 * Order View
 */
add_action('woocommerce_admin_order_data_after_order_details', ['CodeClouds\Unify\Actions\Order', 'add_connection_details_to_view']);

/**
 * Tools
 */
add_action('admin_post_codeclouds_unify_tool_import', ['CodeClouds\Unify\Actions\Product', 'import_connections']);

add_action('admin_post_codeclouds_unify_tool_download', ['CodeClouds\Unify\Actions\Product', 'download_csv']);

add_action('admin_post_codeclouds_unify_tool_mapping', ['CodeClouds\Unify\Actions\Product', 'product_mapping']);

/**
 * About
 */
add_action('in_admin_footer', ['CodeClouds\Unify\Actions\About', 'copyright_msg']);

/**
 * Footer
 */
// Checkout validation
add_action('wp_footer', ['CodeClouds\Unify\Actions\Checkout', 'checkout_js_validation']);


add_action('admin_post_unify_connections_post', ['CodeClouds\Unify\Actions\Connection', 'save_connection']);
add_action('admin_post_unify_connections_delete', ['CodeClouds\Unify\Actions\Connection', 'delete_connection']);
add_action('admin_post_unify_product_post', ['CodeClouds\Unify\Actions\Tools', 'save_product']);
add_action('admin_post_request_unify_pro', ['CodeClouds\Unify\Actions\Dashboard', 'request_unify_pro'] );
add_action('admin_post_unify_settings_form_post', ['CodeClouds\Unify\Actions\Settings', 'save_settings']);

// Add hook for admin <head></head>
add_action( 'wp_ajax_bulk_delete_conn', ['CodeClouds\Unify\Actions\Connection', 'bulk_delete_conn'] );
add_action( 'wp_ajax_bulk_restore_conn', ['CodeClouds\Unify\Actions\Connection', 'bulk_restore_conn'] );
add_action( 'wp_ajax_activate_conn', ['CodeClouds\Unify\Actions\Connection', 'activate_conn'] );

// Add specific CSS class by filter. 
add_filter( 'admin_body_class', function( $classes ) {
	
	if (!empty($_GET['page']) && !empty(strrchr($_GET['page'], 'unify'))){
		return $classes . ' unify_body ';
	}    
} );

// Registering custom post status
add_action( 'init', ['CodeClouds\Unify\Actions\Connection', 'custom_post_status_active'] );

// collecting affiliate params on template load.
add_action( 'template_redirect', ['CodeClouds\Unify\Actions\Checkout', 'collect_affiliate_param']);


// Adding Custom fields (CRM Variation ID) for woocommerce product variation
add_action( 'woocommerce_product_after_variable_attributes', ['CodeClouds\Unify\Actions\Product', 'add_custom_field_to_variations'], 10, 3);
add_action( 'woocommerce_save_product_variation', ['CodeClouds\Unify\Actions\Product', 'save_custom_field_variations'], 10, 2 );

add_action('woocommerce_thankyou', 'paypalPaymentThankyou', 10, 1);

function paypalPaymentThankyou($order_id) { 
    $order = wc_get_order($order_id); 
    $debug = false;
    $wc_codeclouds_unify_settings = get_option('woocommerce_codeclouds_unify_settings');
	if (!empty($wc_codeclouds_unify_settings['enable_debugging']) && $wc_codeclouds_unify_settings['enable_debugging'] == 'yes')
			{
				$debug = true;
			}

	$connection    = get_post_meta($wc_codeclouds_unify_settings['connection']);
    $response = $_REQUEST;
    $orderid = !empty($_REQUEST['orderId'])?$_REQUEST['orderId']:'';
    $tran_id = !empty($_REQUEST['transactionID'])?$_REQUEST['transactionID']:'';
       
	$hasInserted = get_post_meta($order->id, '_codeclouds_unify_order_id', true);

    if($orderid!='' && $hasInserted==''){
	    $order->update_meta_data('_codeclouds_unify_order_id', $orderid);
	    $order->update_meta_data('_codeclouds_unify_transaction_id', $tran_id);
	    $order->update_meta_data('_codeclouds_unify_connection', $connection['unify_connection_crm']);
	    $order->update_meta_data('_codeclouds_unify_connection_id', $wc_codeclouds_unify_settings['connection']);
	    $order->payment_complete($orderid);
	    $order->save();
	    if ($debug)
			{
			    $context = array('source' => 'Unify-App');
				$logger = wc_get_logger();
				$logger->info(('LL Response: ' . json_encode($response, JSON_PRETTY_PRINT)), $context);
				WC()->session->__unset( 'chosen_payment_method' );
			}
	}


}

