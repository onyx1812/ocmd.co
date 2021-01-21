<?php
if ( ! defined( 'ABSPATH' ) )
exit;

/**
 * Add Invoice Into Quickbooks Online.
 *
 * @since    1.0.0
 * Last Updated: 2019-01-25
*/

$include_this_function = true;

if($include_this_function){
	if($this->is_connected()){
		//$this->_p($invoice_data);return false;
		if(!$this->lp_chk_osl_allwd()){
			return false;
		}
		
		$wc_inv_id = $this->get_array_isset($invoice_data,'wc_inv_id',0);
		$wc_inv_num = $this->get_array_isset($invoice_data,'wc_inv_num','');
		$wc_cus_id = $this->get_array_isset($invoice_data,'wc_cus_id','');

		$ord_id_num = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;

		//Zero Total Option Check
		$_order_total = $this->get_array_isset($invoice_data,'_order_total',0);
		if($this->option_checked('mw_wc_qbo_sync_null_invoice')){
			if($_order_total==0 || $_order_total<0){
				$this->save_log('Export Order Error #'.$ord_id_num,'Order amount 0 not allowed in setting ','Invoice',2);
				return false;
			}
		}
		
		if($this->if_sync_invoice($wc_inv_id,$wc_cus_id,$wc_inv_num)){
			if(!$this->check_quickbooks_invoice_get_obj($wc_inv_id,$wc_inv_num)){
				$wc_inv_date = $this->get_array_isset($invoice_data,'wc_inv_date','');
				$wc_inv_date = $this->view_date($wc_inv_date);

				$wc_inv_due_date = $this->get_array_isset($invoice_data,'wc_inv_due_date','');
				$wc_inv_due_date = $this->view_date($wc_inv_due_date);

				$qbo_customerid = $this->get_array_isset($invoice_data,'qbo_customerid',0);
				
				/*PM Due Date*/
				$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);
				$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
				
				if($this->wacs_base_cur_enabled()){
					$base_currency = get_woocommerce_currency();
					$payment_method_map_data  = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
				}else{
					$payment_method_map_data  = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
				}
				
				//
				$cf_map_data = array();
				$cfm_iv = array();
				if($this->is_only_plugin_active('myworks-qbo-sync-custom-field-mapping') && $this->check_sh_cfm_hash()){
					$cf_map_data = $this->get_cf_map_data();
					$cfm_iv = $this->get_cf_map_data(true);
				}
				
				$inv_due_date_days = (int) $this->get_array_isset($payment_method_map_data,'inv_due_date_days',0);
				
				if(!empty($wc_inv_date) && $inv_due_date_days > 0){
					$wc_inv_due_date = date('Y-m-d',strtotime($wc_inv_date . "+{$inv_due_date_days} days"));
				}
				
				$Context = $this->Context;
				$realm = $this->realm;

				$invoiceService = new QuickBooks_IPP_Service_Invoice();
				$invoice = new QuickBooks_IPP_Object_Invoice();

				$DocNumber = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;
				
				$is_send_doc_num = true;
				if($this->option_checked('mw_wc_qbo_sync_use_qb_next_ord_num_iowon') && !$this->get_qbo_company_setting('is_custom_txn_num_allowed')){
					$is_send_doc_num = false;
				}
				
				if($is_send_doc_num){
					$invoice->setDocNumber($DocNumber);
				}				
				
				/*
				$invoice->setTxnDate($wc_inv_date);
				$invoice->setDueDate($wc_inv_due_date);
				*/

				$invoice->setCustomerRef($qbo_customerid);

				/*Count Total Amounts*/
				$_cart_discount = $this->get_array_isset($invoice_data,'_cart_discount',0);
				$_cart_discount_tax = $this->get_array_isset($invoice_data,'_cart_discount_tax',0);

				$_order_tax = (float) $this->get_array_isset($invoice_data,'_order_tax',0);
				$_order_shipping_tax = (float) $this->get_array_isset($invoice_data,'_order_shipping_tax',0);
				$_order_total_tax = ($_order_tax+$_order_shipping_tax);
				
				//Shipping Total
				$order_shipping_total = $this->get_array_isset($invoice_data,'order_shipping_total',0);
				//_order_shipping
				
				if($this->wacs_base_cur_enabled()){
					$_cart_discount_base_currency = $this->get_array_isset($invoice_data,'_cart_discount_base_currency',0);
					$_cart_discount_tax_base_currency = $this->get_array_isset($invoice_data,'_cart_discount_tax_base_currency',0);
					
					$_order_tax_base_currency = (float) $this->get_array_isset($invoice_data,'_order_tax_base_currency',0);
					$_order_shipping_tax_base_currency = (float) $this->get_array_isset($invoice_data,'_order_shipping_tax_base_currency',0);
					$_order_total_tax_base_currency = ($_order_tax_base_currency+$_order_shipping_tax_base_currency);
					
					$order_shipping_total_base_currency = $this->get_array_isset($invoice_data,'_order_shipping_base_currency',0);
					
					$line_subtotal_base_currency = 0;
				}
				
				$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();

				$total_line_subtotal = 0;					
				
				$qbo_date = ''; $is_line_item_date = false;
				if(is_array($qbo_inv_items) && count($qbo_inv_items)){
					foreach($qbo_inv_items as $qbo_item){
						$total_line_subtotal+=$qbo_item['line_subtotal'];
						if($this->wacs_base_cur_enabled()){
							$line_subtotal_base_currency+=$qbo_item['line_subtotal_base_currency'];
						}
						if(empty($qbo_date) && isset($qbo_item['Date_QF'])){
							$qbo_date = $qbo_item['Date_QF'];
							$is_line_item_date = true;
						}
					}
				}
				
				if($is_line_item_date){
					$invoice->setTxnDate($qbo_date);
					$invoice->setDueDate($qbo_date);
				}else{
					$invoice->setTxnDate($wc_inv_date);
					$invoice->setDueDate($wc_inv_due_date);
				}
				
				//Booking Due Date
				$booking_due_date = '';
				$booking_start_date = '';
				if($this->is_plugin_active('woocommerce-deposits','woocommmerce-deposits') && $this->option_checked('mw_wc_qbo_sync_enable_wc_deposit')){
					$wc_booking_dtls = $this->get_wc_booking_dtls($wc_inv_id);
					if(is_array($wc_booking_dtls) && count($wc_booking_dtls)){							
						$_booking_end = $this->get_array_isset($wc_booking_dtls,'_booking_end','');
						$booking_start_date = $this->get_array_isset($wc_booking_dtls,'_booking_start','');
						if($booking_start_date!=''){
							$booking_start_date = date('Y-m-d',strtotime($booking_start_date));
						}							
						if($_booking_end!=''){
							$booking_due_date = date('Y-m-d',strtotime($_booking_end . "-1 days"));
							$invoice->setDueDate($booking_due_date);
						}							
					}						
				}
				
				//Qbo settings
				$qbo_is_sales_tax = $this->get_qbo_company_setting('is_sales_tax');
				$qbo_company_country = $this->get_qbo_company_info('country');

				$qbo_is_shipping_allowed = $this->get_qbo_company_setting('is_shipping_allowed');
				if($this->option_checked('mw_wc_qbo_sync_odr_shipping_as_li')){
					$qbo_is_shipping_allowed = false;
				}
				
				$is_automated_sales_tax = $this->get_qbo_company_setting('is_automated_sales_tax');
				if($is_automated_sales_tax){
					$qbo_is_sales_tax = false;
				}
				
				//Tax rates
				$qbo_tax_code = '';
				$apply_tax = false;
				$is_tax_applied = false;
				$is_inclusive = false;

				$qbo_tax_code_shipping = '';

				$tax_rate_id = 0;
				$tax_rate_id_2 = 0;

				$tax_details = (isset($invoice_data['tax_details']))?$invoice_data['tax_details']:array();
				$allow_zero_tax = true;
				
				//Avatax Settings - 19-07-2017
				$is_avatax_active = false;
				
				$wc_avatax_enable_tax_calculation = get_option('wc_avatax_enable_tax_calculation');
				if($this->is_plugin_active('woocommerce-avatax') && $this->option_checked('mw_wc_qbo_sync_wc_avatax_support') && $wc_avatax_enable_tax_calculation=='yes'){
					$is_avatax_active = true;
					$qbo_is_sales_tax = false;
				}				
				
				//Taxify Settings - 18-10-2017					
				$is_taxify_active = false;
				/*
				$wc_taxify_enable_tax_calculation = get_option('wc_taxify_enabled');
				if($this->is_plugin_active('taxify-for-woocommerce','woocommerce-taxify') && $this->option_checked('mw_wc_qbo_sync_wc_taxify_support') && $wc_taxify_enable_tax_calculation=='yes'){
					$is_taxify_active = true;
					$qbo_is_sales_tax = false;
				}
				*/
				
				//
				$is_so_tax_as_li = false;
				if($this->option_checked('mw_wc_qbo_sync_odr_tax_as_li') && !$is_automated_sales_tax){
					$is_so_tax_as_li = true;
					$qbo_is_sales_tax = false;
				}
				
				//New - Tax Condition
				/* || $is_automated_sales_tax*/
				if($qbo_is_sales_tax && is_array($tax_details)){
					if(count($tax_details)){
						$tax_rate_id = $tax_details[0]['rate_id'];
					}
					if(count($tax_details)>1){
						//24-08-2017
						if($tax_details[1]['tax_amount']>0 || $allow_zero_tax){
							$tax_rate_id_2 = $tax_details[1]['rate_id'];
						}
					}
					
					//24-08-2017
					if(count($tax_details)>1 && $qbo_is_shipping_allowed){
						foreach($tax_details as $td){
							if($td['tax_amount']==0 && $td['shipping_tax_amount']>0){
								$qbo_tax_code_shipping = $this->get_qbo_mapped_tax_code($td['rate_id'],0);
								break;
							}
						}
					}

					$qbo_tax_code = $this->get_qbo_mapped_tax_code($tax_rate_id,$tax_rate_id_2);
					if($qbo_tax_code!='' || $qbo_tax_code!='NON'){
						if($qbo_is_sales_tax){
							$apply_tax = true;
						}							
					}

					$Tax_Code_Details = $this->mod_qbo_get_tx_dtls($qbo_tax_code);
					$is_qbo_dual_tax = false;

					if(!empty($Tax_Code_Details)){
						if($Tax_Code_Details['TaxGroup'] && is_array($Tax_Code_Details['TaxRateDetail']) && count($Tax_Code_Details['TaxRateDetail'])>1){
							$is_qbo_dual_tax = true;
						}
					}
					

					$Tax_Rate_Ref = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']:'';
					$TaxPercent = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref);
					$Tax_Name = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef_name']:'';

					//
					$NetAmountTaxable = 0;

					if($is_qbo_dual_tax){
						$Tax_Rate_Ref_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']:'';
						$TaxPercent_2 = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_2);
						$Tax_Name_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef_name']:'';
						$NetAmountTaxable_2 = 0;
					}

					if($qbo_tax_code_shipping!=''){
						$Tax_Code_Details_Shipping = $this->mod_qbo_get_tx_dtls($qbo_tax_code_shipping);
						$Tax_Rate_Ref_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']:'';
						$TaxPercent_Shipping = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_Shipping);
						$Tax_Name_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef_name']:'';
						$NetAmountTaxable_Shipping = 0;
					}

					$_prices_include_tax = $this->get_array_isset($invoice_data,'_prices_include_tax','no',true);
					if($qbo_is_sales_tax){
						$tax_type = $this->get_tax_type($_prices_include_tax);
						$is_inclusive = $this->is_tax_inclusive($tax_type);
						$invoice->setGlobalTaxCalculation($tax_type);
						//$invoice->setApplyTaxAfterDiscount(true);
					}
					
				}
				//$this->_p($qbo_is_sales_tax,true);
				//$this->_p($qbo_tax_code,true);
				
				$is_nc_pr_diff_tax = false;
				if($qbo_is_sales_tax || $is_automated_sales_tax){
					if(empty($qbo_tax_code) && is_array($tax_details) && count($tax_details)>1 && $this->check_wc_is_diff_tax_per_line($tax_details)){
						$is_nc_pr_diff_tax = true;
					}
				}
				
				/*Single S Tax Support - For Now*/
				$s_stax_applied = false;
				if(is_array($tax_details) && count($tax_details) == 1){
					if($tax_details[0]['tax_amount'] == 0 && $tax_details[0]['shipping_tax_amount'] > 0){
						//$qbo_tax_code = '';
						$is_nc_pr_diff_tax = true;
						$s_stax_applied = true;
					}
				}
				
				/**/
				if($is_inclusive && !$this->wacs_base_cur_enabled()){
					$invoice->setTotalAmt($this->trim_after_decimal_place($_order_total,7));
				}
				
				//Bundle Support
				$is_bundle_order = false;
				if($this->is_plugin_active('woocommerce-product-bundles') && $this->option_checked('mw_wc_qbo_sync_compt_wpbs')){
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							if(isset($qbo_item['bundled_items']) && $qbo_item['qbo_product_type'] == 'Group'){
								$is_bundle_order = true;
								$line = new QuickBooks_IPP_Object_Line();
								$line->setDetailType('GroupLineDetail');

								$line->setAmount(0);
								$GroupLineDetail = new QuickBooks_IPP_Object_GroupLineDetail();

								$GroupLineDetail->setGroupItemRef($qbo_item['ItemRef']);
								$GroupLineDetail->setQuantity($qbo_item['Qty']);
								
								if(!$this->option_checked('mw_wc_qbo_sync_skip_os_lid')){
									$line->setDescription($qbo_item['Description']);
								}
								
								$qbo_gp_details = $this->get_qbo_group_product_details($qbo_item['ItemRef']);
								//$this->_p($qbo_gp_details);
								if(is_array($qbo_gp_details) && count($qbo_gp_details) && isset($qbo_gp_details['buldle_items'])){
									if(is_array($qbo_gp_details['buldle_items']) && count($qbo_gp_details['buldle_items'])){
										foreach($qbo_gp_details['buldle_items'] as $qbo_gp_item){
											$gp_line = new QuickBooks_IPP_Object_Line();

											$gp_line->setDetailType('SalesItemLineDetail');
											$UnitPrice = $qbo_gp_item["UnitPrice"];
											//$Amount = $qbo_gp_item['Qty']*$UnitPrice;
											$Amount = ($qbo_gp_item['Qty']*$qbo_item['Qty'])*$UnitPrice;
											$gp_line->setAmount($Amount);
											if(!$this->option_checked('mw_wc_qbo_sync_skip_os_lid')){
												//$gp_line->setDescription($qbo_gp_item['ItemRef_name']);
												$bsi_desc = $this->get_qbo_bundle_sub_item_desc_from_woo($qbo_gp_item["ItemRef"],$qbo_gp_item['ItemRef_name']);
												$gp_line->setDescription($bsi_desc);
											}												
											$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();

											$tax_class =  $qbo_item["tax_class"];

											if($qbo_is_sales_tax){
												if($apply_tax && $qbo_item["Taxed"]){
													$is_tax_applied = true;
													$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;

													if($is_inclusive){
														//$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
														//$salesItemLineDetail->setTaxInclusiveAmt($TaxInclusiveAmt);
													}
													
													if($TaxCodeRef!=''){
														if($is_inclusive){
															$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
															//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}else{
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}														
													}
												}else{
													$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
													$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
												}
											}
											
											if($is_automated_sales_tax){
												if($qbo_item["Taxed"]){
													$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
													if($TaxCodeRef!=''){
														if($is_inclusive){
															$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
															//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}else{
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}														
													}
												}else{
													//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
													//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
												}
											}
											
											$salesItemLineDetail->setItemRef($qbo_gp_item["ItemRef"]);

											$Qty = $qbo_gp_item["Qty"];
											//$Qty = $qbo_item['Qty'];
											
											//$salesItemLineDetail->setQty($Qty);
											$salesItemLineDetail->setQty($Qty*$qbo_item['Qty']);
											$salesItemLineDetail->setUnitPrice($UnitPrice);
											//$salesItemLineDetail->setUnitPrice($UnitPrice*$qbo_item['Qty']);
											
											//
											if(!empty($qbo_item['ClassRef'])){
												$salesItemLineDetail->setClassRef($qbo_item['ClassRef']);
											}
											
											$gp_line->addSalesItemLineDetail($salesItemLineDetail);
											$GroupLineDetail->addLine($gp_line);
										}
									}
									
									$wc_b_price = $qbo_item['UnitPrice'];
									/*
									$wc_b_price = ($this->option_checked('mw_wc_qbo_sync_no_ad_discount_li'))?$qbo_item['line_total']:$qbo_item['line_subtotal'];
									*/
									if(!$wc_b_price){
										$wc_b_price = $this->get_wc_bundle_line_item_up_from_oli($qbo_inv_items,$qbo_item);
									}
									//$wc_b_price = $qbo_item['UnitPrice']*$qbo_item['Qty'];
									$qbo_b_tp = $qbo_gp_details['b_tp'];
									$gp_p_diff = ($wc_b_price-$qbo_b_tp);
									
									if($gp_p_diff!=0){
										$b_q_ap = (int) $this->get_option('mw_wc_qbo_sync_compt_wpbs_ap_item');
										$gp_line = new QuickBooks_IPP_Object_Line();
										$gp_line->setDetailType('SalesItemLineDetail');
										
										//$UnitPrice = $gp_p_diff;
										$UnitPrice = $gp_p_diff*$qbo_item['Qty'];
										$Qty = 1;
										$Amount = $Qty*$UnitPrice;
										$gp_line->setAmount($Amount);

										$gp_line->setDescription('Bundle Product Price Adjustment');
										$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();

										$tax_class =  $qbo_item["tax_class"];
										
										if($qbo_is_sales_tax){
											if($apply_tax && $qbo_item["Taxed"]){
												$is_tax_applied = true;
												$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;

												if($is_inclusive){
													//$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
													//$salesItemLineDetail->setTaxInclusiveAmt($TaxInclusiveAmt);
												}

												if($TaxCodeRef!=''){
													if($is_inclusive){
														$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
														//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}else{
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}														
												}
											}else{
												$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
												$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
											}
										}
										
										if($is_automated_sales_tax){
											if($qbo_item["Taxed"]){
												$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
												if($TaxCodeRef!=''){
													if($is_inclusive){
														$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
														//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}else{
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}														
												}
											}else{
												//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
												//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
											}
										}
										
										$salesItemLineDetail->setItemRef($b_q_ap);

										$salesItemLineDetail->setQty($Qty);
										$salesItemLineDetail->setUnitPrice($UnitPrice);
										
										//
										if(!empty($qbo_item['ClassRef'])){
											$salesItemLineDetail->setClassRef($qbo_item['ClassRef']);
										}
										
										$gp_line->addSalesItemLineDetail($salesItemLineDetail);
										$GroupLineDetail->addLine($gp_line);
									}
								}

								$line->addGroupLineDetail($GroupLineDetail);
								$invoice->addLine($line);
							}
						}

						/*
						if($is_bundle_order){
							$bundle_arr = array();
							foreach($qbo_inv_items as $qbo_item){
								if(isset($qbo_item['bundled_items']) && $qbo_item['qbo_product_type'] == 'Group'){
									$bundled_item_ref = (int) $qbo_item['ItemRef'];
									$bundled_price = $qbo_item['UnitPrice'];
									$bundled_items = $qbo_item['bundled_items'];
									$bundled_items = unserialize($bundled_items);

									$stamp = $qbo_item['stamp'];
									$stamp = unserialize($stamp);

									$bundle_cart_key = $qbo_item['bundle_cart_key'];
									if(is_array($bundled_items) && count($bundled_items) && is_array($stamp) && count($stamp)){
										foreach($qbo_inv_items as $qbo_item){
											if(isset($qbo_item['bundled_item_id']) && isset($qbo_item['bundled_by']) && isset($qbo_item['bundle_cart_key'])){
												if(in_array($qbo_item['bundle_cart_key'],$bundled_items) && isset($stamp[$qbo_item['bundled_item_id']])){
													if($stamp[$qbo_item['bundled_item_id']]['product_id']==$qbo_item['product_id']){
														if($bundle_cart_key == $qbo_item['bundled_by']){
															//
														}
													}
												}
											}
										}
									}
								}
							}
						}
						*/
					}
				}
				
				//07-06-2017
				//Map Bundle Support
				$map_bundle_support = false;
				$allow_map_bundle_always = true;
				if(!$is_bundle_order || $allow_map_bundle_always){
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							/**/
							if(isset($qbo_item['bundled_items']) || isset($qbo_item['bundled_item_id']) || isset($qbo_item['bundled_by'])){
								continue;
							}
							
							if($qbo_item['qbo_product_type'] == 'Group'){
								$map_bundle_support = true;
								$line = new QuickBooks_IPP_Object_Line();
								$line->setDetailType('GroupLineDetail');

								$line->setAmount(0);
								$GroupLineDetail = new QuickBooks_IPP_Object_GroupLineDetail();

								$GroupLineDetail->setGroupItemRef($qbo_item['ItemRef']);
								$GroupLineDetail->setQuantity($qbo_item['Qty']);
								
								if(!$this->option_checked('mw_wc_qbo_sync_skip_os_lid')){
									$line->setDescription($qbo_item['Description']);
								}
								
								$qbo_gp_details = $this->get_qbo_group_product_details($qbo_item['ItemRef']);
								if(is_array($qbo_gp_details) && count($qbo_gp_details) && isset($qbo_gp_details['buldle_items'])){
									if(is_array($qbo_gp_details['buldle_items']) && count($qbo_gp_details['buldle_items'])){
										foreach($qbo_gp_details['buldle_items'] as $qbo_gp_item){
											$gp_line = new QuickBooks_IPP_Object_Line();

											$gp_line->setDetailType('SalesItemLineDetail');
											$UnitPrice = $qbo_gp_item["UnitPrice"];
											//$Amount = $qbo_gp_item['Qty']*$UnitPrice;
											$Amount = ($qbo_gp_item['Qty']*$qbo_item['Qty'])*$UnitPrice;
											$gp_line->setAmount($Amount);
											
											if(!$this->option_checked('mw_wc_qbo_sync_skip_os_lid')){												
												//$gp_line->setDescription($qbo_gp_item['ItemRef_name']);
												$bsi_desc = $this->get_qbo_bundle_sub_item_desc_from_woo($qbo_gp_item["ItemRef"],$qbo_gp_item['ItemRef_name']);
												$gp_line->setDescription($bsi_desc);
											}
											
											$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();

											$tax_class =  $qbo_item["tax_class"];

											if($qbo_is_sales_tax){
												if($apply_tax && $qbo_item["Taxed"]){
													$is_tax_applied = true;
													$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;

													if($is_inclusive){
														//$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
														//$salesItemLineDetail->setTaxInclusiveAmt($TaxInclusiveAmt);
													}

													if($TaxCodeRef!=''){
														if($is_inclusive){
															$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
															//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}else{
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}														
													}
												}else{
													$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
													$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
												}
											}
											
											if($is_automated_sales_tax){
												if($qbo_item["Taxed"]){
													$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
													if($TaxCodeRef!=''){
														if($is_inclusive){
															$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
															//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}else{
															$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
														}														
													}
												}else{
													//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
													//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
												}
											}
											
											$salesItemLineDetail->setItemRef($qbo_gp_item["ItemRef"]);

											$Qty = $qbo_gp_item["Qty"];
											//$Qty = $qbo_item['Qty'];
											
											//$salesItemLineDetail->setQty($Qty);
											$salesItemLineDetail->setQty($Qty*$qbo_item['Qty']);
											$salesItemLineDetail->setUnitPrice($UnitPrice);
											//$salesItemLineDetail->setUnitPrice($UnitPrice*$qbo_item['Qty']);
											
											//
											if(!empty($qbo_item['ClassRef'])){
												$salesItemLineDetail->setClassRef($qbo_item['ClassRef']);
											}
											
											$gp_line->addSalesItemLineDetail($salesItemLineDetail);
											$GroupLineDetail->addLine($gp_line);
										}
									}

									$wc_b_price = $qbo_item['UnitPrice'];
									//$wc_b_price = $qbo_item['UnitPrice']*$qbo_item['Qty'];
									//$wc_b_price = $qbo_item['line_total'];
									$wc_b_price = ($this->option_checked('mw_wc_qbo_sync_no_ad_discount_li'))?$qbo_item['line_total']:$qbo_item['line_subtotal'];
									$qbo_b_tp = $qbo_gp_details['b_tp'];
									$qbo_b_tp = $qbo_b_tp*$qbo_item['Qty'];
									$gp_p_diff = ($wc_b_price-$qbo_b_tp);
									
									$allow_bndl_line_adstmnt = true;
									if($gp_p_diff!=0 && $allow_bndl_line_adstmnt){
										$b_q_ap = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
										$gp_line = new QuickBooks_IPP_Object_Line();
										$gp_line->setDetailType('SalesItemLineDetail');
										
										$UnitPrice = $gp_p_diff;
										//$UnitPrice = $gp_p_diff*$qbo_item['Qty'];
										$Qty = 1;
										$Amount = $Qty*$UnitPrice;
										$gp_line->setAmount($Amount);

										$gp_line->setDescription('Bundle Product Price Adjustment');
										$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();

										$tax_class =  $qbo_item["tax_class"];

										if($qbo_is_sales_tax){
											if($apply_tax && $qbo_item["Taxed"]){
												$is_tax_applied = true;
												$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;

												if($is_inclusive){
													//$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
													//$salesItemLineDetail->setTaxInclusiveAmt($TaxInclusiveAmt);
												}

												if($TaxCodeRef!=''){
													if($is_inclusive){
														$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
														//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}else{
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}														
												}
											}else{
												$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
												$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
											}
										}
										
										if($is_automated_sales_tax){
											if($qbo_item["Taxed"]){
												$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
												if($TaxCodeRef!=''){
													if($is_inclusive){
														$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
														//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}else{
														$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
													}														
												}
											}else{
												//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
												//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
											}
										}
										
										$salesItemLineDetail->setItemRef($b_q_ap);

										$salesItemLineDetail->setQty($Qty);
										$salesItemLineDetail->setUnitPrice($UnitPrice);
										
										//
										if(!empty($qbo_item['ClassRef'])){
											$salesItemLineDetail->setClassRef($qbo_item['ClassRef']);
										}
										
										$gp_line->addSalesItemLineDetail($salesItemLineDetail);
										$GroupLineDetail->addLine($gp_line);
									}
								}

								$line->addGroupLineDetail($GroupLineDetail);
								$invoice->addLine($line);
							}
						}
					}
				}
				
				//Add Invoice items
				$first_line_desc = '';
				if(is_array($qbo_inv_items) && count($qbo_inv_items)){
					$first_line_desc = $qbo_inv_items[0]['Description'];
					foreach($qbo_inv_items as $qi_k => $qbo_item){
						//Bundle Support
						/*
						if($is_bundle_order){
							if(isset($qbo_item['bundled_items']) || isset($qbo_item['bundled_item_id']) || isset($qbo_item['bundle_cart_key'])){
								continue;
							}
						}
						*/
						
						if($is_bundle_order){
							if(isset($qbo_item['bundled_items']) && $qbo_item['qbo_product_type'] == 'Group'){
								continue;
							}
							
							if(isset($qbo_item['bundled_item_id']) && isset($qbo_item['bundled_by']) && !empty($qbo_item['bundled_by'])){
								$b_parent_qi = $this->get_woo_child_bundle_line_item_parent_line($qbo_inv_items,$qbo_item['bundled_by']);
								if(!empty($b_parent_qi) && $b_parent_qi['qbo_product_type'] == 'Group'){
									continue;
								}
							}
						}
						
						if($map_bundle_support && $qbo_item['qbo_product_type'] == 'Group'){
							continue;
						}

						$line = new QuickBooks_IPP_Object_Line();
						$line->setDetailType('SalesItemLineDetail');
						
						$Description = $qbo_item['Description'];
						$UnitPrice = $qbo_item["UnitPrice"];
						$line_total = $qbo_item["line_total"];
						$line_subtotal = $qbo_item["line_subtotal"];
						
						if($this->wacs_base_cur_enabled()){
							$UnitPrice = $qbo_item["UnitPrice_base_currency"];
							$Description.= " ({$_order_currency} ".$qbo_item["UnitPrice"].")";
							$line_total = $qbo_item["line_total_base_currency"];
							$line_subtotal = $qbo_item["line_subtotal_base_currency"];
						}
						
						if($_cart_discount){
							//$UnitPrice = $this->get_discounted_item_price($_cart_discount,$total_line_subtotal,$UnitPrice);
						}

						//$Amount = $qbo_item['Qty']*$UnitPrice;
						$Amount = ($this->option_checked('mw_wc_qbo_sync_no_ad_discount_li'))?$line_total:$line_subtotal;
						
						//24-10-21017 - Wc Deposit Plugin Support
						if($this->is_plugin_active('woocommerce-deposits','woocommmerce-deposits') && $this->option_checked('mw_wc_qbo_sync_enable_wc_deposit') && isset($qbo_item["deposit_full_amount_ex_tax"])){								
							$UnitPrice = $qbo_item["deposit_full_amount_ex_tax"];
							$qbo_item['Qty'] = 1;
							
							$Amount = $UnitPrice;
						}
						
						$line->setAmount($Amount);
						if(!$this->option_checked('mw_wc_qbo_sync_skip_os_lid')){
							$line->setDescription($Description);
						}							
						
						$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();

						$tax_class =  $qbo_item["tax_class"];
						/**/
						$qbo_tax_code_fli = $qbo_tax_code;
						if($s_stax_applied){
							$qbo_tax_code_fli = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
						}
						
						if($is_nc_pr_diff_tax){
							$qbo_tax_code_fli = $this->get_per_line_tax_code_id($qbo_tax_code_fli,$qbo_item,$tax_details,$qi_k,$qbo_inv_items);
						}
						
						/**/
						if($qbo_item["Taxed"] && !$qbo_item['line_tax'] && !$is_nc_pr_diff_tax){
							$qbo_tax_code_fli = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
						}
						
						if($qbo_is_sales_tax){
							if($apply_tax && $qbo_item["Taxed"]){
								$is_tax_applied = true;
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code_fli;

								if($is_inclusive){
									$TaxInclusiveAmt = ($qbo_item['line_subtotal']+$qbo_item['line_subtotal_tax']);
									if($this->option_checked('mw_wc_qbo_sync_no_ad_discount_li')){
										$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
									}
									
									//
									$TaxInclusiveAmt = $this->trim_after_decimal_place($TaxInclusiveAmt,7);
									
									$NetAmountTaxable += $qbo_item['line_total'];
									$salesItemLineDetail->setTaxInclusiveAmt($TaxInclusiveAmt);
								}

								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						if($is_automated_sales_tax){
							if($qbo_item["Taxed"]){
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code_fli;
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						$salesItemLineDetail->setItemRef($qbo_item["ItemRef"]);

						$Qty = $qbo_item["Qty"];
						//$is_set_up = false;
						$is_set_up = true;
						if($this->option_checked('mw_wc_qbo_sync_os_skip_uprice_l_item')){
							$is_set_up = false;
						}
						
						//10-05-2017
						if($this->is_plugin_active('woocommerce-measurement-price-calculator') && isset($qbo_item['measurement_data']) && $this->option_checked('mw_wc_qbo_sync_measurement_qty')){
							$measurement_data = unserialize($qbo_item['measurement_data']);
							if(is_array($measurement_data) && isset($measurement_data['_measurement_needed'])){
								$_measurement_needed = floatval($measurement_data['_measurement_needed']);
								if($_measurement_needed>0){
									$is_set_up = true;
									$UnitPrice = $UnitPrice/$_measurement_needed;
									//$UnitPrice = number_format($UnitPrice, 2);
									$_quantity = (isset($measurement_data['_quantity']))?$measurement_data['_quantity']:1;
									$Qty = $_measurement_needed*$_quantity;
								}
							}
						}

						$salesItemLineDetail->setQty($Qty);
						if($is_set_up){
							$salesItemLineDetail->setUnitPrice($UnitPrice);
						}						
						
						if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
							$salesItemLineDetail->setClassRef($qbo_item["ClassRef"]);
						}


						if($this->option_checked('mw_wc_qbo_sync_invoice_date')){
							$salesItemLineDetail->setServiceDate($wc_inv_date);
						}
						
						if($this->is_plugin_active('woocommerce-deposits','woocommmerce-deposits') && $this->option_checked('mw_wc_qbo_sync_enable_wc_deposit')){
							if($booking_start_date!=''){
								$salesItemLineDetail->setServiceDate($booking_start_date);
							}								
						}
						
						/**/
						$odd_wdk = 'Delivery Date';
						if(isset($invoice_data[$odd_wdk]) && !empty($invoice_data[$odd_wdk])){
							if($this->is_only_plugin_active('order-delivery-date','order_delivery_date') && $this->get_qbo_company_setting('is_service_date_allowed')){
								$odd_cfmk = $this->get_cf_cmd_concat_k('ordd_as_li_sd',$cfm_iv);
								if(isset($cf_map_data[$odd_cfmk]) && $cf_map_data[$odd_cfmk] =='ServiceDate'){									
									$odd_val = $this->get_array_isset($invoice_data,$odd_wdk,'',true);
									$odd_val_f = $this->view_date($odd_val);
									if(!empty($odd_val_f)){
										$salesItemLineDetail->setServiceDate($odd_val_f);
									}
								}
							}
						}					
						
						$line->addSalesItemLineDetail($salesItemLineDetail);
						$invoice->addLine($line);
					}
				}
				
				//pgdf compatibility
				$is_negative_fee_discount_line = false;
				$nfd_amount = 0;
				if($this->get_wc_fee_plugin_check()){
					$dc_gt_fees = (isset($invoice_data['dc_gt_fees']))?$invoice_data['dc_gt_fees']:array();
					if(is_array($dc_gt_fees) && count($dc_gt_fees)){
						foreach($dc_gt_fees as $df){
							//
							$UnitPrice = $df['_line_total'];								
							if($UnitPrice<0){
								$nfd_amount += $UnitPrice;
								$is_negative_fee_discount_line = true;
								continue;
							}
							
							$line = new QuickBooks_IPP_Object_Line();
							$line->setDetailType('SalesItemLineDetail');
							
							$Qty = 1;
							$Amount = $Qty*$UnitPrice;

							$line->setAmount($Amount);
							$line->setDescription($df['name']);

							$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
							$_line_tax = $df['_line_tax'];
							//$df_tax_code = $this->get_qbo_tax_map_code_from_serl_line_tax_data($df['_line_tax_data']);
							if($_line_tax && $qbo_is_sales_tax){
								//$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$df_tax_code;
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}
							if(!$_line_tax && $qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
							
							if($is_automated_sales_tax){
								if($_line_tax){
									$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
									if($TaxCodeRef!=''){
										$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
									}
								}else{
									//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
								}
							}
							
							/**/
							$df_ItemRef = 0;
							if($this->option_checked('mw_wc_qbo_sync_compt_wdotocac_fee_li_ed') && !empty($this->get_option('mw_wc_qbo_sync_compt_dntp_fn_itxt')) && $this->get_option('mw_wc_qbo_sync_compt_dntp_qbo_item') > 0){
								if($df['name'] == $this->get_option('mw_wc_qbo_sync_compt_dntp_fn_itxt')){
									$df_ItemRef = (int) $this->get_option('mw_wc_qbo_sync_compt_dntp_qbo_item');
								}
							}
							
							if(!$df_ItemRef){
								$df_ItemRef = $this->get_wc_fee_qbo_product($df['name'],'',$invoice_data);
							}
							
							$salesItemLineDetail->setItemRef($df_ItemRef);
							$salesItemLineDetail->setQty($Qty);
							$salesItemLineDetail->setUnitPrice($UnitPrice);
							/**/
							$df_ClassRef = $this->get_wc_fee_product_class_ref($df_ItemRef);
							if(!empty($df_ClassRef)){
								$salesItemLineDetail->setClassRef($df_ClassRef);
							}
							
							$line->addSalesItemLineDetail($salesItemLineDetail);
							$invoice->addLine($line);
						}
					}
				}
				
				/**/
				if($this->is_plugin_active('negative-fee-in-deposit-for-myworks-qbo-sync')){
					$nfd_amount = abs($nfd_amount);
					$invoice->setDeposit($nfd_amount);
					$is_negative_fee_discount_line = false;
				}
				
				/*Negative Fee Line Discount*/
				if($is_negative_fee_discount_line){
					$dc_gt_fees = (isset($invoice_data['dc_gt_fees']))?$invoice_data['dc_gt_fees']:array();
					if(is_array($dc_gt_fees) && count($dc_gt_fees)){
						foreach($dc_gt_fees as $df){
							$UnitPrice = $df['_line_total'];								
							if(!$UnitPrice<0){									
								continue;
							}
							$UnitPrice = abs($UnitPrice);
							//$qbo_discount_account = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_discount_account');
							$qbo_discount_account = (int) $this->get_qbo_company_setting('default_discount_account');
							
							$line = new QuickBooks_IPP_Object_Line();
							$line->setDetailType('DiscountLineDetail');
							
							$Qty = 1;
							$Amount = $Qty*$UnitPrice;
							
							$line->setAmount($Amount);
							$line->setDescription($df['name']);
							
							$discountLineDetail = new QuickBooks_IPP_Object_DiscountLineDetail();
							$discountLineDetail->setPercentBased(false);
							$discountLineDetail->setDiscountAccountRef($qbo_discount_account);
							
							$_line_tax = $df['_line_tax'];
							//$df_tax_code = $this->get_qbo_tax_map_code_from_serl_line_tax_data($df['_line_tax_data']);
							if($_line_tax && $qbo_is_sales_tax){
								//$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$df_tax_code;
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
								if($TaxCodeRef!=''){
									$discountLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}
							if(!$_line_tax && $qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$discountLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
							
							if($is_automated_sales_tax){
								if($_line_tax){
									$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
									if($TaxCodeRef!=''){
										$discountLineDetail->setTaxCodeRef($TaxCodeRef);
									}
								}else{
									//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									//$discountLineDetail->setTaxCodeRef($zero_rated_tax_code);
								}
							}
							
							$line->addDiscountLineDetail($discountLineDetail);
							$invoice->addLine($line);
							
						}
					}
				}
				
				/*Add Invoice Shipping*/
				$shipping_details  = (isset($invoice_data['shipping_details']))?$invoice_data['shipping_details']:array();
				$is_shipping_tax_apply_f = false;
				if(!empty($shipping_details) && isset($shipping_details[0]['taxes']) && !empty($shipping_details[0]['taxes'])){
					$s_taxes = $shipping_details[0]['taxes'];
					$s_taxes = @unserialize($s_taxes);
					if(is_array($s_taxes) && !empty($s_taxes) && isset($s_taxes['total']) && is_array($s_taxes['total']) && !empty($s_taxes['total'])){
						$st_t = array_filter($s_taxes['total']);
						if(!empty($st_t)){
							$is_shipping_tax_apply_f = true;
						}
					}
				}
				
				$shipping_method = '';
				$shipping_method_name = '';

				$shipping_taxes = '';
				if(isset($shipping_details[0])){
					if($this->get_array_isset($shipping_details[0],'type','')=='shipping'){
						$shipping_method_id = $this->get_array_isset($shipping_details[0],'method_id','');
						if($shipping_method_id!=''){
							//$shipping_method = substr($shipping_method_id, 0, strpos($shipping_method_id, ":"));
							$shipping_method = $this->wc_get_sm_data_from_method_id_str($shipping_method_id);
						}
						$shipping_method = ($shipping_method=='')?'no_method_found':$shipping_method;

						$shipping_method_name =  $this->get_array_isset($shipping_details[0],'name','',true,30);

						//Serialized
						$shipping_taxes = $this->get_array_isset($shipping_details[0],'taxes','');
						//$shipping_taxes = unserialize($shipping_taxes);
						//$this->_p($shipping_taxes);
					}
				}

				//$order_shipping_total+=$_order_shipping_tax;
				
				if($shipping_method!='' && $order_shipping_total >0){
					if($qbo_is_shipping_allowed){
						$line = new QuickBooks_IPP_Object_Line();
						$line->setDetailType('SalesItemLineDetail');
						if($this->wacs_base_cur_enabled()){
							$line->setAmount($order_shipping_total_base_currency);
						}else{
							$line->setAmount($order_shipping_total);
						}							
						
						$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
						$salesItemLineDetail->setItemRef('SHIPPING_ITEM_ID');
						
						/**/
						$qbo_tax_code_fli = $qbo_tax_code;
						if($is_nc_pr_diff_tax){							
							$qbo_tax_code_fli = $this->get_per_line_tax_code_from_shipping_line($qbo_tax_code_fli,$shipping_details[0]);
						}
						
						if($qbo_is_sales_tax){
							if($_order_shipping_tax > 0 || $is_shipping_tax_apply_f){
								$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code_fli;
								if($qbo_tax_code_shipping!=''){
									if($this->wacs_base_cur_enabled()){
										$NetAmountTaxable_Shipping = $order_shipping_total_base_currency;
									}else{
										$NetAmountTaxable_Shipping = $order_shipping_total;
									}										
									$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code_shipping;
								}
								
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						if($is_automated_sales_tax){
							if($_order_shipping_tax > 0 || $is_shipping_tax_apply_f){
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code_fli;
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						//
						$mw_wc_qbo_sync_inv_sr_txn_qb_class = $this->get_option('mw_wc_qbo_sync_inv_sr_txn_qb_class');
						if(!empty($mw_wc_qbo_sync_inv_sr_txn_qb_class) && $this->get_qbo_company_setting('ClassTrackingPerTxnLine')){
							$salesItemLineDetail->setClassRef($mw_wc_qbo_sync_inv_sr_txn_qb_class);
						}
						
						$line->addSalesItemLineDetail($salesItemLineDetail);
						$invoice->addLine($line);
					}else{
						$shipping_product_arr = $this->get_mapped_shipping_product($shipping_method);
						$line = new QuickBooks_IPP_Object_Line();
						$line->setDetailType('SalesItemLineDetail');

						//16-05-2017
						$order_shipping_total = $this->get_array_isset($invoice_data,'order_shipping_total',0);
						
						$shipping_description = ($shipping_method_name!='')?'Shipping ('.$shipping_method_name.')':'Shipping';
						if($this->wacs_base_cur_enabled()){
							$shipping_description.= " ({$_order_currency} {$order_shipping_total})";
							$line->setAmount($order_shipping_total_base_currency);
						}else{
							$line->setAmount($order_shipping_total);
						}														

						$line->setDescription($shipping_description);

						$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();


						$salesItemLineDetail->setItemRef($shipping_product_arr["ItemRef"]);

						if(isset($shipping_product_arr["ClassRef"]) && $shipping_product_arr["ClassRef"]!=''){
							$salesItemLineDetail->setClassRef($shipping_product_arr["ClassRef"]);
						}
						
						if($this->wacs_base_cur_enabled()){
							$salesItemLineDetail->setUnitPrice($order_shipping_total_base_currency);
						}else{
							$salesItemLineDetail->setUnitPrice($order_shipping_total);
						}
						
						/**/
						$qbo_tax_code_fli = $qbo_tax_code;
						if($is_nc_pr_diff_tax){							
							$qbo_tax_code_fli = $this->get_per_line_tax_code_from_shipping_line($qbo_tax_code_fli,$shipping_details[0]);
						}
						
						if($qbo_is_sales_tax){
							if($_order_shipping_tax > 0 || $is_shipping_tax_apply_f){
								//$shipping_tax_code = $this->get_qbo_tax_map_code_from_serl_line_tax_data($shipping_taxes,'shipping');
								//$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$shipping_tax_code;
								$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code_fli;

								if($qbo_tax_code_shipping!=''){
									//$NetAmountTaxable_Shipping = $order_shipping_total;
									//$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code_shipping;
								}

								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						if($is_automated_sales_tax){
							if($_order_shipping_tax > 0 || $is_shipping_tax_apply_f){
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code_fli;
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						//
						$mw_wc_qbo_sync_inv_sr_txn_qb_class = $this->get_option('mw_wc_qbo_sync_inv_sr_txn_qb_class');
						if(!empty($mw_wc_qbo_sync_inv_sr_txn_qb_class) && $this->get_qbo_company_setting('ClassTrackingPerTxnLine')){
							$salesItemLineDetail->setClassRef($mw_wc_qbo_sync_inv_sr_txn_qb_class);
						}
						
						$line->addSalesItemLineDetail($salesItemLineDetail);
						$invoice->addLine($line);
					}

				}
				
				/**/
				if(!$qbo_is_shipping_allowed && $is_inclusive){
					if($this->wacs_base_cur_enabled()){
						$NetAmountTaxable_Shipping = $order_shipping_total_base_currency;
					}else{
						$NetAmountTaxable_Shipping = $order_shipping_total;
					}
				}
				
				/*Add Invoice Coupons*/
				$used_coupons  = (isset($invoice_data['used_coupons']))?$invoice_data['used_coupons']:array();

				$qbo_is_discount_allowed = $this->get_qbo_company_setting('is_discount_allowed');
				if($this->option_checked('mw_wc_qbo_sync_no_ad_discount_li')){
					$qbo_is_discount_allowed = false;
				}
				//New Condition for API Changes
				$discount_line_item_allowed = false;
				
				if(!empty($used_coupons) && $discount_line_item_allowed){
					foreach($used_coupons as $coupon){
						$coupon_name = $coupon['name'];
						$coupon_discount_amount = $coupon['discount_amount'];
						$coupon_discount_amount = -1 * abs($coupon_discount_amount);

						$coupon_discount_amount_tax = $coupon['discount_amount_tax'];
						
						if($this->wacs_base_cur_enabled()){
							$coupon_discount_amount_base_currency = $this->get_array_isset($coupon,'discount_amount_base_currency',0);
							$coupon_discount_amount_base_currency = -1 * abs($coupon_discount_amount_base_currency);
							
							$coupon_discount_amount_tax_base_currency = $coupon['discount_amount_tax_base_currency'];
						}
						
						$coupon_product_arr = $this->get_mapped_coupon_product($coupon_name);
						$line = new QuickBooks_IPP_Object_Line();
						
						$Description = $coupon_product_arr['Description'];

						$line->setDetailType('SalesItemLineDetail');
						if($qbo_is_discount_allowed){
							$line->setAmount(0);
						}else{
							if($this->wacs_base_cur_enabled()){
								$Description.= " ({$_order_currency} {$coupon_discount_amount})";
								$line->setAmount($coupon_discount_amount_base_currency);
							}else{
								$line->setAmount($coupon_discount_amount);
							}								
						}
						
						$line->setDescription($Description);

						$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
						$salesItemLineDetail->setItemRef($coupon_product_arr['ItemRef']);
						if(isset($coupon_product_arr["ClassRef"]) && $coupon_product_arr["ClassRef"]!=''){
							$salesItemLineDetail->setClassRef($coupon_product_arr["ClassRef"]);
						}
						if($qbo_is_discount_allowed){
							//$salesItemLineDetail->setUnitPrice(0);
						}else{
							if($this->wacs_base_cur_enabled()){
								$salesItemLineDetail->setUnitPrice($coupon_discount_amount_base_currency);
							}else{
								$salesItemLineDetail->setUnitPrice($coupon_discount_amount);
							}								
						}

						if($qbo_is_sales_tax){
							if($coupon_discount_amount_tax > 0 || $is_tax_applied){
								$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code;
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						if($is_automated_sales_tax){
							if($coupon_discount_amount_tax > 0 || $is_tax_applied){
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						$line->addSalesItemLineDetail($salesItemLineDetail);
						$invoice->addLine($line);
					}
				}
				
				$calc_dc_amnt_fli = true;
				/**/
				if(!$_cart_discount && $this->is_only_plugin_active('woocommerce-smart-coupons')){
					$_cart_discount = $this->get_wc_ord_smart_coupon_discount_amount($invoice_data);
					$calc_dc_amnt_fli = false;
				}
				
				/**/
				if($calc_dc_amnt_fli && !$_cart_discount && !empty($used_coupons)){
					$_cart_discount = $this->get_wc_ord_get_discount_amount_from_coupons($invoice_data);
				}
				
				/*Discount Line*/
				if($_cart_discount && $qbo_is_discount_allowed){
					//$qbo_discount_account = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_discount_account');
					$qbo_discount_account = (int) $this->get_qbo_company_setting('default_discount_account');
					
					$line = new QuickBooks_IPP_Object_Line();
					$line->setDetailType('DiscountLineDetail');
					
					$Description = 'Total Discount';
					if($this->wacs_base_cur_enabled()){
						$Description.= " ({$_order_currency} {$_cart_discount})";
						$line->setAmount($_cart_discount_base_currency);
					}else{
						$line->setAmount($_cart_discount);
					}
					
					$line->setDescription($Description);

					$discountLineDetail = new QuickBooks_IPP_Object_DiscountLineDetail();
					$discountLineDetail->setPercentBased(false);
					$discountLineDetail->setDiscountAccountRef($qbo_discount_account);
					if($qbo_is_sales_tax){
						if($is_tax_applied){
							$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code;
							if($TaxCodeRef!=''){
								$discountLineDetail->setTaxCodeRef($TaxCodeRef);
							}
						}else{
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							$discountLineDetail->setTaxCodeRef($zero_rated_tax_code);
						}
					}
					
					if($is_automated_sales_tax){
						if($_cart_discount){
							$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
							if($TaxCodeRef!=''){
								$discountLineDetail->setTaxCodeRef($TaxCodeRef);
							}
						}else{
							//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$discountLineDetail->setTaxCodeRef($zero_rated_tax_code);
						}
					}
					
					$line->addDiscountLineDetail($discountLineDetail);
					$invoice->addLine($line);

				}
				
				/*Txn Fee*/
				$enable_transaction = (int) $this->get_array_isset($payment_method_map_data,'enable_transaction',0);
				//$enable_transaction && 
				if($this->option_checked('mw_wc_qbo_sync_sync_txn_fee_as_ng_li')){
					if($_payment_method == 'stripe'  || strpos($_payment_method, 'paypal') !== false){
						/*
						$wc_currency = get_woocommerce_currency();
						if($wc_currency == $_order_currency){
							
						}
						*/
						
						if($_payment_method == 'stripe'){
							if(isset($invoice_data['Stripe Fee'])){
								$txn_fee_amount = (float) $this->get_array_isset($invoice_data,'Stripe Fee',0);
							}else{
								$txn_fee_amount = (float) $this->get_array_isset($invoice_data,'_stripe_fee',0);
							}				
							
							$txn_fee_desc = 'Stripe Fee';
						}else{
							if(isset($invoice_data['PayPal Transaction Fee'])){
								$txn_fee_amount = (float) $this->get_array_isset($invoice_data,'PayPal Transaction Fee',0);
							}else{
								$txn_fee_amount = (float) $this->get_array_isset($invoice_data,'_paypal_transaction_fee',0);
							}
							
							$txn_fee_desc = 'PayPal Transaction Fee';
						}
						
						if($txn_fee_amount > 0){
							$txn_fee_amount = -1 * abs($txn_fee_amount);
							
							$line = new QuickBooks_IPP_Object_Line();
							$line->setDetailType('SalesItemLineDetail');
							$line->setAmount($txn_fee_amount);
							$line->setDescription($txn_fee_desc);
							$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
							$txn_fee_item_ref = (int) $this->get_option('mw_wc_qbo_sync_txn_fee_li_qbo_item');
							if(!$txn_fee_item_ref){
								$txn_fee_item_ref = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
							}
							
							$salesItemLineDetail->setItemRef($txn_fee_item_ref);
							$salesItemLineDetail->setQty(1);
							//
							$mw_wc_qbo_sync_inv_sr_txn_qb_class = $this->get_option('mw_wc_qbo_sync_inv_sr_txn_qb_class');
							if(!empty($mw_wc_qbo_sync_inv_sr_txn_qb_class) && $this->get_qbo_company_setting('ClassTrackingPerTxnLine')){
								$salesItemLineDetail->setClassRef($mw_wc_qbo_sync_inv_sr_txn_qb_class);
							}
							
							$salesItemLineDetail->setUnitPrice($txn_fee_amount);
							
							if($qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
							
							$line->addSalesItemLineDetail($salesItemLineDetail);
							$invoice->addLine($line);
						}
						
					}
				}
				
				//$order_total_tax = floatval($_order_tax) + floatval($_order_shipping_tax);
				//Avatax Line item - 19-07-2017
				if($is_avatax_active && !empty($tax_details) && $_order_total_tax >0){
					$line = new QuickBooks_IPP_Object_Line();
					$line->setDetailType('SalesItemLineDetail');
					$Description = 'AVATAX - QBO Line Item';

					$Qty = 1;
					if($this->wacs_base_cur_enabled()){
						$Description.= " ({$_order_currency} {$_order_total_tax})";
						$UnitPrice = $_order_total_tax_base_currency;
					}else{
						$UnitPrice = $_order_total_tax;
					}
					
					$Amount = $Qty*$UnitPrice;

					$line->setAmount($Amount);
					$line->setDescription($Description);

					$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
					$avatax_item = (int) $this->get_option('mw_wc_qbo_sync_wc_avatax_map_qbo_product');
					if($avatax_item<1){
						$avatax_item = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
					}

					$salesItemLineDetail->setItemRef($avatax_item);
					$salesItemLineDetail->setQty($Qty);
					$salesItemLineDetail->setUnitPrice($UnitPrice);

					$line->addSalesItemLineDetail($salesItemLineDetail);
					$invoice->addLine($line);
				}
				
				//Taxify Line item - 18-10-2017
				if($is_taxify_active && !empty($tax_details) && $_order_total_tax >0){
					$line = new QuickBooks_IPP_Object_Line();
					$line->setDetailType('SalesItemLineDetail');
					
					$Description = 'Taxify - QBO Line Item';

					$Qty = 1;
					if($this->wacs_base_cur_enabled()){
						$Description.= " ({$_order_currency} {$_order_total_tax})";
						$UnitPrice = $_order_total_tax_base_currency;
					}else{
						$UnitPrice = $_order_total_tax;
					}
					
					$Amount = $Qty*$UnitPrice;

					$line->setAmount($Amount);
					$line->setDescription($Description);

					$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
					$taxify_item = (int) $this->get_option('mw_wc_qbo_sync_wc_taxify_map_qbo_product');
					if($taxify_item<1){
						$taxify_item = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
					}

					$salesItemLineDetail->setItemRef($taxify_item);
					$salesItemLineDetail->setQty($Qty);
					$salesItemLineDetail->setUnitPrice($UnitPrice);

					$line->addSalesItemLineDetail($salesItemLineDetail);
					$invoice->addLine($line);
				}
				
				//Order Tax Line Item
				if($is_so_tax_as_li && !empty($tax_details) && $_order_total_tax >0){
					$line = new QuickBooks_IPP_Object_Line();
					$line->setDetailType('SalesItemLineDetail');

					$Qty = 1;						
					
					$otli_desc = '';
					if(is_array($tax_details) && count($tax_details)){
						if(isset($tax_details[0]['label'])){
							$otli_desc = $tax_details[0]['label'];
						}
						
						if(isset($tax_details[1]) && $tax_details[1]['label']){
							if(!empty($tax_details[1]['label'])){
								$otli_desc = $otli_desc.', '.$tax_details[1]['label'];
							}
						}
					}
					
					if(empty($otli_desc)){
						$otli_desc = 'Woocommerce Order Tax - QBO Line Item';
					}
					
					if($this->wacs_base_cur_enabled()){
						$otli_desc.= " ({$_order_currency} {$_order_total_tax})";
						$UnitPrice = $_order_total_tax_base_currency;
					}else{
						$UnitPrice = $_order_total_tax;
					}
					
					$Amount = $Qty*$UnitPrice;

					$line->setAmount($Amount);
					
					$line->setDescription($otli_desc);

					$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
					$otli_item = (int) $this->get_option('mw_wc_qbo_sync_otli_qbo_product');
					if($otli_item<1){
						$otli_item = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
					}
					
					$salesItemLineDetail->setItemRef($otli_item);
					$salesItemLineDetail->setQty($Qty);
					$salesItemLineDetail->setUnitPrice($UnitPrice);
					
					//
					$mw_wc_qbo_sync_inv_sr_txn_qb_class = $this->get_option('mw_wc_qbo_sync_inv_sr_txn_qb_class');
					if(!empty($mw_wc_qbo_sync_inv_sr_txn_qb_class) && $this->get_qbo_company_setting('ClassTrackingPerTxnLine')){
						$salesItemLineDetail->setClassRef($mw_wc_qbo_sync_inv_sr_txn_qb_class);
					}
					
					$line->addSalesItemLineDetail($salesItemLineDetail);
					$invoice->addLine($line);
				}

				//15-05-2017
				if($this->is_plugin_active('woocommerce-order-delivery') && $this->option_checked('mw_wc_qbo_sync_compt_p_wod')){
					$_delivery_date = $this->get_array_isset($invoice_data,'_delivery_date','',true);
					if($_delivery_date!=''){
						$_delivery_date = $this->view_date($_delivery_date);
						$invoice->setShipDate($_delivery_date);
					}
				}									
				
				//20-04-2017
				$_billing_email = $this->get_array_isset($invoice_data,'_billing_email','',true);
				
				$bill_email_addr = $_billing_email;
				if($this->option_checked('mw_wc_qbo_sync_set_bemail_to_cus_email_addr') && $qbo_customerid > 0){
					$m_cus_email = $this->get_qbo_customer_email_by_id_from_db($qbo_customerid);
					if(!empty($m_cus_email)){
						$bill_email_addr = $m_cus_email;
					}
				}
				
				if(!empty($bill_email_addr)){
					$BillEmail = new QuickBooks_IPP_Object_BillEmail();
					$BillEmail->setAddress($bill_email_addr);
					$invoice->setBillEmail($BillEmail);
				}				
				
				//BillAddr
				$BillAddr = new QuickBooks_IPP_Object_BillAddr();
				$BillAddr->setLine1($this->get_array_isset($invoice_data,'_billing_first_name','',true).' '.$this->get_array_isset($invoice_data,'_billing_last_name','',true));
				
				//01-06-2017
				$is_cf_bf_applied = false;
				$_billing_phone_cfmk = $this->get_cf_cmd_concat_k('wopn_for_ba_sa',$cfm_iv);
				if(isset($cf_map_data[$_billing_phone_cfmk]) && $cf_map_data[$_billing_phone_cfmk]!=''){
					$bp_a = explode(',',$cf_map_data[$_billing_phone_cfmk]);
					if(is_array($bp_a) && in_array('bill_addr',array_map('trim', $bp_a))){
						$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);
						if($_billing_phone!=''){
							$BillAddr->setLine2($_billing_phone);
							$is_cf_bf_applied = true;
						}
					}
				}
				
				if($is_cf_bf_applied){
					$BillAddr->setLine3($this->get_array_isset($invoice_data,'_billing_company','',true));
					$BillAddr->setLine4($this->get_array_isset($invoice_data,'_billing_address_1','',true));
					$BillAddr->setLine5($this->get_array_isset($invoice_data,'_billing_address_2','',true));
				}else{
					$BillAddr->setLine2($this->get_array_isset($invoice_data,'_billing_company','',true));
					$BillAddr->setLine3($this->get_array_isset($invoice_data,'_billing_address_1','',true));
					$BillAddr->setLine4($this->get_array_isset($invoice_data,'_billing_address_2','',true));
				}

				$BillAddr->setCity($this->get_array_isset($invoice_data,'_billing_city','',true));

				$country = $this->get_array_isset($invoice_data,'_billing_country','',true);
				$country = $this->get_country_name_from_code($country);
				/**/
				if($this->is_plugin_active('custom-us-ca-sp-loc-map-for-myworks-qbo-sync') && $this->option_checked('mw_wc_qbo_sync_compt_cucsp_opn_bsa_ed')){
					$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);
					if(!empty($_billing_phone)){
						$country = $_billing_phone;
					}
				}
				
				$BillAddr->setCountry($country);

				$BillAddr->setCountrySubDivisionCode($this->get_array_isset($invoice_data,'_billing_state','',true));
				$BillAddr->setPostalCode($this->get_array_isset($invoice_data,'_billing_postcode','',true));
				$invoice->setBillAddr($BillAddr);

				//ShipAddr
				if($this->get_array_isset($invoice_data,'_shipping_first_name','',true)!=''){
					$ShipAddr = new QuickBooks_IPP_Object_ShipAddr();
					$ShipAddr->setLine1($this->get_array_isset($invoice_data,'_shipping_first_name','',true).' '.$this->get_array_isset($invoice_data,'_shipping_last_name','',true));

					$is_cf_bf_applied = false;
					$_billing_phone_cfmk = $this->get_cf_cmd_concat_k('wopn_for_ba_sa',$cfm_iv);
					if(isset($cf_map_data[$_billing_phone_cfmk]) && $cf_map_data[$_billing_phone_cfmk]!=''){
						$bp_a = explode(',',$cf_map_data[$_billing_phone_cfmk]);
						if(is_array($bp_a) && in_array('ship_addr',array_map('trim', $bp_a))){
							$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);
							if($_billing_phone!=''){
								$ShipAddr->setLine2($_billing_phone);
								$is_cf_bf_applied = true;
							}
						}
					}
					
					if($is_cf_bf_applied){
						$ShipAddr->setLine3($this->get_array_isset($invoice_data,'_shipping_company','',true));
						$ShipAddr->setLine4($this->get_array_isset($invoice_data,'_shipping_address_1','',true));
						$ShipAddr->setLine5($this->get_array_isset($invoice_data,'_shipping_address_2','',true));
					}else{
						$ShipAddr->setLine2($this->get_array_isset($invoice_data,'_shipping_company','',true));
						$ShipAddr->setLine3($this->get_array_isset($invoice_data,'_shipping_address_1','',true));
						$ShipAddr->setLine4($this->get_array_isset($invoice_data,'_shipping_address_2','',true));
					}

					$ShipAddr->setCity($this->get_array_isset($invoice_data,'_shipping_city','',true));

					$country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
					$country = $this->get_country_name_from_code($country);
					/**/
					if($this->is_plugin_active('custom-us-ca-sp-loc-map-for-myworks-qbo-sync') && $this->option_checked('mw_wc_qbo_sync_compt_cucsp_opn_bsa_ed')){
						$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);
						if(!empty($_billing_phone)){
							$country = $_billing_phone;
						}
					}
					
					$ShipAddr->setCountry($country);

					$ShipAddr->setCountrySubDivisionCode($this->get_array_isset($invoice_data,'_shipping_state','',true));
					$ShipAddr->setPostalCode($this->get_array_isset($invoice_data,'_shipping_postcode','',true));
					$invoice->setShipAddr($ShipAddr);
				}

				/*Add  Invoice Note Start*/

				$customer_note = $this->get_array_isset($invoice_data,'customer_note','',true);
				if($customer_note!=''){
					if($this->option_checked('mw_wc_qbo_sync_invoice_notes')){
						//custom field
						$note_cf_id = (int) $this->get_option('mw_wc_qbo_sync_invoice_note_id');
						$note_cf_name = trim($this->get_option('mw_wc_qbo_sync_invoice_note_id'));

						if($note_cf_id && $note_cf_name!=''){
							$Cus_Field = new QuickBooks_IPP_Object_CustomField();
							$Cus_Field->setDefinitionId($note_cf_id);
							$Cus_Field->setName($note_cf_name);

							$Cus_Field->setType('StringType');
							$Cus_Field->setStringValue($customer_note);
							$invoice->addCustomField($Cus_Field);

						}

					}elseif($this->option_checked('mw_wc_qbo_sync_invoice_memo')){
						//29-03-2017
						if(strlen($customer_note) > 4000){
							$customer_note = substr($customer_note,0,4000);
						}
						$invoice->setPrivateNote($customer_note);
					}else{
						if(strlen($customer_note) > 1000){
							$customer_note = substr($customer_note,0,1000);
						}
						$invoice->setCustomerMemo($customer_note);
					}
				}

				if($this->option_checked('mw_wc_qbo_sync_invoice_memo_statement')){
					if(strlen($first_line_desc) > 4000){
						$first_line_desc = substr($first_line_desc,0,4000);
					}
					$invoice->setPrivateNote($first_line_desc);
				}

				/*Add Invoice Note End*/
				
				/*Tracking Num Compatibility*/
				if($this->is_plugin_active('woocommerce-shipment-tracking') && $this->option_checked('mw_wc_qbo_sync_w_shp_track')){
					$_wc_shipment_tracking_items = $this->get_array_isset($invoice_data,'_wc_shipment_tracking_items','',true);
					
					$wf_wc_shipment_source = $this->get_array_isset($invoice_data,'wf_wc_shipment_source','',true);
					$wf_wc_shipment_result = $this->get_array_isset($invoice_data,'wf_wc_shipment_result','',true);
					
					if($_wc_shipment_tracking_items!='' || $wf_wc_shipment_source!=''){
						if($_wc_shipment_tracking_items!=''){
							$wsti_data = $this->wc_get_wst_data($_wc_shipment_tracking_items);
						}else{
							$wsti_data = $this->wc_get_wst_data_pro($wf_wc_shipment_source,$wf_wc_shipment_result);
						}
						if(!empty($wsti_data)){
							$tracking_provider = $this->get_array_isset($wsti_data,'tracking_provider','',true);
							$tracking_number = $this->get_array_isset($wsti_data,'tracking_number','',true);
							$date_shipped = $this->get_array_isset($wsti_data,'date_shipped','',true);
							if($tracking_provider!=''){
								$invoice->setShipMethodRef($tracking_provider);
							}
							$invoice->setTrackingNum($tracking_number);
							$invoice->setShipDate($date_shipped);
						}
					}
				}

				$is_dpt_added = false;
				if($this->is_plugin_active('woocommerce-hear-about-us') && $this->get_qbo_company_setting('TrackDepartments') && $this->option_checked('mw_wc_qbo_sync_compt_wchau_enable')){
					$source = $this->get_array_isset($invoice_data,'source','',true);
					if($source!=''){
						$mdp_id = (int) $this->get_compt_map_dep_item_id($source);
						if($mdp_id){
							$invoice->setDepartmentRef($mdp_id);
							$is_dpt_added = true;
						}
					}
				}
				
				/*Class Support Added Later*/
				$compt_qbo_class_ref = '';
				if($this->option_checked('mw_wc_qbo_sync_compt_np_wurqbld_ed') && ($this->get_qbo_company_setting('TrackDepartments') || $this->get_qbo_company_setting('ClassTrackingPerTxn'))){
					$wc_user_role = '';
					if($wc_cus_id > 0){
						$user_info = get_userdata($wc_cus_id);
						if(isset($user_info->roles) && is_array($user_info->roles)){
							$wc_user_role = $user_info->roles[0];
						}							
					}else{
						$wc_user_role = 'wc_guest_user';
					}
					
					if(!empty($wc_user_role)){
						if($this->get_qbo_company_setting('TrackDepartments')){
							$wurqbld_wur_qbld_map = get_option('mw_wc_qbo_sync_wurqbld_wur_qbld_map');
							if(is_array($wurqbld_wur_qbld_map) && count($wurqbld_wur_qbld_map) && isset($wurqbld_wur_qbld_map[$wc_user_role])){
								$mdp_id = (int) $wurqbld_wur_qbld_map[$wc_user_role];
								if($mdp_id){
									$invoice->setDepartmentRef($mdp_id);
									$is_dpt_added = true;
								}
							}
						}
						
						if($this->get_qbo_company_setting('ClassTrackingPerTxn')){
							$wurqbcls_wur_qbcls_map = get_option('mw_wc_qbo_sync_wurqbcls_wur_qbcls_map');
							if(is_array($wurqbcls_wur_qbcls_map) && count($wurqbcls_wur_qbcls_map) && isset($wurqbcls_wur_qbcls_map[$wc_user_role])){
								$mcls_id = trim($wurqbcls_wur_qbcls_map[$wc_user_role]);
								if($mcls_id!= ''){
									$compt_qbo_class_ref = $mcls_id;									
								}
							}
						}
					}
				}
				
				/**/
				$mw_wc_qbo_sync_inv_sr_txn_qb_department = $this->get_option('mw_wc_qbo_sync_inv_sr_txn_qb_department');
				if(!empty($mw_wc_qbo_sync_inv_sr_txn_qb_department)){
					$invoice->setDepartmentRef($mw_wc_qbo_sync_inv_sr_txn_qb_department);
					$is_dpt_added = true;
				}
				
				$wes_ebay_oth_ord_qb_department = $this->get_wes_eo_qb_loc($invoice_data);
				if(!empty($wes_ebay_oth_ord_qb_department)){
					$invoice->setDepartmentRef($wes_ebay_oth_ord_qb_department);
					$is_dpt_added = true;
				}
				
				/**/
				if($this->get_qbo_company_setting('TrackDepartments') && $this->is_plugin_active('custom-us-ca-sp-loc-map-for-myworks-qbo-sync') && $this->option_checked('mw_wc_qbo_sync_compt_cucsp_qbl_map_ed')){
					$_shipping_country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
					$_shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','',true);
					if(!empty($_shipping_country) && !empty($_shipping_state) && ($_shipping_country == 'US' || $_shipping_country == 'CA')){
						$us_ca_sp_qb_loc_map_key = ($_shipping_country =='US')?'mw_wc_qbo_sync_cucsp_ship_us_st_qb_loc_map':'mw_wc_qbo_sync_cucsp_ship_ca_pv_qb_loc_map';
						$us_ca_sp_qb_loc_map_data = get_option($us_ca_sp_qb_loc_map_key);
						if(is_array($us_ca_sp_qb_loc_map_data) && !empty($us_ca_sp_qb_loc_map_data) && isset($us_ca_sp_qb_loc_map_data[$_shipping_state]) && !empty($us_ca_sp_qb_loc_map_data[$_shipping_state])){
							$mdp_id = (int) $us_ca_sp_qb_loc_map_data[$_shipping_state];
							if($mdp_id){								
								$invoice->setDepartmentRef($mdp_id);
								$is_dpt_added = true;
							}
						}
					}
				}
				
				/*New CF Map Functionality*/
				if(is_array($cf_map_data) && count($cf_map_data)){
					//$wacfm = $this->get_wc_avl_cf_map_fields();
					$qacfm = $this->get_qbo_avl_cf_map_fields();
					
					foreach($cf_map_data as $wcfm_k_id => $wcfm_v){
						$wcfm_k = $this->get_woo_field_fm_cmd($wcfm_k_id,$cfm_iv);
						$wcfm_k = trim($wcfm_k);
						$wcfm_v = trim($wcfm_v);
						
						$is_static_val = false;
						if(strpos($wcfm_k, '{') !== false && strpos($wcfm_k, '}') !== false){
							$is_static_val = true;
						}
						
						if(!$is_static_val){
							$wcfm_ext_data = (isset($cf_map_data[$wcfm_k_id.'_ext_data']))?$cf_map_data[$wcfm_k_id.'_ext_data']:'';
						}else{
							$wcfm_ext_data = '';
						}						
						
						if(!empty($wcfm_v)){
							$wcf_val = '';
							if($is_static_val){
								$wcf_val = $this->get_string_between($wcfm_k,'{','}');
								$wcf_val = trim($wcf_val);
							}else{
								switch ($wcfm_k) {
									case "wc_order_shipping_details":
										if($this->get_array_isset($invoice_data,'_shipping_first_name','',true)!=''){
											$shipping_details = $this->get_shipping_details_from_order_data($invoice_data);
											$wcf_val = $shipping_details;
										}										
										break;
									case "wc_order_shipping_method_name":
										$wcf_val = $shipping_method_name;
										break;
									case "wc_order_phone_number":
										$wcf_val = $this->get_array_isset($invoice_data,'_billing_phone','',true);
										break;								
									case "wc_customer_username_field_val":									
										if($wc_cus_id > 0){
											$wcf_val = get_user_meta($wc_cus_id,'nickname',true);
										}
										break;
									case "wc_order_line_item_meta_1st":
										$wcf_val = (!empty($qbo_inv_items))?$qbo_inv_items[0]['Line_Item_Meta']:'';
										break;
									default:
										if(isset($invoice_data[$wcfm_k])){
											//is_string
											if(!is_array($invoice_data[$wcfm_k]) && !is_object($invoice_data[$wcfm_k])){
												$wcf_val = $this->get_array_isset($invoice_data,$wcfm_k,'',true);
											}										
										}
								}
							}							
							
							if(!empty($wcf_val) && isset($qacfm[$wcfm_v])){
								$wcf_val = $this->cfm_ft_ev_pv($wcf_val,$wcfm_ext_data);
								
								$qbo_cf_arr = $this->get_qbo_company_setting('sf_str_type_custom_field_list');
								switch ($wcfm_v) {
									case "":								
										break;
									
									default:
										try {
											if(is_array($qbo_cf_arr) && count($qbo_cf_arr) && isset($qbo_cf_arr[$wcfm_v])){
												//QBOCF
												if (strpos($wcfm_v, ',') !== false) {
													$wcfm_v_arr = explode(',',$wcfm_v);
													if(is_array($wcfm_v_arr) && count($wcfm_v_arr)==2){
														$cf_s = array_map('trim', $wcfm_v_arr);
														$cf_s_id = (int) $cf_s[0];
														$cf_s_name = $cf_s[1];
														if($cf_s_id && $cf_s_name!=''){
															if(!is_array($wcf_val) && !is_object($wcf_val)){
																$wcf_val = (string) $wcf_val;
																$Cus_Field = new QuickBooks_IPP_Object_CustomField();
																$Cus_Field->setDefinitionId($cf_s_id);
																$Cus_Field->setName($cf_s_name);
																$Cus_Field->setType('StringType');
																
																$Cus_Field->setStringValue($wcf_val);
																$invoice->addCustomField($Cus_Field);
															}																
														}
													}
												}
											}else{
												$qacfm_naf = $this->get_qbo_avl_cf_map_fields(true);
												$ivqf = true;
												if(is_array($qacfm_naf) && count($qacfm_naf) && isset($qacfm_naf[$wcfm_v])){
													$ivqf = false;
												}
												if($ivqf){
													$invoice->{"set".$wcfm_v}($wcf_val);
												}
												
											}
										}catch(Exception $e) {
											$cfm_err = $e->getMessage();
										}
								}
							}								
						}							
					}
				}
				
				/*Add Invoice Currency Start*/
				
				$qbo_home_currency = $this->get_qbo_company_setting('h_currency');
				if($_order_currency!='' && $qbo_home_currency!='' && $_order_currency!=$qbo_home_currency){

					$currency_rate_date = $wc_inv_date;
					$currency_rate = $this->get_qbo_cur_rate($_order_currency,$currency_rate_date,$qbo_home_currency);
					//11-04-2017
					$invoice->setCurrencyRef($_order_currency);
					$invoice->setExchangeRate($currency_rate);
				}

				/*Add Invoice Currency End*/

				$term_id	= (int) $this->get_array_isset($payment_method_map_data,'term_id','',true);
				if($term_id){
					$invoice->setSalesTermRef($term_id);
				}
				
				$inv_sr_txn_class = $this->get_option('mw_wc_qbo_sync_inv_sr_txn_qb_class');
				
				//WCFE CF SalesRep QBO Class Map
				if($this->is_plugin_active('woocommerce-checkout-field-editor') && $this->option_checked('mw_wc_qbo_sync_compt_wcfe_rf_srqcm_ed')){
					$wcfe_cf_rep_qc_map = get_option('mw_wc_qbo_sync_wcfe_cf_rep_qc_map');
					if(is_array($wcfe_cf_rep_qc_map) && count($wcfe_cf_rep_qc_map)){
						$wsr_ky = $this->get_option('mw_wc_qbo_sync_wcfe_srqcm_wfn');
						if(empty($wsr_ky)){
							$wsr_ky = 'sales-rep';
						}
						
						$wcfe_cf_rep = $this->get_array_isset($invoice_data,$wsr_ky,'');
						if(!empty($wcfe_cf_rep)){
							if(isset($wcfe_cf_rep_qc_map[$wcfe_cf_rep]) && !empty($wcfe_cf_rep_qc_map[$wcfe_cf_rep])){
								$inv_sr_txn_class = $wcfe_cf_rep_qc_map[$wcfe_cf_rep];									
							}
						}
					}
				}
				
				if($inv_sr_txn_class!='' && $this->get_qbo_company_setting('ClassTrackingPerTxn')){
					$invoice->setClassRef($inv_sr_txn_class);
				}
				
				/**/
				if($compt_qbo_class_ref!='' && $this->get_qbo_company_setting('ClassTrackingPerTxn')){
					$invoice->setClassRef($compt_qbo_class_ref);
				}
				
				/*Add Invoice Tax*/
				
				//AST
				if($is_automated_sales_tax){
					$TotalTax = 0;
					if($qbo_is_shipping_allowed){
						$TotalTax = $_order_tax;
						//$TotalTax = $_order_tax+$_order_shipping_tax;
					}else{
						$TotalTax = $_order_tax+$_order_shipping_tax;
					}
					
					if($this->wacs_base_cur_enabled()){
						if($qbo_is_shipping_allowed){
							//$TotalTax = $_order_tax_base_currency;
							$TotalTax = $_order_tax_base_currency+$_order_shipping_tax_base_currency;
						}else{
							$TotalTax = $_order_tax_base_currency+$_order_shipping_tax_base_currency;
						}
					}
					
					if($is_avatax_active){
						$TotalTax = 0;
					}
					
					if($TotalTax > 0 || $allow_zero_tax){
						$TxnTaxDetail = new QuickBooks_IPP_Object_TxnTaxDetail();
						if(!empty($qbo_tax_code)){
							$TxnTaxDetail->setTxnTaxCodeRef($qbo_tax_code);
						}						
						
						$TxnTaxDetail->setTotalTax($TotalTax);						
						$invoice->addTxnTaxDetail($TxnTaxDetail);
					}												
				}
				
				if($apply_tax && $is_tax_applied && $Tax_Rate_Ref!=''  && $Tax_Name!=''){
					$TxnTaxDetail = new QuickBooks_IPP_Object_TxnTaxDetail();
					$TxnTaxDetail->setTxnTaxCodeRef($qbo_tax_code);
					$TaxLine = new QuickBooks_IPP_Object_TaxLine();
					$TaxLine->setDetailType('TaxLineDetail');
					
					/**/
					if($is_inclusive){
						$TotalTax = $_order_tax+$_order_shipping_tax;
						$TxnTaxDetail->setTotalTax($TotalTax);
						$TaxLine->setAmount($TotalTax);
					}	
					
					if($is_qbo_dual_tax && $TaxPercent_2>0){
						$TaxLine_2 = new QuickBooks_IPP_Object_TaxLine();
						$TaxLine_2->setDetailType('TaxLineDetail');

						$TaxLineDetail_2 = new QuickBooks_IPP_Object_TaxLineDetail();
					}

					$TaxLineDetail = new QuickBooks_IPP_Object_TaxLineDetail();

					$TaxLineDetail->setTaxRateRef($Tax_Rate_Ref);
					$TaxLineDetail->setPerCentBased('true');

					//$NetAmountTaxable = 0;
					if($is_inclusive){
						//$NetAmountTaxable = round($NetAmountTaxable+$NetAmountTaxable_Shipping,2);
						$NetAmountTaxable = $this->trim_after_decimal_place($NetAmountTaxable+$NetAmountTaxable_Shipping,7);
						$TaxLineDetail->setNetAmountTaxable($NetAmountTaxable);
					}					
					
					$TaxLineDetail->setTaxPercent($TaxPercent);

					$TaxLine->addTaxLineDetail($TaxLineDetail);

					if($is_qbo_dual_tax && $TaxPercent_2>0){
						$TaxLineDetail_2->setTaxRateRef($Tax_Rate_Ref_2);
						$TaxLineDetail_2->setPerCentBased('true');
						$TaxLineDetail_2->setTaxPercent($TaxPercent_2);

						//$NetAmountTaxable_2 = 0;
						//$TaxLineDetail_2->setNetAmountTaxable($NetAmountTaxable_2);

						$TaxLine_2->addTaxLineDetail($TaxLineDetail_2);
					}

					$TxnTaxDetail->addTaxLine($TaxLine);

					if($is_qbo_dual_tax && $TaxPercent_2>0){
						$TxnTaxDetail->addTaxLine($TaxLine_2);
					}
					
					/*
					$SalesTax = new QuickBooks_IPP_Object_SalesTax();
					$SalesTax->setTaxable('true');
					$SalesTax->setSalesTaxCodeId($Tax_Rate_Ref);

					$SalesTax->setSalesTaxCodeName($Tax_Name);

					$invoice->addSalesTax($SalesTax);

					if($is_qbo_dual_tax && $TaxPercent_2>0){
						$SalesTax_2 = new QuickBooks_IPP_Object_SalesTax();
						$SalesTax_2->setTaxable('true');
						$SalesTax_2->setSalesTaxCodeId($Tax_Rate_Ref_2);

						$SalesTax_2->setSalesTaxCodeName($Tax_Name_2);

						$invoice->addSalesTax($SalesTax_2);
					}
					*/
					
					//Shipping Tax Line
					if($qbo_tax_code_shipping!='' && $Tax_Rate_Ref_Shipping!=''){
						$TaxLine_Shipping = new QuickBooks_IPP_Object_TaxLine();
						$TaxLine_Shipping->setDetailType('TaxLineDetail');

						$TaxLineDetail_Shipping = new QuickBooks_IPP_Object_TaxLineDetail();

						$TaxLineDetail_Shipping->setTaxRateRef($Tax_Rate_Ref_Shipping);
						$TaxLineDetail_Shipping->setPerCentBased('true');
						$TaxLineDetail_Shipping->setTaxPercent($TaxPercent_Shipping);

						$TaxLineDetail_Shipping->setNetAmountTaxable($NetAmountTaxable_Shipping);

						$TaxLine_Shipping->addTaxLineDetail($TaxLineDetail_Shipping);

						$TxnTaxDetail->addTaxLine($TaxLine_Shipping);

						/*
						$SalesTax_Shipping = new QuickBooks_IPP_Object_SalesTax();
						$SalesTax_Shipping->setTaxable('true');
						$SalesTax_Shipping->setSalesTaxCodeId($Tax_Rate_Ref_Shipping);

						$SalesTax_Shipping->setSalesTaxCodeName($Tax_Name_Shipping);

						$invoice->addSalesTax($SalesTax_2);
						*/
					}

					$invoice->addTxnTaxDetail($TxnTaxDetail);

				}

				//_transaction_id
				
				/**/
				if($this->get_qbo_company_setting('ETransactionPaymentEnabled')){
					$invoice->setAllowOnlineCreditCardPayment(true);
					$invoice->setAllowOnlineACHPayment(true);
				}

				/**/
				if($this->option_checked('mw_wc_qbo_sync_qb_ap_tx_aft_discount')){
					$invoice->setApplyTaxAfterDiscount(true);
				}else{
					//$invoice->setApplyTaxAfterDiscount('0');
				}
				
				//
				$log_title = "";
				$log_details = "";
				$log_status = 0;
				
				//$this->_p($invoice_data);
				//$this->_p($invoice);
				//die;
				//return false;
				
				if ($resp = $invoiceService->add($Context, $realm, $invoice)){
					$qbo_inv_id = $this->qbo_clear_braces($resp);
					$log_title.="Export Order #$ord_id_num\n";
					$log_details.="Order #$ord_id_num has been exported, QuickBooks Invoice ID is #$qbo_inv_id";
					$log_status = 1;
					$this->set_imp_sync_data(array('ID'=>$wc_inv_id));
					$this->save_log($log_title,$log_details,'Invoice',$log_status,true);
					$this->add_qbo_item_obj_into_log_file('Invoice Add',$invoice_data,$invoice,$this->get_IPP()->lastRequest(),$this->get_IPP()->lastResponse(),true);
					
					/*Order Note Add*/
					$order = new WC_Order( $wc_inv_id );
					$o_note = __('Order synced to QuickBooks Online - MyWorks Sync','mw_wc_qbo_sync');
					$order->add_order_note($o_note);
					
					/*Send Invoice*/
					if($this->option_checked('mw_wc_qbo_sync_send_inv_sr_afsi_qb')){
						//BillEmail
						if(!empty($bill_email_addr)){
							if ($resp_send = $invoiceService->send($Context, $realm, $qbo_inv_id)){
								
							}else{
								$res_send_err = $invoiceService->lastError($Context);
							}
						}
					}
					
					/**/
					if($this->option_checked('mw_wc_qbo_sync_use_qb_next_ord_num_iowon') && !$this->get_qbo_company_setting('is_custom_txn_num_allowed')){
						$qb_inv_data = $invoiceService->query($Context, $realm, "SELECT DocNumber FROM Invoice WHERE Id = '$qbo_inv_id'");
						if($qb_inv_data && !empty($qb_inv_data)){
							$qbo_inv_doc_no = $qb_inv_data[0]->getDocNumber();
							if(!empty($qbo_inv_doc_no)){
								update_post_meta($wc_inv_id,'_mw_qbo_sync_ord_doc_no',$qbo_inv_doc_no);
								$_mw_qbo_sync_ord_details = array();
								$_mw_qbo_sync_ord_details['DocNumber'] = $qbo_inv_doc_no;
								$_mw_qbo_sync_ord_details['Id'] = $qbo_inv_id;
								$_mw_qbo_sync_ord_details['SyncAs'] = 'Invoice';
								update_post_meta($wc_inv_id,'_mw_qbo_sync_ord_details',$_mw_qbo_sync_ord_details);
							}							
						}
					}
					
					return $qbo_inv_id;

				}else{
					$res_err = $invoiceService->lastError($Context);
					$log_title.="Export Order Error #$ord_id_num\n";
					$log_details.="Error:$res_err";
					$this->save_log($log_title,$log_details,'Invoice',$log_status,true,true);
					$this->add_qbo_item_obj_into_log_file('Invoice Add',$invoice_data,$invoice,$this->get_IPP()->lastRequest(),$this->get_IPP()->lastResponse());
					
					/*Order Note Add*/
					$order = new WC_Order( $wc_inv_id );
					$o_note = __('Order attempted sync to QuickBooks but failed. Check MyWorks Sync > Log for more info.','mw_wc_qbo_sync');
					$order->add_order_note($o_note);
					
					return false;
				}
			}
		}
	}
	return false;
}