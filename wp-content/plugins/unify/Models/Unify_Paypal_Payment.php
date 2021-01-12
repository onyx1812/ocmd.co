<?php

namespace CodeClouds\Unify\Models;

use CodeClouds\Unify\Service\Request;
use \CodeClouds\Unify\Data_Sources\Connection_Handler;
use \CodeClouds\Unify\Model\Connection;
use CodeClouds\Unify\Service\Mapping\Fields;

/**
 * Payment model.
 * @package CodeClouds\Unify
 */
class Unify_Paypal_Payment extends \WC_Payment_Gateway
{
    /**
     * @var String
     */
    public $domain;

    /**
     * @var array
     */
    private $payload = [];

    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
        $this->domain = 'codeclouds_unify_paypal_payment';

        $this->id                 = 'codeclouds_unify_paypal_payment';
        $this->icon               = apply_filters('woocommerce_unify_gateway_icon', '');
        $this->order_button_text  = __( 'Pay with PayPal', $this->domain ); 
        $this->has_fields         = false;
		$this->supports           = ['subscriptions', 'products'];
        $this->method_title       = __('Unify Paypal Payment', $this->domain);
        $this->method_description = __('Accepts payments via LimeLight/Konnektive CRM and many more.', $this->domain);

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title       = $this->get_option('title');
        $this->description = $this->get_option('description');

