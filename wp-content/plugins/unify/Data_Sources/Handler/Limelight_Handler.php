<?php

namespace CodeClouds\Unify\Data_Sources\Handler;

/**
 * Handle Limelight API for process orders.
 * @package CodeClouds\Unify
 */
class Limelight_Handler extends \CodeClouds\Unify\Abstracts\Order_Abstract
{
	private $debug = false;
	public $messages = [];
	
    /**
     * Connection authentication.
     * @param array $args Information for API.
     */
    public function __construct($args)
    {
        $this->api_payload  = $args;
        $this->api_instance = new \CodeClouds\Limelight\API(
                $this->api_payload['config']['endpoint'],
                $this->api_payload['config']['api_username'],
                \stripslashes($this->api_payload['config']['api_password'])
        );
		
		$this->messages = \CodeClouds\Unify\Service\Helper::getDataFromFile('Messages');
    }

	public function set_config($connection, $name)
    {
        if(!empty($this->api_payload['config']['offer_model']) && $this->api_payload['config']['offer_model'] == 1)
        {
            $this->api_config = \file_get_contents(__DIR__ . '/../../Config/' . strtolower($connection) . '/om_' . $name . '.config.json');
        }
        else
        {
            parent::set_config($connection, $name);
        }
    }
	
	/*
	 * checking is billing model is on
	 */
	private function is_billing_model()
	{
		if (!empty($this->api_payload['config']['offer_model']) && $this->api_payload['config']['offer_model'] == 1)
		{
			return true;
		}
		return false;
	}

    /**
     * Call API for payment process.
     * @return array
     */
    public function make_order()
	{
		$payment_method = $this->api_payload['payment_method'];
		try
		{
			$wc_codeclouds_unify_settings = get_option('woocommerce_codeclouds_unify_settings');
			$context = array('source' => 'Unify-App');
			if (!empty($wc_codeclouds_unify_settings['enable_debugging']) && $wc_codeclouds_unify_settings['enable_debugging'] == 'yes')
			{
				$this->debug = true;
			}

			$this->prepare_order_payload();

			if (!empty($wc_codeclouds_unify_settings['shipment_price_settings']) && $wc_codeclouds_unify_settings['shipment_price_settings'] == 2)
			{
				$is_error = true;
				$orderIds = [];
				$transactionIds = [];
				$notes = [];

				$newCart = $this->prepare_multi_shipping_cart();

				foreach ($newCart as $k => $val)
				{
					unset($this->api_config['upsellProductIds']);
					unset($this->api_config['upsellCount']);
					$this->api_payload['cart_items'] = $val;
					
					$this->format_data();
					$this->get_product_variant_payload();

					$this->api_config['shippingId'] = ($k == 'default') ? $this->api_config['shippingId'] : $k;
					
					$this->process_to_crm();


					if ($this->api_response['responseCode'] != 100 && $this->api_response['errorFound'] > 0)
					{
						$notes[] = (isset($this->api_response['declineReason']) && !empty($this->api_response['declineReason']) ? $this->api_response['declineReason'] : $this->api_response['errorMessage']);
						continue;
					}
					$is_error = false;
					$orderIds[] = $this->api_response['orderId'];
					$transactionIds[] = $this->api_response['transactionId'];
					unset($this->api_response);
				}

				if ($is_error)
				{
					throw new \Exception(implode(' <br/> ', $notes), 9999);
				}
				
				return ['status' => true, 'orderIds' => implode(', ', $orderIds), 'transactionIds' => implode(', ', $transactionIds), 'notes' => $notes];
			}
			else
			{

				$this->format_data();
				$this->get_product_variant_payload();
				$this->get_shipping_product($wc_codeclouds_unify_settings);
				($payment_method=='codeclouds_unify_paypal_payment')?$this->process_crm_paypal():$this->process_to_crm();
				if($payment_method=='codeclouds_unify_paypal_payment'){
					return $this->api_response;
				}else{
					if ($this->api_response['responseCode'] != 100 && $this->api_response['errorFound'] > 0)
					{
						throw new \Exception((isset($this->api_response['declineReason']) && !empty($this->api_response['declineReason']) ? $this->api_response['declineReason'] : $this->api_response['errorMessage']), 9999);
					}

					return ['status' => true, 'orderIds' => $this->api_response['orderId'], 'transactionIds' => $this->api_response['transactionIds'], 'notes' => []];
				}
			}
		}
		catch (\Exception $ex)
		{
			if ($ex->getCode() == 9999)
			{
				throw new \Exception($ex->getMessage());
			}

			throw new \Exception($this->messages['COMMON']['PAYMENT_FAILED']);
		}
	}

