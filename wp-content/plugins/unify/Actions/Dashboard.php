<?php

namespace CodeClouds\Unify\Actions;
use \CodeClouds\Unify\Service\Request;
use \CodeClouds\Unify\Service\Helper;
use \CodeClouds\Unify\Service\Notice;
/**
 * Plugin's Tools.
 * @package CodeClouds\Unify
 */
class Dashboard
{
    /**
     * Setup tools page
     */
    public static function unify_upgrade_to_pro()
    {  
		global $wpdb, $current_user;
		\wp_get_current_user();
        include_once __DIR__ . '/../Templates/upgrade-to-pro.php';
    }

    public static function dashboard_page()
    {  
		global $wpdb, $current_user;
		\wp_get_current_user();
		
		// We add 'wc-' prefix when is missing from order staus
		// $status = 'wc-' . str_replace('wc-', '', $status);

		$todays_order_count = $wpdb->get_var("
			SELECT count(ID)  FROM {$wpdb->prefix}posts WHERE post_status = 'wc-processing' AND `post_type` = 'shop_order' AND date(`post_date`) = '".\date('Y-m-d')."'
		");
		
		// Total Connection Count
		$count_posts = wp_count_posts('unify_connections');
		$total_publish_posts = $count_posts->publish;   
		
		$args = [
			'post_type' => 'product',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'codeclouds_unify_connection',
					'value' => '',
					'compare' => '!='
				)
			)
		];
		$mapped_product = new \WP_Query( $args );		
		
        include_once __DIR__ . '/../Templates/dashboard.php';
    }
	
    public static function request_unify_pro()
    {  		
		$request = Request::post();
		$nonce = $request['_wpnonce'];	
		$messages = Helper::getDataFromFile('Messages');
		
		if (wp_verify_nonce($nonce, 'request_unify_pro_chk'))
		{
			//****** Form Validate Starts *********** //
			$err = self::validate_request_pro_form($request, $messages);
			if(!empty($err)){
				Notice::setFlashMessage('error', $err);
				wp_redirect(Request::post('_wp_http_referer'));
				exit();
			}
			//****** Form Validate ENDS *********** //
			$logo = plugins_url('unify/assets/images/unify-logo-email.png');
			date_default_timezone_set(timezone_name_from_abbr('EST'));
			$request_time = date('F j, Y'). ' at ' . date('g:ia');
			
			$user_ip = $_SERVER['REMOTE_ADDR'];
			$location_details = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
			$location = (!empty($location_details) && !empty($location_details->city)) ? $location_details->city.', ' : '';
			$location .= (!empty($location_details) && !empty($location_details->country)) ? $location_details->country : '';
			
			$message_template = @file_get_contents(Helper::getDataFromFile('request-unfiy-pro'), true);			
			$message_template = str_replace(['{$full_name}', '{$comment}', '{$email_address}', '{$phone}', '{$company}', '{$website_url}', '{$ip_address}', '{$logo}', '{$request_time}', '{$location}'], [$request['full_name'], $request['comment'], $request['email_address'], $request['phone_number'], $request['company_name'], get_site_url(), $user_ip, $logo, $request_time, $location], $message_template);
			$mail_data = [
//				'to' => ['sales@unify.to'],
				'to' => ['sales@codeclouds.com'],
//				'to' => 'shoubhik.ghosh@codeclouds.in',
				'subject' => 'Request for Unify Pro Version',
				'message' => $message_template,
			];
			$message_header_admin = [
				'Content-Type: text/html; charset=UTF-8',
//				'From: Unify Team <noreply@unify.to>',
				'Reply-To: '.$request['full_name'].' <'.$request['email_address'].'>',
				'Bcc: Subhadip Naskar <subhadip@codeclouds.in>',
//				'Bcc: raunak.gupta@codeclouds.in',
				'Bcc: shoubhik.ghosh@codeclouds.in'
			];
			$response = wp_mail($mail_data['to'], $mail_data['subject'], $mail_data['message'], $message_header_admin);
			
			$message_template_user = @file_get_contents(Helper::getDataFromFile('request-unfiy-pro-user'), true);			
			$message_template_user = str_replace(['{$full_name}', '{$logo}'], [$request['full_name'], $logo], $message_template_user);
			$mail_data_user = [
				'to' => $request['email_address'],
				'subject' => 'We\'ve received your request for upgrade to Unify Pro!',
				'message' => $message_template_user,
			];
			$message_header_user = [
				'Content-Type: text/html; charset=UTF-8',
//				'From: Unify Team <noreply@unify.to>',
				'Reply-To: Unify Team <support@unify.to>',
				'Bcc: Subhadip Naskar <subhadip@codeclouds.in>',
//				'Bcc: raunak.gupta@codeclouds.in',
				'Bcc: shoubhik.ghosh@codeclouds.in'
			];
			$response_user = wp_mail($mail_data_user['to'], $mail_data_user['subject'], $mail_data_user['message'], $message_header_user);
			
			if($response){
				$msg = $messages['REQUEST_UNIFY_PRO']['MAIL_SENT'];
				Notice::setFlashMessage('success', $msg);
				
				wp_redirect(Request::post('_wp_http_referer'));
				exit();
			}
		}
		
		$error_msg = $messages['COMMON']['ERROR'];
		Notice::setFlashMessage('error', $error_msg);
			
		wp_redirect(Request::post('_wp_http_referer'));
		exit();
    }
	
	public static function validate_request_pro_form($request, $messages)
	{
		$validate_field = ['full_name', 'company_name', 'email_address', 'phone_number', 'comment'];
		$err = '';
		foreach ($validate_field as $key => $value)
		{
			if(empty($request[$value])){
				$err .= '<span style="display:block;" >'.$messages['VALIDATION']['REQUEST_UNIFY_PRO'][strtoupper($value)] . '</span>';
			}else{
				if($value == 'email_address' && !filter_var($request[$value], FILTER_VALIDATE_EMAIL)){
					$err .= '<span style="display:block;" >'.$messages['VALIDATION']['REQUEST_UNIFY_PRO'][strtoupper($value).'_INVALID'] . '</span>';
				}
				
				if($value == 'phone_number' && !preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $request[$value]) && (strlen($request[$value]) > 10 || strlen($request[$value]) < 10) ){
					$err .= '<span style="display:block;" >'.$messages['VALIDATION']['REQUEST_UNIFY_PRO'][strtoupper($value).'_INVALID'] . '</span>';
				}
			}
		}
		return $err;
	}
}
