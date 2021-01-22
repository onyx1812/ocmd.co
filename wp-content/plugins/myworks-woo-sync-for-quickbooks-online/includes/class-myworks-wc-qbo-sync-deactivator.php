<?php

/**
 * Fired during plugin activation
 *
 * @link       http://myworks.design/software/wordpress/woocommerce/myworks-wc-qbo-sync
 * @since      1.0.0
 *
 * @package    MyWorks_WC_QBO_Sync
 * @subpackage MyWorks_WC_QBO_Sync/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MyWorks_WC_QBO_Sync
 * @subpackage MyWorks_WC_QBO_Sync/includes
 * @author     My Works <support@myworks.design>
 */
class MyWorks_WC_QBO_Sync_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		
		$url = get_bloginfo('url');//wpurl
		$company = get_bloginfo('name');
		$email = get_bloginfo('admin_email');
		$wordpress_version = get_bloginfo('version');
		
		$license_key = get_option('mw_wc_qbo_sync_license');
		$plugin_version = MyWorks_WC_QBO_Sync_Admin::return_plugin_version();
		
		$message = "<b>WooCommerce Sync for QuickBooks Online</b></br>";
		$message .= "</br>";
		$message .= "<b>License Key:</b> " . $license_key ."</br>";
		$message .= "<b>Version:</b> " . $plugin_version ."</br>";
		$message .= "</br>";
		$message .= "<b>Company:</b> " .$company ."</br>";
		$message .= "<b>Email:</b> " .$email ."</br>";
		$message .= "<b>WooCommerce URL:</b> " .$url ."</br>";
		
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type:text/html;charset=UTF-8',
		);		
		
		$to = 'notifications@myworks.design';		
		
		wp_mail($to, 'Deactivate - WooCommerce Sync', $message, $headers);
		
		$post_url = 'https://myworks.design/dashboard/api/dashboard/product/saveModule';
		
		$params = array(
			'api_version'=>'0.1',
			'result_type'=>'json',
			'process'=>'de-activated',
			'licensekey'=>$license_key,
			'version'=>$plugin_version,
			'company'=>$company,
			'email'=>$email,
			'system_url'=>$url
		);
		
		wp_remote_post($post_url, [
			'timeout' => 30,
			'body' => $params,
		]);		
		
		delete_option('mw_qbo_sync_activation_redirect');
		
		//
		delete_option('mw_wc_qbo_sync_license');
		delete_option('mw_wc_qbo_sync_access_token');
		delete_option('mw_wc_qbo_sync_localkey');
		delete_option('mw_wc_qbo_sync_qbo_is_connected');
		//
		wp_clear_scheduled_hook('mw_qbo_sync_logging_hook');
		
		//01-08-2017
		if(isset($_SESSION['mw_wc_qbo_sync_qbo_con_creds'])){
			unset($_SESSION['mw_wc_qbo_sync_qbo_con_creds']);
		}
		
		if(isset($_SESSION['mw_wc_qbo_sync_qbo_is_connected_rts'])){
			unset($_SESSION['mw_wc_qbo_sync_qbo_is_connected_rts']);
		}
		
		if(isset($_SESSION['mw_wc_qbo_sync_rts_license_data'])){
			unset($_SESSION['mw_wc_qbo_sync_rts_license_data']);
		}
		
		if(isset($_SESSION['mw_wc_qbo_sync_mwqs_session_msg'])){
			unset($_SESSION['mw_wc_qbo_sync_mwqs_session_msg']);
		}
		
		/**/
		delete_user_meta(get_current_user_id(), 'dismissed_mw_pointers');
		delete_option( 'mw_wc_qbo_sync_admin_pointers' );
		
		delete_option( 'mw_wc_qbo_sync_successfull_activation_message' );
		
		
		deactivate_plugins( plugin_dir_path( __FILE__ ) . 'myworks-woo-sync-for-quickbooks-online.php' );
		
	}

}
