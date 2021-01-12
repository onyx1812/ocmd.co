<?php

namespace CodeClouds\Unify\Data_Sources\Handler;

/**
 * Handle Response API.
 * @package CodeClouds\Unify
 */
class Response_Handler extends \CodeClouds\Unify\Abstracts\Order_Abstract
{

	private $debug = false;
	private $prepare_customer_payload = true;
	public $messages = [];

	/**
	 * Connection authentication.
	 * @param array $args Information for API.
	 */
	public function __construct($args)
	{
		$args['pay_source'] = 'CREDITCARD';
		$this->api_payload = $args;

		$this->api_instance = new \CodeClouds\ResponseCRM\API('ApiKey ' . $args['config']['api_username']);

		$this->messages = \CodeClouds\Unify\Service\Helper::getDataFromFile('Messages');
	}

	/*
	 * Overriding the method for preparing customer payload
	 */

	public function set_config($connection, $name)
	{
		if ($this->prepare_customer_payload)
		{
			$this->api_config = \file_get_contents(__DIR__ . '/../../Config/' . strtolower($connection) . '/customer_' . $name . '.config.json');
			$this->prepare_customer_payload = false;
		}
		else
		{
			parent::set_config($connection, $name);
		}
	}

	/**
	 * Call API for payment process.
	 * @return array
	 */
	public function make_order()
	{
		$context = array('source' => 'Unify-App');
		$wc_codeclouds_unify_settings = get_option('woocommerce_codeclouds_unify_settings');

		if (!empty($wc_codeclouds_unify_settings['enable_debugging']) && $wc_codeclouds_unify_settings['enable_debugging'] == 'yes')
		{
			$this->debug = true;
		}

		/*
		 * Preparing payload for customer creation
		 */
		$this->format_data();
		
		/**
		 * Customer creation
		 */
		$this->create_customer();
		
		
		/**
		 * Creating payload for Order Creation
		 */
		$this->format_data();
		$is_new_response_crm = $this->api_payload['config']['is_legacy_response_crm'];
		(!empty($is_new_response_crm) && $is_new_response_crm == 1)? $this->prepare_product_new(): $this->prepare_product();
				
		if ($this->debug)
		{
			$logger = wc_get_logger();
			$temp_config = $this->api_config;
			$rep_num = substr($temp_config['PaymentInformation']['CCNumber'], 6, -4);
			$to_rep_num = '';
			for ($i = strlen($rep_num); $i > 0; $i--)
			{
				$to_rep_num .= '*';
			}
			$temp_config['PaymentInformation']['CCNumber'] = substr_replace($temp_config['PaymentInformation']['CCNumber'], $to_rep_num, 6, -4);

			$to_rep_cvv = '';
			for ($i = strlen($temp_config['PaymentInformation']['CVV']); $i > 0; $i--)
			{
				$to_rep_cvv .= '*';
			}
			$temp_config['PaymentInformation']['CVV'] = str_replace($temp_config['PaymentInformation']['CVV'], $to_rep_cvv, $temp_config['PaymentInformation']['CVV']);

			$logger->info(('Request to Response CRM: ' . json_encode($temp_config, JSON_PRETTY_PRINT)), $context);
		}



		/**
		 * Api calling for Order Creation
		 */
		$this->api_response = $this->api_instance->addSignupTransaction($this->api_config)->getInArray();
		
		if ($this->debug)
		{
			$logger->info(('Response from Response CRM: ' . json_encode($this->api_response, JSON_PRETTY_PRINT)), $context);
		}

		if(empty($this->api_response) || (isset($this->api_response['response']['Transaction']['OrderInfo']['Response']) && $this->api_response['response']['Transaction']['OrderInfo']['Response'] != 1) ){
			throw new \Exception((isset($this->api_response['response']['Transaction']['OrderInfo']['ResponseText']) ? $this->api_response['response']['Transaction']['OrderInfo']['ResponseText'] : $this->messages['COMMON']['PAYMENT_FAILED']), 9999);
		}		
		
		return ['status' => true, 'orderIds' => $this->api_response['response']['Transaction']['OrderInfo']['OrderID'], 'transactionIds' => $this->api_response['response']['Transaction']['OrderInfo']['TransactionID'], 'notes' => []];
	}
	
	/**
	 * Creating customer to process the order
	 * @throws \Exception
	 */
	public function create_customer()
	{
		/**
		 * Calling customer creation API
		 */
		$customer_creation_response = $this->api_instance->addCustomer($this->api_config)->getInArray();
		
		if (empty($customer_creation_response) || (isset($customer_creation_response['response']['Status']) && $customer_creation_response['response']['Status']) || !isset($customer_creation_response['response']['CustomerID']))
		{
			throw new \Exception((isset($customer_creation_response['response']['ErrorMessage']) ? $customer_creation_response['response']['ErrorMessage'] : $this->messages['COMMON']['PAYMENT_FAILED']), 9999);
		}

		$this->api_payload['customer_id'] = $customer_creation_response['response']['CustomerID'];

		/*
		 * Unset the customer api payload after successfully customer creation
		 */
		unset($this->api_config);
	}
	
	/**
	 * Preparing product payload for API
	 */

	public function prepare_product()
	{
		foreach ($this->api_payload['cart_items'] as $key => $item)
		{
				$this->api_config['PaymentInformation']['ProductGroups'][$key] = [
	                'ProductGroupKey' => $item['connection_group_id']
	            ];
				
				$this->api_config['PaymentInformation']['ProductGroups'][$key]['CustomProducts'][] = [
	                    'ProductID' => $item['connection_product_id'],
	                    'Amount'    => $item['price'],
	                    'Quantity'  => $item['qty'],
					];
			
		}
	}


	/**
	 * Preparing product payload for API
	 */
	public function prepare_product_new()
	{
		foreach ($this->api_payload['cart_items'] as $key => $item)
		{
			$this->api_config['Products'][$key] = [
                    'ProductID' => $item['connection_product_id'],
                    'Amount'    => $item['price'],
                    'Quantity'  => $item['qty'],
				];
			}
	}


}
