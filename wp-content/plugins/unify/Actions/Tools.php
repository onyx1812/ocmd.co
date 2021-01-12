<?php

namespace CodeClouds\Unify\Actions;
use \CodeClouds\Unify\Service\Request;
use CodeClouds\Unify\Model\Tools_model;
use \CodeClouds\Unify\Service\Notice;
use \CodeClouds\Unify\Service\Helper;

/**
 * Plugin's Tools.
 * @package CodeClouds\Unify
 */
class Tools
{
	/**
	 * Setup tools page
	 */	
    public static function tools_page()
    {  
		global $wpdb;
		$request = $_GET;
			
		$sections = [
			'product-mapping',
			'import-export',
		];
		
		if (!empty(Request::get('section')) && Request::get('section') == 'product-mapping')
		{
			//******* Get setting for connection Starts ********
			$crm_meta = '';
			$crm_model_meta = '';

			$setting_option = \get_option('woocommerce_codeclouds_unify_settings');

			if (!empty($setting_option) && $setting_option['enabled'] == 'yes' && !empty($setting_option['connection']))
			{
				$meta_data = \get_post_meta($setting_option['connection'], 'unify_connection_crm');
				
				if (!empty($meta_data))
				{
					$crm_meta = $meta_data[0];
					
					if($crm_meta == 'limelight'){
						$meta_model_data = \get_post_meta($setting_option['connection'], 'unify_connection_offer_model');
						$crm_model_meta = (!empty($meta_model_data)) ? $meta_model_data[0] : '';
					}

					if($crm_meta == 'response'){
						$meta_model_data = \get_post_meta($setting_option['connection'], 'unify_response_crm_type_enable');
						$crm_model_meta = (!empty($meta_model_data)) ? $meta_model_data[0] : '';
					}


				}
			}
			//******* Get setting for connection Ends ********
			
			$request['paged'] = (empty($request['paged'])) ? 1 : $request['paged'];
			$request['posts_per_page'] = (empty($request['posts_per_page'])) ? 10 : $request['posts_per_page'];
			
			$request['orderby'] = (empty($request['orderby'])) ? 'post_title' : $request['orderby'];
			$request['order'] = (empty($request['order'])) ? 'asc' : $request['order'];
			
			$tools_model_object = new Tools_model();
			$data = $tools_model_object->get_products_with_meta($request);

			$prev_dis = (($request['paged'] == 1)) ? true : false;
			$next_dis = (!empty($request['paged']) && $request['paged'] == $data['total']) ? true : false;
		}
		
        include_once __DIR__ . '/../Templates/tools.php';
    }
	
    public static function save_product()
	{
		$nonce = Request::post('_wpnonce');
		$messages = Helper::getDataFromFile('Messages');
		$param = '';
		
		if (wp_verify_nonce($nonce, 'unify-product') && Request::post('check_submit') == 'update_product')
		{
			$fields = ['codeclouds_unify_connection', 'codeclouds_unify_shipping', 'codeclouds_unify_offer_id', 'codeclouds_unify_billing_model_id', 'codeclouds_unify_group_id'];
			
			foreach (Request::post()['map'] as $post_id => $value)
			{
				foreach ($value as $field_key => $field_val)
				{
					if (in_array($field_key, $fields))
					{
						if (count(\get_post_meta($post_id, $field_key, true)) > 0)
						{
							if (!empty($field_val))
							{
								/**
								 * If the custom field already has a value, update it.
								 */
								var_dump(\update_post_meta($post_id, $field_key, trim(esc_attr($field_val))));
							}
							else
							{
								/**
								 * Delete the meta key if there's no value
								 */
								\delete_post_meta($post_id, $field_key);
							}
						}
								else
						{
							/**
							 * If the custom field doesn't have a value, add it.
							 */
							\add_post_meta($post_id, $field_key, trim(esc_attr($field_val)));
						}
					}
						}
			}
			$msg = $messages['PRODUCT_MAP']['SUCCESS'];
			Notice::setFlashMessage('success', $msg);
		}else if (Request::post('check_submit') == 'sort_field'){ // sort_field is for sorting	
			$orderBy = (!empty(Request::post('orderby'))) ? Request::post('orderby') : 'post_title';
			$order = (!empty(Request::post('order'))) ? Request::post('order') : 'desc';
			$param .= (empty($_GET['orderby'])) ? '&orderby='.$orderBy : $orderBy;
			$param .= (empty($_GET['order'])) ? '&order='.$order : $order;
		}else{
			$msg = $messages['COMMON']['ERROR'];
			Notice::setFlashMessage('error', $msg);
		}
		
		wp_redirect(Request::post('_wp_http_referer').(!empty($param) ? $param : ''));
		exit();
	}
}