        add_filter( 'woocommerce_order_button_html', array($this, 'custom_order_button_html'));
        add_action( 'woocommerce_checkout_after_order_review', array($this, 'second_place_order_button'), 5 );
        // Actions
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_thankyou_unify', array($this, 'thankyou_page'));       
        // Customer Emails
        add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);    
    }

    /**
     * Custom place order button 
     */
    function second_place_order_button() {
            echo '<div class="overlayDiv" style="display: none;z-index: 9999999999;width: 100%;height: 100%;position: fixed;background: #fff;opacity:0.5;left: 0;top: 0;"><div class="ajax-loader" style="max-width: 307px;display: block;padding-top: 10px;border-radius: 15px;padding-bottom: 15px;width: 100%;height:120px;position: absolute;margin: auto;left:0;right:0;top:0;bottom:0;"><center> <img class="ajax-loader-image" src="'.plugins_url( '/assets/images/loading.gif', dirname(__FILE__) ).'" alt="loading.." style="width:30px;"></center></div></div><button type="button" class="button alt place_order_paypal" name="woocommerce_paypal_checkout_place_order" id="place_order_paypal" style="width: 278px;height: 64px; font-size: 1.4rem; margin: 0 auto; background-color: #ffc924; color: #000; display: none;" value="' . esc_attr( $this->order_button_text ) . '" data-value="' . esc_attr( $this->order_button_text ) . '">' . esc_html( $this->order_button_text ) . '</button>';
        }

    /**
     * customizing woocommrece checkout button
     */
    function custom_order_button_html( $button ) {
        $chosen_payment_method = WC()->session->get('chosen_payment_method');
        if($chosen_payment_method !== "codeclouds_unify"){
            $order_button_text = __(' order', 'woocommerce');
            $button = '<div id="uni_custom_Checkout_Button"></div>';
            return $button;
        }else{
            return $button;
        }
    }


    /**
     * Initialize Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
        if (!empty($_GET['page']) && $_GET['page'] == 'wc-settings' && !empty($_GET['section']) && $_GET['section'] == 'codeclouds_unify_paypal_payment')
        {
            wp_redirect(admin_url('admin.php?page=unify-settings'), 302, 'Unify');
            die();
        }

        $this->form_fields = [
            'paypal_enabled'     => [
                'title'   => __('Enable Paypal Payment', $this->domain),
                'type'    => 'checkbox',
                'label'   => __('Enable', $this->domain),
                'default' => 'yes',
            ],
            'paypal_payment_title'       => [
                'title'       => __('Title', $this->domain),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', $this->domain),
                'default'     => __('Unify Payment', $this->domain),
                'desc_tip'    => true,
            ],
            'paypal_payment_description' => [
                'title'       => __('Description', $this->domain),
                'type'        => 'text',
                'description' => __('This controls the description which the user sees during checkout.', $this->domain),
                'default'     => __('Pay with your credit card.', $this->domain),
                'desc_tip'    => true,
            ]
        ];
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page()
    {
        if ($this->instructions)
        {
            echo wpautop(wptexturize($this->instructions));
        }
    }

    /**
     * Add content to the WC email.
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false)
    {
        if ($this->instructions && !$sent_to_admin && 'codeclouds_unify_paypal_payment' === $order->payment_method && $order->has_status('on-hold'))
        {
            echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
        }
    }

    public function payment_fields()
    {
        if (!empty($this->settings['connection']))
        {
            if ($this->get_description())
            {
                echo wpautop(wptexturize($this->get_description()));
            }
        }
        else
        {
            echo 'Plugin is not configured yet.';
        }
    }

   /**
     * Process the payment and return the result.
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id)
    {
        $order = \wc_get_order($order_id);
        $this->prepare_payload($order);
        $response = Connection_Handler::call($this->payload)->order();
        print_r($response);exit;
        
    }

    private function paypal2unify($order){

        // Return thankyou redirect
        $redirect = $this->get_return_url($order);

        return $redirect;
    }
    
    /**
     * @param Object $order
     */
    private function prepare_payload($order)
    {

        $connection    = Connection::get_post_meta($this->settings['connection']);

        $this->payload = [
            'config'      => [
                'connection'   => $connection['unify_connection_crm'][0],
                'api_username' => $connection['unify_connection_api_username'][0],
                'api_password' => \Codeclouds\Unify\Model\Protection\Decryption::make($connection['unify_connection_api_password'][0], $connection['unify_connection_salt'][0]),
                'campaign_id'  => $connection['unify_connection_campaign_id'][0],
                'shipping_id'  => ($connection['unify_connection_shipping_id'][0]!='')?$connection['unify_connection_shipping_id'][0]:'',
                'endpoint' => $connection['unify_connection_endpoint'][0],
                'offer_model'  => !empty($connection['unify_connection_offer_model'][0]) ? $connection['unify_connection_offer_model'][0] : 0,
                'is_order_note_enabled'  => !empty($connection['unify_order_note'][0]) ? $connection['unify_order_note'][0] : 0,
                'is_legacy_response_crm'  => !empty($connection['unify_response_crm_type_enable'][0]) ? $connection['unify_response_crm_type_enable'][0] : 0
            ],
            'payment_method'=> $order->get_payment_method(),
            'alt_pay_return_url'=> $this->paypal2unify($order),
            'billing'     => $order->get_address(),
            'shipping'    => $order->get_address('shipping'),
            'ip_address'  => $order->get_customer_ip_address(),
            'description' => sprintf('Payment from: %s. Order ID: #%s, %s', get_site_url(), $order->get_id(), $order->get_billing_email())
        ];

        if(!empty($order->get_customer_note()))
        {
            $this->payload['description'] = $this->payload['description'] . '. Note: ' . $order->get_customer_note();
        }

        $this->get_cart_items($order);
        $this->prepare_billing_shipping($order);
        $this->set_konnektive_shipping($connection, $order);
        $this->prepare_affiliate_paras();

    }
 

    private function get_cart_items($order)
    {
        $cart_items = $order->get_items();
        $counter = 0;

        foreach ($cart_items as $cart_item)
        {
            $variantId = $cart_item->get_variation_id();
            //$connection = \CodeClouds\Unify\Actions\Product::get_connetion($cart_item['product_id']);
            $this->payload['cart_items'][$counter] = [
                'product_id' => $cart_item['product_id'],
                'price' => $order->get_item_total($cart_item, false, false),
                'qty' => $cart_item['qty'],
                'is_variant' => ($variantId > 0) ? true : false,
                'variant_id' => ($variantId > 0) ? get_post_meta($variantId, 'unify_crm_variation_prod_id', true) : null
            ];

            foreach (Fields::get() as $field)
            {
                $this->payload['cart_items'][$counter][$field['payload']] = trim(get_post_meta($cart_item['product_id'], $field['id'], true));
            }

            foreach ($cart_item->get_meta_data() as $variant)
            {

                $this->payload['cart_items'][$counter]['variants'][] = [
                    'id' => $variant->get_data()['id'],
                    'key' => str_replace('pa_', '', $variant->get_data()['key']),
                    'value' => $variant->get_data()['value'],
                ];
            }

            $counter ++;
        }
    }

    /**
     * Check shipping address.
     * If shipping empty, assign shipping as billing.
     */
    private function prepare_billing_shipping($order)
    {
        if(!$order->has_shipping_address())
        {
            $this->payload['shipping'] = $this->payload['billing'];
        }
    }

    /**
     * Setting Order Shipping Price in Konnektive cart item payload
     *
     * @param array $connection
     * @param Object $order WooCommerce Order Object
     */
    private function set_konnektive_shipping($connection, $order)
    {
        //If CRM is Konnektive and the shipProfileId is empty then check order shipping price
        if(!empty($connection['unify_connection_crm'][0]) && $connection['unify_connection_crm'][0] == 'konnektive' && empty($connection['unify_connection_shipping_id'][0]))
        {
            foreach ($this->payload['cart_items'] as $key => $v)
            {
                $this->payload['cart_items'][$key]['shipPrice'] = $key == 0 ? wc_format_decimal($order->get_total_shipping(), 2) : 0;
            }
        }
    }


    /**
     * Preparing data for Limelight affiliate params.
     * @global type $session
     */

    private function prepare_affiliate_paras_limelight()
    {
        $affiliate_param = $_SESSION['affiliate_param'];
        $ll_affiliate_params = ['utm_source','UTM_SOURCE','utm_medium','UTM_MEDIUM','utm_campaign','UTM_CAMPAIGN','utm_term','UTM_TERM','utm_content','UTM_CONTENT','device_category','DEVICE_CATEGORY','AFID','afid','SID','sid','AFFID','affid','AID','aid','OPT','opt','c1','c2','c3','C1','C2','C3'];
       
        $affiliate_param_final = array();
        $i=1;
        foreach ($affiliate_param as $key => $value)
        {
         if(in_array($key, $ll_affiliate_params)){
             $affiliate_param_final[strtolower($key)] = $value;
         }else{
            $affiliate_param_final['c'.$i] = $value;
            $i++;
         }
        }
        
        return $affiliate_param_final;
        
    }


    /**
     * Preparing data for Konnektive affiliate params.
     * @global type $session
     */

    private function prepare_affiliate_paras_konnektive()
    {
        $affiliate_param = $_SESSION['affiliate_param'];
        $affiliate_param_values = array_values( $affiliate_param );       
        $kk_affiliate_params = ['AffiliateID','affid','afid','AFFID','AFID','AFFILIATEID','affiliateid','AffiliateID','affId'];
        $kk_sub_affiliate_params = ['sourceValue1','sourceValue1','sourceValue1','sourceValue1','sourceValue1','sourcevalue1','sourcevalue2','sourcevalue3','sourcevalue4','sourcevalue5','subId','subId2','subId3','subId4','subId5','SID','SID2','SID3','SID4','SID5'];
        $affiliate_param_final = array();
        $i=1;$j=1;
        foreach ($affiliate_param as $key => $value){          
                if(in_array($key, $kk_affiliate_params)){
                    $affiliate_param_final['affId'] = $value;
                }else if(in_array($key, $kk_sub_affiliate_params)){
                    $affiliate_param_final['sourcevalue'.$i] = $value;
                    $i++;
                }else{
                    $affiliate_param_final['c'.$j] = $value;
                    $j++;
                }
                
            }
        
        return $affiliate_param_final;
        
    }


    /**
     * Preparing data for Response affiliate params.
     * @global type $session
     */

    private function prepare_affiliate_paras_response()
    {
        $affiliate_param = $_SESSION['affiliate_param'];
        $affiliate_param_values = array_values( $affiliate_param );       
        $response_affiliate_params = ['AffiliateID','affid','afid','AFFID','AFID','AFFILIATEID','affiliateid','AffiliateID'];
        $affiliate_param_final = array();
        $i=1;
        foreach ($affiliate_param as $key => $value){           
                 if(in_array($key, $response_affiliate_params)){
                    $affiliate_param_final['affiliateid'] = $value;
                }else{
                    $affiliate_param_final['subaffiliateid'.$i] = $value;
                    $i++;
                }
                
            }
        if(empty($affiliate_param_final['affiliateid'])){
            return false;
        }
        
        return $affiliate_param_final;
        
    }

    /**
     * Preparing data for affiliate params.
     * @global type $session
     */
    private function prepare_affiliate_paras()
    {
        if (!session_id())
        {
            session_start();
        }
        $affiliate_param_final = array();

        $connection =  $this->payload['config']['connection'];
        if($connection == 'response'){
            $affiliate_param_final = $this->prepare_affiliate_paras_response();
        }elseif ($connection == 'konnektive') {
            $affiliate_param_final = $this->prepare_affiliate_paras_konnektive();
        }elseif ($connection == 'limelight') {
            $affiliate_param_final = $this->prepare_affiliate_paras_limelight();
        }

        $this->payload['affiliate_param'] = (!empty($affiliate_param_final)) ? $affiliate_param_final : [];
    }
}
