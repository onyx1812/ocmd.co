<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class WFTY_Shortcodes
 * @package WFTY
 * @author XlPlugins
 */
class WFTY_Shortcodes {


	public static function init() {
		add_shortcode( 'wfty_order_number', array( WFFN_Core()->thank_you_pages->data, 'get_order_id' ) );
		add_shortcode( 'wfty_customer_first_name', array( WFFN_Core()->thank_you_pages->data, 'get_customer_first_name' ) );
		add_shortcode( 'wfty_customer_last_name', array( WFFN_Core()->thank_you_pages->data, 'get_customer_last_name' ) );
		add_shortcode( 'wfty_customer_email', array( WFFN_Core()->thank_you_pages->data, 'get_customer_email' ) );
		add_shortcode( 'wfty_customer_phone_number', array( WFFN_Core()->thank_you_pages->data, 'get_customer_phone' ) );
		add_shortcode( 'wfty_customer_details', array( WFFN_Core()->thank_you_pages->data, 'get_customer_info' ) );
		add_shortcode( 'wfty_order_details', array( WFFN_Core()->thank_you_pages->data, 'get_order_details' ) );
	}
}