	private function paypal_response_nonbilling($response){
		if(is_string($response)){
			if(strpos($response, 'errorFound') !== false){
				parse_str($response,$output);
				$responseArr = ['result'=>'failure','messages'=>!empty($output['errorMessage'])?"<ul class='woocommerce-error' role='alert'><li>".$output['errorMessage']."</li></ul>":''];
			}else{
				$responseArr = ['result'=>$response,'messages'=>''];
			}
		}
		return json_encode($responseArr);
	}

	/**
     * Prepare order payload before sending to the API.
     */
    private function prepare_order_payload()
    { 
    	$payment_method = $this->api_payload['payment_method'];
        $this->api_payload['method']           = 'NewOrder';
        $this->api_payload['tran_type']        = 'Sale';
        (count($this->api_payload['cart_items']) > 1) ? $this->api_payload['upsell_count'] = true : '';

        if($payment_method == 'codeclouds_unify_paypal_payment'){
			$this->api_payload['card']['type']= "paypal";
		}else{
        	$this->api_payload['card']['type']     = $this->get_cctype($this->api_payload['card']['type']);
        	$this->api_payload['card']['exp_year'] = \substr($this->api_payload['card']['exp_year'], -2);
        }
    }

	/**
     * Get the name of card type.
     * @param type $cc_code
     * @return String Card type name.
     */
    private function get_cctype($cc_code)
    {
        $card_types = \json_decode(
            \file_get_contents(__DIR__ . '/../../Config/limelight/cards.json'), true
        );

        if (isset($card_types[$cc_code]))
        {
            return $card_types[$cc_code];
        }

        return '';
    }


    /*
	 * prepare product variant for payload
	 */
	private function get_product_variant_payload()
	{
		foreach ($this->api_payload['cart_items'] as $key => $product)
		{
			if (!empty($product['variants']))
			{
				foreach ($product['variants'] as $variant)
				{
					if($this->is_billing_model())
					{
						$this->api_config['offers'][$key]['variant'][] = [
							'attribute_name' => $variant['key'],
							'attribute_value' => $variant['value']
						];			
					}
					else
					{
						$this->api_config['product_attribute'][$product['connection_product_id']][$variant['key']] = $variant['value'];			
					}
				}
			}
		}
		
		$is_note_enabled = $this->api_payload['config']['is_order_note_enabled'];
		if($is_note_enabled == 1){
			$this->orderNoteCondition();
		}
	}


