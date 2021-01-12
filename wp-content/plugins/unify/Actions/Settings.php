<?php

namespace CodeClouds\Unify\Actions;
use \CodeClouds\Unify\Service\Request;
use \CodeClouds\Unify\Service\Helper;
use \CodeClouds\Unify\Service\Notice;
/**
 * Plugin's Tools.
 * @package CodeClouds\Unify
 */
class Settings
{	
	public static function setting()
	{
		$setting_data = \get_option('woocommerce_codeclouds_unify_settings');
		$additional_setting_option = \get_option('woocommerce_codeclouds_unify_paypal_payment_settings');
		$connection_list = \CodeClouds\Unify\Model\Connection::getArrayWithMeta();
		$shipment_list = ['' => 'Please choose an option', 1 => 'Single order with custom product', 2 => 'Multiple orders'];
		
		include_once __DIR__ . '/../Templates/setting.php';
	}
	
	public static function save_settings()
	{
		$request = Request::post();
		$nonce = $request['_wpnonce'];	
		$messages = Helper::getDataFromFile('Messages');
		$setting_option = \get_option('woocommerce_codeclouds_unify_settings');
		$additional_setting_option = \get_option('woocommerce_codeclouds_unify_paypal_payment_settings');
		
		if (wp_verify_nonce($nonce, 'unify-settings-data'))
		{
			//****** Save to option Starts *********** //
			$fields = ['enabled' => 'no', 'title' => '', 'description' => '', 'connection' => '', 'shipment_price_settings' => '','shipping_product_id' => '', 'shipping_product_offer_id' => '', 'shipping_product_billing_id' => '', 'testmode' => 'no', 'enable_debugging' => 'no'];
			$aditional_fields = ['paypal_enabled' => 'no','paypal_payment_title' => '', 'paypal_payment_description' => '', 'connection' => '', 'shipment_price_settings' => '','shipping_product_id' => '', 'shipping_product_offer_id' => '', 'shipping_product_billing_id' => '', 'testmode' => 'no', 'enable_debugging' => 'no'];
			
			foreach ($request as $reqKey => $reqValue)
			{				
				if(array_key_exists($reqKey, $fields) && !empty($reqValue)){
					$fields[$reqKey] = \esc_html($reqValue);
				}
			}

			foreach ($request as $additionalReqKey => $additionalReqValue)
			{
				if(array_key_exists($additionalReqKey, $aditional_fields) && !empty($additionalReqValue)){
					if($additionalReqKey == 'paypal_enabled'){
						$aditional_fields['enabled'] = \esc_html($additionalReqValue);
					}else if($additionalReqKey == 'paypal_payment_title'){
						$aditional_fields['title'] = \esc_html($additionalReqValue);
					}else if($additionalReqKey == 'paypal_payment_description'){
						$aditional_fields['description'] = \esc_html($additionalReqValue);
					}else{
						$aditional_fields[$additionalReqKey] = \esc_html($additionalReqValue);
					}
				}
			}
			unset($aditional_fields['paypal_enabled']);
			unset($aditional_fields['paypal_payment_title']);
			unset($aditional_fields['paypal_payment_description']);
			
			if(empty($setting_option) || empty($additional_setting_option)){
				$result = \add_option('woocommerce_codeclouds_unify_settings', $fields);
				$additional_result = \add_option('woocommerce_codeclouds_unify_paypal_payment_settings', $aditional_fields);
			}else{
				if(!empty($setting_option['connection']) && $setting_option['connection'] != $fields['connection']){
					\wp_update_post( ['ID' => $setting_option['connection'], 'post_status' => 'publish'] );
					\wp_update_post( ['ID' => $fields['connection'], 'post_status' => 'active'] );
				}
				$result = \update_option('woocommerce_codeclouds_unify_settings', $fields);

				if(!empty($additional_setting_option['connection']) && $additional_setting_option['connection'] != $aditional_fields['connection']){
					\wp_update_post( ['ID' => $additional_setting_option['connection'], 'post_status' => 'publish'] );
					\wp_update_post( ['ID' => $aditional_fields['connection'], 'post_status' => 'active'] );
				}
				$result = \update_option('woocommerce_codeclouds_unify_paypal_payment_settings', $aditional_fields);
			}
			
			//****** Save to option ENDS *********** //
			
				$msg = $messages['SETTINGS']['SAVE'];
				Notice::setFlashMessage('success', $msg);
				
				wp_redirect(Request::post('_wp_http_referer'));
				exit();
				
		}
		
		$error_msg = $messages['COMMON']['ERROR'];
		Notice::setFlashMessage('error', $error_msg);
			
		wp_redirect(Request::post('_wp_http_referer'));
		exit();
	}
}