	private function orderNoteCondition(){

		$notes = "<br>";
		foreach ($this->api_payload['cart_items'] as $key => $product)
		{
			if (!empty($product['variants']))
			{
				$this->productPrice[$product['connection_product_id']] = empty($this->productPrice[$product['connection_product_id']]) ? 0 : $this->productPrice[$product['connection_product_id']];
				$notes .= "<strong>Product ID: " . $product['connection_product_id'] . "</strong> Quantity :- ". $product["qty"] . "<br>";
				$notes .= "<strong>Variant Details: </strong><br>";
				foreach ($product['variants'] as $variant)
				{
						$notes .= $variant['key'] . ":-" . $variant['value'] . "<br>";
						if($this->api_config['productId'] === $product['connection_product_id'])
						{
							$upsells = !empty($this->api_config['upsellProductIds']) ? explode(',', $this->api_config['upsellProductIds']) : [];
							while (($k = array_search($product['connection_product_id'], $upsells))!== false) 
							{
							unset($upsells[$k]);
							}
							$this->api_config['upsellProductIds'] = implode(',', $upsells);
						}
						else
						{
							$upsells = !empty($this->api_config['upsellProductIds']) ? explode(',', $this->api_config['upsellProductIds']) : [];
							$this->api_config['upsellProductIds'] = implode(',', array_unique($upsells));
						}
						
						$this->api_config["product_qty_". $product['connection_product_id']] = 1;

				}
				$notes .= "<br><br>";
			}
			$this->productPrice[$product['connection_product_id']] += round(($product['price'] * $product['qty']) , 2);
		}

		if(empty($this->api_config["upsellProductIds"])){
			unset($this->api_config["upsellProductIds"]);
			unset($this->api_config["upsellCount"]);
		}

		$this->api_config["notes"] .= !empty($notes) ? $notes : '';
		unset($this->api_config['product_attribute']);

		if(!empty($this->productPrice)){
			foreach ($this->productPrice as $key => $value) {
			$this->api_config["dynamic_product_price_" . $key] = $value;
			$this->api_config["product_qty_" . $key] = 1;
			}
		}

		
	}
	
	/*
	 * preparing a product for shipping
	 */
    private function get_shipping_product($wc_codeclouds_unify_settings)
	{
		if (!empty($wc_codeclouds_unify_settings['shipment_price_settings']) && $wc_codeclouds_unify_settings['shipment_price_settings'] == 1)
		{		
			$ship_product_id = !empty($wc_codeclouds_unify_settings['shipping_product_id']) ? $wc_codeclouds_unify_settings['shipping_product_id'] : NULL;
			if (!empty($ship_product_id))
			{				
				if($this->is_billing_model())
				{					
					$this->api_config['offers'][$ship_product_id]['product_id'] = $ship_product_id;
					$this->api_config['offers'][$ship_product_id]['offer_id'] = $wc_codeclouds_unify_settings['shipping_product_offer_id'];
					$this->api_config['offers'][$ship_product_id]['billing_model_id'] = $wc_codeclouds_unify_settings['shipping_product_billing_id'];
					$this->api_config['offers'][$ship_product_id]['quantity'] = 1;
					$this->api_config['offers'][$ship_product_id]['price'] = !empty(WC()->cart->get_shipping_total()) ? WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() : '0.00';
				}
				else
				{
					$this->api_config['upsellProductIds'] = trim($this->api_config['upsellProductIds'] . ',' . $ship_product_id, ',');
					$this->api_config['upsellCount'] = 1;
					$this->api_config['dynamic_product_price_' . $ship_product_id] = !empty(WC()->cart->get_shipping_total()) ? WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() : '0.00';
					$this->api_config['product_qty_' . $ship_product_id] = 1;
				}				
			}
		}
		
	}

	
	/*
	 * processing the request to CRM 
	 */
	private function process_to_crm()
	{
		try
		{
			$context = array('source' => 'Unify-App');
			if ($this->debug)
			{
				$logger = wc_get_logger();
				$temp_config = $this->api_config;
					$rep_num = substr($temp_config['creditCardNumber'], 6, -4);
					$to_rep_num = '';
					for ($i = strlen($rep_num); $i > 0; $i--)
					{
						$to_rep_num .= '*';
					}
					$temp_config['creditCardNumber'] = substr_replace($temp_config['creditCardNumber'], $to_rep_num, 6, -4);

					$to_rep_cvv = '';
					for ($i = strlen($temp_config['CVV']); $i > 0; $i--)
					{
						$to_rep_cvv .= '*';
					}
					$temp_config['CVV'] = str_replace($temp_config['CVV'], $to_rep_cvv, $temp_config['CVV']);

				$logger->info(('LL Request: ' . json_encode($temp_config, JSON_PRETTY_PRINT)), $context);
			}

			if ($this->is_billing_model())
			{
				$response = json_decode($this->offer_model_payment(), true);
			}
			else
			{
				$response = $this->api_instance->newOrder($this->api_config)->getInArray()['response'];
			}

				foreach ($response as $k => $val)
				{
					$k = lcfirst(str_replace('_', '', ucwords($k, '_')));
					$this->api_response[$k] = $val;
				}
			

				if ($this->debug)
				{
					$logger->info(('LL Response: ' . json_encode($this->api_response, JSON_PRETTY_PRINT)), $context);
				}
		}
		catch (\Exception $ex)
		{

			if ($ex->getCode() == 9999 || $ex->getCode() == 0)
			{
				throw new \Exception($ex->getMessage(), 9999);
			}
			
			throw new \Exception($this->messages['COMMON']['PAYMENT_FAILED']);
		}
	}


	/*
	 * processing the request to CRM 
	 */
	private function process_crm_paypal()
	{
		try
		{
			$context = array('source' => 'Unify-App');
			if ($this->debug)
			{
				$logger = wc_get_logger();
				$temp_config = $this->api_config;
				$logger->info(('LL Request: ' . json_encode($temp_config, JSON_PRETTY_PRINT)), $context);
			}

			if ($this->is_billing_model())
			{
				$response = $this->offer_model_paypal_payment();
			}
			else
			{
				$api_response = $this->api_instance->newOrder($this->api_config)->get()['response'];
				$response = $this->paypal_response_nonbilling($api_response);
			}

			$this->api_response = $response;
		}
		catch (\Exception $ex)
		{

			if ($ex->getCode() == 9999 || $ex->getCode() == 0)
			{
				throw new \Exception($ex->getMessage(), 9999);
			}
			
			throw new \Exception($this->messages['COMMON']['PAYMENT_FAILED']);
		}
	}

	
	/*
	 * For offer model calling API using curl
	 */
	private function offer_model_payment()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => rtrim($this->api_payload['config']['endpoint']) . '/api/v1/new_order',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => json_encode($this->api_config),
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => array(
				"authorization: Basic " . base64_encode($this->api_payload['config']['api_username'] . ":" . $this->api_payload['config']['api_password']),
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);

		return $response;
	}


	/*
	 * For (paypal) offer model calling API using curl
	 */
	private function offer_model_paypal_payment()
	{
		$context = array('source' => 'Unify-App');
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => rtrim($this->api_payload['config']['endpoint']) . '/api/v1/new_order',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POSTFIELDS => json_encode($this->api_config),
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => array(
				"authorization: Basic " . base64_encode($this->api_payload['config']['api_username'] . ":" . $this->api_payload['config']['api_password']),
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$headers = [];
		$output = rtrim($response);
		$data = explode("\n",$output);
		array_shift($data);
		end($data);     
		$key = key($data);
		$error = (!empty($data) && isset($data[$key]))?json_decode($data[$key],true):'';
		if($error!=''){
			if ($this->debug)
			{
				$logger = wc_get_logger();
				$temp_config = $this->api_config;
				$logger->info(('LL Error Response: ' . json_encode($data, JSON_PRETTY_PRINT)), $context);
			}
		}
		foreach($data as $part){
    		$middle = explode(":",$part,2);
    		if ( !isset($middle[1]) ) { $middle[1] = null; }
    		$headers[trim($middle[0])] = trim($middle[1]);
		}
		$headerLocation = '';
		if(array_key_exists('location', $headers)){
			$headerLocation = $headers['location'];
		}else if(array_key_exists('Location', $headers)){
			$headerLocation = $headers['Location'];
		}else{
			$headerLocation = 'failure';
		}
		$responseArr = ['result'=>$headerLocation,'messages'=>(!empty($error))?"<ul class='woocommerce-error' role='alert'><li>".$error['error_message']."</li></ul>":''];
		return json_encode($responseArr);
	}
	
	/*
	 * preparing multi shipping cart
	 */
	private function prepare_multi_shipping_cart()
	{
		$newCart = [];
		$cartValue = $this->api_payload['cart_items'];
		unset($this->api_payload['cart_items']);

		foreach ($cartValue as $k => $val)
		{
			$newCart[empty($val['connection_shipping_id']) ? 'default' : $val['connection_shipping_id']][] = $val;
		}
		return $newCart;
	}
	
}
