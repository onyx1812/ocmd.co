<?php
if ( ! defined( 'ABSPATH' ) )
exit;

/**
 * Add Refund Into Quickbooks Online.
 *
 * @since    
 * Last Updated: 2019-01-25
*/

$include_this_function = true;

if($include_this_function){
	if($this->is_connected()){
		$manual = $this->get_array_isset($refund_data,'manual',false);
		$wc_inv_id = (int) $this->get_array_isset($refund_data,'wc_inv_id',0);
		$wc_rfnd_id = (int) $this->get_array_isset($refund_data,'refund_id',0);
		$wc_cus_id = (int) $this->get_array_isset($refund_data,'customer_user',0);

		$wc_inv_num = $this->get_array_isset($refund_data,'wc_inv_num','');
		$ord_id_num = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;

		if($this->if_sync_refund($refund_data)){
			//$this->add_txt_to_log_file('Refund Test');
			$qbo_customer_id = (int) $this->get_array_isset($refund_data,'qbo_customerid',0);
			if(!$this->if_refund_exists($refund_data)){
				
				$Context = $this->Context;
				$realm = $this->realm;

				$RefundReceiptService = new QuickBooks_IPP_Service_RefundReceipt();
				$RefundReceipt = new QuickBooks_IPP_Object_RefundReceipt();

				$wc_inv_date = $this->get_array_isset($refund_data,'wc_inv_date','');
				$wc_inv_date = $this->view_date($wc_inv_date);

				$wc_rfnd_date = $this->get_array_isset($refund_data,'refund_date','');
				$wc_rfnd_date = $this->view_date($wc_rfnd_date);

				$RefundReceipt->setDocNumber($wc_inv_id.'-'.$wc_rfnd_id);
				$RefundReceipt->setCustomerRef("{-$qbo_customer_id}");
				$RefundReceipt->setTxnDate($wc_rfnd_date);

				$_order_currency = $this->get_array_isset($refund_data,'_order_currency','',true);
				$qbo_home_currency = $this->get_qbo_company_setting('h_currency');
				if($_order_currency!='' && $qbo_home_currency!='' && $_order_currency!=$qbo_home_currency){

					$currency_rate_date = $wc_inv_date;
					$currency_rate = $this->get_qbo_cur_rate($_order_currency,$currency_rate_date,$qbo_home_currency);

					$RefundReceipt->setCurrencyRef($_order_currency);
					$RefundReceipt->setExchangeRate($currency_rate);
				}

				$qbo_inv_items = (isset($refund_data['qbo_inv_items']))?$refund_data['qbo_inv_items']:array();

				$_refund_amount = $this->get_array_isset($refund_data,'_refund_amount',0);
				$_order_total = $this->get_array_isset($refund_data,'_order_total',0);
				$is_partial = false;

				if($_order_total!=$_refund_amount){
					$is_partial = true;
				}

				//$is_partial = true;
				$rd_dtls = $this->get_wc_order_details_from_order($wc_rfnd_id,get_post($wc_rfnd_id));
				$rd_qii = array();$skp_rli_wp_ids = array();$skp_rli_wv_ids = array();$r_shp_dtls = array();$r_tx_dtls = array();
				if($is_partial && $wc_rfnd_id >0){					
					$rd_qii = (isset($rd_dtls['qbo_inv_items']))?$rd_dtls['qbo_inv_items']:array();
					
					$r_shp_dtls = (isset($rd_dtls['shipping_details']))?$rd_dtls['shipping_details']:array();
					$r_tx_dtls = (isset($rd_dtls['tax_details']))?$rd_dtls['tax_details']:array();
					
					/*
					if(is_array($rd_qii) && count($rd_qii)){					
						foreach($rd_qii as $ri){
							$skp_rli_wp_ids[] = $ri['product_id'];
							if(isset($ri['variation_id']) && $ri['variation_id'] > 0){
								$skp_rli_wv_ids[] = $ri['variation_id'];
							}
						}
					}
					
					if(!empty($skp_rli_wp_ids) || !empty($skp_rli_wv_ids)){
						if(is_array($qbo_inv_items) && count($qbo_inv_items)){						
							foreach($qbo_inv_items as $qi_k => $qbo_item){
								if(!empty($skp_rli_wp_ids) && $qbo_item['product_id'] > 0 && !in_array($qbo_item['product_id'],$skp_rli_wp_ids)){
									unset($qbo_inv_items[$qi_k]);
								}
								
								if(!empty($skp_rli_wv_ids) && $qbo_item['variation_id'] > 0 && !in_array($qbo_item['variation_id'],$skp_rli_wv_ids)){
									unset($qbo_inv_items[$qi_k]);
								}
							}
						}
					}
					*/
					
					$qbo_inv_items = $rd_qii;
					if(empty($qbo_inv_items) && empty($r_shp_dtls)){
						//$qbo_inv_items = (isset($refund_data['qbo_inv_items']))?$refund_data['qbo_inv_items']:array();
					}
					
					//$qbo_inv_items = array_values($qbo_inv_items);
					$refund_data['shipping_details'] = $r_shp_dtls;
					$refund_data['tax_details'] = $r_tx_dtls;
				}
				
				/*Count Total Amounts*/
				$_cart_discount = $this->get_array_isset($refund_data,'_cart_discount',0);
				$_cart_discount_tax = $this->get_array_isset($refund_data,'_cart_discount_tax',0);

				$_order_tax = (float) $this->get_array_isset($refund_data,'_order_tax',0);
				$_order_shipping_tax = (float) $this->get_array_isset($refund_data,'_order_shipping_tax',0);
				$_order_total_tax = ($_order_tax+$_order_shipping_tax);

				$order_shipping_total = $this->get_array_isset($refund_data,'order_shipping_total',0);
				
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
				
				/**/
				if($is_partial && $wc_rfnd_id >0){
					if(!empty($r_shp_dtls)){
						$order_shipping_total = (float) $r_shp_dtls[0]['cost'];
						$order_shipping_total = abs($order_shipping_total);
					}
					
					if(!empty($rd_dtls)){
						$_cart_discount = $this->get_array_isset($rd_dtls,'_cart_discount',0);
						$_cart_discount = abs($_cart_discount);
						$_cart_discount_tax = $this->get_array_isset($rd_dtls,'_cart_discount_tax',0);
						$_cart_discount_tax = abs($_cart_discount_tax);
				
						$_order_tax = (float) $this->get_array_isset($rd_dtls,'_order_tax',0);
						$_order_tax = abs($_order_tax);
						$_order_shipping_tax = (float) $this->get_array_isset($rd_dtls,'_order_shipping_tax',0);
						$_order_shipping_tax = abs($_order_shipping_tax);
						$_order_total_tax = ($_order_tax+$_order_shipping_tax);
						
						$order_shipping_total = $this->get_array_isset($rd_dtls,'order_shipping_total',0);
						$order_shipping_total = abs($order_shipping_total);
					}
				}
				
				//Tax rates
				$qbo_tax_code = '';
				$apply_tax = false;
				$is_tax_applied = false;
				$is_inclusive = false;

				$qbo_tax_code_shipping = '';

				$tax_rate_id = 0;
				$tax_rate_id_2 = 0;

				$tax_details = (isset($refund_data['tax_details']))?$refund_data['tax_details']:array();
				
				$is_avatax_active = false;
				/*
				$wc_avatax_enable_tax_calculation = get_option('wc_avatax_enable_tax_calculation');
				if($this->is_plugin_active('woocommerce-avatax') && $this->option_checked('mw_wc_qbo_sync_wc_avatax_support') && $wc_avatax_enable_tax_calculation=='yes'){
				  $is_avatax_active = true;
				  $qbo_is_sales_tax = false;
				}
				*/
				
				//New - Tax Condition
				/* || $is_automated_sales_tax*/
				if($qbo_is_sales_tax){
					if(count($tax_details)){
						$tax_rate_id = $tax_details[0]['rate_id'];
					}
					if(count($tax_details)>1){
						if(abs($tax_details[1]['tax_amount'])>0){
							$tax_rate_id_2 = $tax_details[1]['rate_id'];
						}
					}
					
					if(count($tax_details)>1 && $qbo_is_shipping_allowed){
						foreach($tax_details as $td){
						  if(abs($td['tax_amount'])==0 && abs($td['shipping_tax_amount'])>0){
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
					
					if(count($Tax_Code_Details)){
						if($Tax_Code_Details['TaxGroup'] && count($Tax_Code_Details['TaxRateDetail'])>1){
							$is_qbo_dual_tax = true;
						}
					}

					
					$Tax_Rate_Ref = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']:'';
					$TaxPercent = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref);
					$Tax_Name = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef_name']:'';

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

					$_prices_include_tax = $this->get_array_isset($refund_data,'_prices_include_tax','no',true);
					if($qbo_is_sales_tax){
						$tax_type = $this->get_tax_type($_prices_include_tax);
						$is_inclusive = $this->is_tax_inclusive($tax_type);
						$RefundReceipt->setGlobalTaxCalculation($tax_type);
						//$RefundReceipt->setApplyTaxAfterDiscount(true);
					}
				}
				
				/**/
				$order_refund_details = (isset($refund_data['order_refund_details']))?$refund_data['order_refund_details']:array();
				$r_order_tax = $this->get_array_isset($order_refund_details,'_order_tax',0);
				$r_order_shipping_tax = $this->get_array_isset($order_refund_details,'_order_shipping_tax',0);
				/**/
				$r_order_tax = abs($r_order_tax);
				$r_order_shipping_tax = abs($r_order_shipping_tax);
				
				//
				if($is_partial){
					//$apply_tax = false;
					$RefundReceipt->setTotalAmt($_refund_amount);
				}

				$refund_note = $this->get_array_isset($refund_data,'refund_note','',true,4000);
				$RefundReceipt->setPrivateNote($refund_note);
				
				/**/
				$disable_this = false;
				if(!$disable_this && $is_partial && empty($qbo_inv_items) && !empty($r_shp_dtls)){
					$line = new QuickBooks_IPP_Object_Line();
					$line->setDetailType('SalesItemLineDetail');
					
					$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
					$qdp = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
					$salesItemLineDetail->setItemRef($qdp);
					$salesItemLineDetail->setUnitPrice(0);
					//$salesItemLineDetail->setQty(1);
					$line->setAmount(0);
					$line->setDescription('Refund - '.'Shipping');
					
					if($is_automated_sales_tax && $_order_shipping_tax > 0){
						$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
						if($TaxCodeRef!=''){
							$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
						}
					}
					
					$line->addSalesItemLineDetail($salesItemLineDetail);
					$RefundReceipt->addLine($line);					
				}
				
				//Add Refund items
				$first_line_desc = '';
				if(is_array($qbo_inv_items) && count($qbo_inv_items)){
					$first_line_desc = $qbo_inv_items[0]['Description'];
					foreach($qbo_inv_items as $qbo_item){

						$line = new QuickBooks_IPP_Object_Line();
						$line->setDetailType('SalesItemLineDetail');

						$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
						
						$UnitPrice = $qbo_item["UnitPrice"];
						$UnitPrice = abs($UnitPrice);						
						
						if($_cart_discount){
							//$UnitPrice = $this->get_discounted_item_price($_cart_discount,$total_line_subtotal,$UnitPrice);
						}
						
						if($is_partial){
							//$UnitPrice = $_refund_amount;
							//$UnitPrice = $_refund_amount+($r_order_tax)+($r_order_shipping_tax);
							$qbo_item["Qty"] = abs($qbo_item["Qty"]);
							
							if($apply_tax && $TaxPercent > 0){
								/*
								$UnitPrice = round($UnitPrice / (($TaxPercent/100) + 1),2);
								if($is_qbo_dual_tax && $TaxPercent_2 > 0){
									$UnitPrice = round($UnitPrice / (($TaxPercent_2/100) + 1),2);
								}
								*/
								$comb_tp = ($is_qbo_dual_tax && $TaxPercent_2 > 0)?$TaxPercent+$TaxPercent_2:$TaxPercent;
								$UnitPrice = round($UnitPrice / (($comb_tp/100) + 1),2);

								$is_tax_applied = true;
							}

							if($qbo_is_sales_tax){
								if($is_tax_applied){
									$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
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
									$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
									if($TaxCodeRef!=''){
										$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
									}
								}else{
									//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
								}
							}
						}
						
						$Amount = $qbo_item['Qty']*$UnitPrice;
						$line->setDescription('Refund - '.$qbo_item['Description']);
						$tax_class =  $qbo_item["tax_class"];
						
						if(!$is_partial && $qbo_is_sales_tax){
							if($apply_tax && $qbo_item["Taxed"]){
								$is_tax_applied = true;
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;

								if($is_inclusive){
									$TaxInclusiveAmt = (abs($qbo_item['line_total'])+abs($qbo_item['line_tax']));
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
						
						if(!$is_partial && $is_automated_sales_tax){
							if($qbo_item["Taxed"]){
								$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;
								if($TaxCodeRef!=''){
									$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
								}
							}else{
								//$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								//$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
							}
						}
						
						if($qbo_item["qbo_product_type"]=='Group'){
							$qdp = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
							$salesItemLineDetail->setItemRef($qdp);
						}else{
							$salesItemLineDetail->setItemRef($qbo_item["ItemRef"]);
						}

						if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
							if($qbo_item["qbo_product_type"]!='Group'){
								$salesItemLineDetail->setClassRef($qbo_item["ClassRef"]);
							}
						}

						if($this->option_checked('mw_wc_qbo_sync_invoice_date')){
							$salesItemLineDetail->setServiceDate($wc_inv_date);
						}

						$salesItemLineDetail->setUnitPrice($UnitPrice);
						
						if($is_partial && empty($rd_qii)){
							$line->setAmount($UnitPrice);
							$salesItemLineDetail->setQty(1);
						}else{
							$line->setAmount($Amount);
							$salesItemLineDetail->setQty($qbo_item["Qty"]);
						}
						
						$line->addSalesItemLineDetail($salesItemLineDetail);
						$RefundReceipt->addLine($line);
						//if($is_partial){break;}
					}
				}
				
				//pgdf compatibility
				$is_negative_fee_discount_line = false;
				if($this->get_wc_fee_plugin_check()){
					//$dc_gt_fees = (isset($invoice_data['dc_gt_fees']))?$invoice_data['dc_gt_fees']:array();
					$dc_gt_fees = (isset($rd_dtls['dc_gt_fees']))?$rd_dtls['dc_gt_fees']:array();
					
					if(is_array($dc_gt_fees) && count($dc_gt_fees)){
						foreach($dc_gt_fees as $df){
							$UnitPrice = $df['_line_total'];								
							if($UnitPrice<0){
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
							$_line_tax = abs($_line_tax);
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
							$RefundReceipt->addLine($line);
						}
					}
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
							$_line_tax = abs($_line_tax);
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
							$RefundReceipt->addLine($line);
							
						}
					}
				}
				
				/*Add Refund Shipping*/
				$shipping_details  = (isset($refund_data['shipping_details']))?$refund_data['shipping_details']:array();
				
				$shipping_method = '';
				$shipping_method_name = '';

				$shipping_taxes = '';
				if(isset($shipping_details[0])){
				  if($this->get_array_isset($shipping_details[0],'type','')=='shipping'){
					$shipping_method_id = $this->get_array_isset($shipping_details[0],'method_id','');
					if($shipping_method_id!=''){
					  //$shipping_method = substr($shipping_method_id, 0, strpos($shipping_method_id, ":"));
					  $shipping_method = $this->wc_get_sm_data_from_method_id_str($shipping_method_id);			        }
					$shipping_method = ($shipping_method=='')?'no_method_found':$shipping_method;
					$shipping_method_name =  $this->get_array_isset($shipping_details[0],'name','',true,30);
					//Serialized
					$shipping_taxes = $this->get_array_isset($shipping_details[0],'taxes','');
				  }
				}
				
				//$order_shipping_total+=$_order_shipping_tax;
				
				if($shipping_method!='' && (!$is_partial || ($is_partial && !empty($r_shp_dtls)))){
				  if($qbo_is_shipping_allowed){
					$line = new QuickBooks_IPP_Object_Line();
					$line->setDetailType('SalesItemLineDetail');
					$line->setAmount($order_shipping_total);

					$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
					$salesItemLineDetail->setItemRef('SHIPPING_ITEM_ID');

					if($qbo_is_sales_tax){
					  if($_order_shipping_tax > 0){
						$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code;
						if($qbo_tax_code_shipping!=''){
						  $NetAmountTaxable_Shipping = $order_shipping_total;
						  $TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code_shipping;
						}

						$salesItemLineDetail->setTaxCodeRef($TaxCodeRef);
					  }else{
						$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
						$salesItemLineDetail->setTaxCodeRef($zero_rated_tax_code);
					  }
					}
					
					if($is_automated_sales_tax){
						if($_order_shipping_tax > 0){
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
					$RefundReceipt->addLine($line);
				  }else{
					$shipping_product_arr = $this->get_mapped_shipping_product($shipping_method);
					$line = new QuickBooks_IPP_Object_Line();
					$line->setDetailType('SalesItemLineDetail');

					//$order_shipping_total = $this->get_array_isset($refund_data,'order_shipping_total',0);
					
					$line->setAmount($order_shipping_total);

					$shipping_description = ($shipping_method_name!='')?'Shipping ('.$shipping_method_name.')':'Shipping';

					$line->setDescription($shipping_description);

					$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();


					$salesItemLineDetail->setItemRef($shipping_product_arr["ItemRef"]);

					if(isset($shipping_product_arr["ClassRef"]) && $shipping_product_arr["ClassRef"]!=''){
					  $salesItemLineDetail->setClassRef($shipping_product_arr["ClassRef"]);
					}

					$salesItemLineDetail->setUnitPrice($order_shipping_total);


					if($qbo_is_sales_tax){
					  if($_order_shipping_tax > 0){
						//$shipping_tax_code = $this->get_qbo_tax_map_code_from_serl_line_tax_data($shipping_taxes,'shipping');
						//$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$shipping_tax_code;
						$TaxCodeRef = ($qbo_company_country=='US')?'{-TAX}':$qbo_tax_code;

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
						if($_order_shipping_tax > 0){
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
					$RefundReceipt->addLine($line);
				  }

				}
				
				/*Add Refund Coupons*/
				$used_coupons  = (isset($refund_data['used_coupons']))?$refund_data['used_coupons']:array();
				$qbo_is_discount_allowed = $this->get_qbo_company_setting('is_discount_allowed');
				if($this->option_checked('mw_wc_qbo_sync_no_ad_discount_li')){
					$qbo_is_discount_allowed = false;
				}
				$discount_line_item_allowed = false;
				
				if(count($used_coupons) && $discount_line_item_allowed){
				  foreach($used_coupons as $coupon){
					$coupon_name = $coupon['name'];
					$coupon_discount_amount = $coupon['discount_amount'];
					$coupon_discount_amount = -1 * abs($coupon_discount_amount);

					$coupon_discount_amount_tax = $coupon['discount_amount_tax'];

					$coupon_product_arr = $this->get_mapped_coupon_product($coupon_name);
					$line = new QuickBooks_IPP_Object_Line();

					$line->setDetailType('SalesItemLineDetail');
					if($qbo_is_discount_allowed){
					  $line->setAmount(0);
					}else{
					  $line->setAmount($coupon_discount_amount);
					}


					$line->setDescription($coupon_product_arr['Description']);

					$salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
					$salesItemLineDetail->setItemRef($coupon_product_arr['ItemRef']);
					if(isset($coupon_product_arr["ClassRef"]) && $coupon_product_arr["ClassRef"]!=''){
					  $salesItemLineDetail->setClassRef($coupon_product_arr["ClassRef"]);
					}
					if($qbo_is_discount_allowed){
					  //$salesItemLineDetail->setUnitPrice(0);
					}else{
					  $salesItemLineDetail->setUnitPrice($coupon_discount_amount);
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
					$RefundReceipt->addLine($line);
				  }
				}
			
				/**/
				if(!$_cart_discount && $this->is_only_plugin_active('woocommerce-smart-coupons')){
					$_cart_discount = $this->get_wc_ord_smart_coupon_discount_amount($refund_data);
				}
				
				/*Discount Line*/
				if($_cart_discount && $qbo_is_discount_allowed){
				  //$qbo_discount_account = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_discount_account');
				  $qbo_discount_account = (int) $this->get_qbo_company_setting('default_discount_account');
				  
				  $line = new QuickBooks_IPP_Object_Line();
				  $line->setDetailType('DiscountLineDetail');
				  $line->setAmount($_cart_discount);
				  $line->setDescription('Total Discount');

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
				  $RefundReceipt->addLine($line);

				}
				
				//$order_total_tax = floatval($_order_tax) + floatval($_order_shipping_tax);
				//Avatax Line item
				/*
				if($is_avatax_active && count($tax_details) && $_order_tax >0){
				  $line = new QuickBooks_IPP_Object_Line();
				  $line->setDetailType('SalesItemLineDetail');

				  $Qty = 1;
				  $UnitPrice = $order_total_tax;
				  $Amount = $Qty*$UnitPrice;

				  $line->setAmount($Amount);
				  $line->setDescription('AVATAX - QBO Line Item');

				  $salesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
				  $avatax_item = (int) $this->get_option('mw_wc_qbo_sync_wc_avatax_map_qbo_product');
				  if($avatax_item<1){
					$avatax_item = (int) $this->get_option('mw_wc_qbo_sync_default_qbo_item');
				  }

				  $salesItemLineDetail->setItemRef($avatax_item);
				  $salesItemLineDetail->setQty($Qty);
				  $salesItemLineDetail->setUnitPrice($UnitPrice);

				  $line->addSalesItemLineDetail($salesItemLineDetail);
				  $RefundReceipt->addLine($line);
				}
				*/

				$_payment_method = $this->get_array_isset($refund_data,'_payment_method','',true);
				$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);

				$enable_batch = (int) $this->get_array_isset($pm_map_data,'enable_batch',0);
				if($enable_batch){
					$r_acc_id = (int) $this->get_array_isset($pm_map_data,'udf_account_id',0);
				}else{
					$r_acc_id = (int) $this->get_array_isset($pm_map_data,'qbo_account_id',0);
				}
				$RefundReceipt->setDepositToAccountRef("{-$r_acc_id}");

				$qb_p_method_id = (int) $this->get_array_isset($pm_map_data,'qb_p_method_id',0);
				if($qb_p_method_id){
					$RefundReceipt->setPaymentMethodRef("{-$qb_p_method_id}");
				}

				/*Add Refund Tax*/
				
				//AST
				if($is_automated_sales_tax){					
					$TotalTax = 0;
					if($qbo_is_shipping_allowed){
						//$TotalTax = $_order_tax;
						if(!$is_partial || ($is_partial && !empty($r_tx_dtls))){
							$TotalTax = $_order_tax+$_order_shipping_tax;
						}
					}else{
						if(!$is_partial || ($is_partial && !empty($r_tx_dtls))){
							$TotalTax = $_order_tax+$_order_shipping_tax;
						}							
					}
					
					if($TotalTax > 0){
						$TxnTaxDetail = new QuickBooks_IPP_Object_TxnTaxDetail();
						if(!empty($qbo_tax_code)){
							$TxnTaxDetail->setTxnTaxCodeRef($qbo_tax_code);
						}							
						
						$TxnTaxDetail->setTotalTax($TotalTax);						
						$RefundReceipt->addTxnTaxDetail($TxnTaxDetail);
					}												
				}
				
				if($apply_tax && $is_tax_applied && $Tax_Rate_Ref!=''  && $Tax_Name!=''){
					$TxnTaxDetail = new QuickBooks_IPP_Object_TxnTaxDetail();
					$TxnTaxDetail->setTxnTaxCodeRef($qbo_tax_code);
					$TaxLine = new QuickBooks_IPP_Object_TaxLine();
					$TaxLine->setDetailType('TaxLineDetail');

					if($is_qbo_dual_tax && $TaxPercent_2>0){
						$TaxLine_2 = new QuickBooks_IPP_Object_TaxLine();
						$TaxLine_2->setDetailType('TaxLineDetail');

						$TaxLineDetail_2 = new QuickBooks_IPP_Object_TaxLineDetail();
					}

					$TaxLineDetail = new QuickBooks_IPP_Object_TaxLineDetail();

					$TaxLineDetail->setTaxRateRef($Tax_Rate_Ref);
					$TaxLineDetail->setPerCentBased('true');

					//$NetAmountTaxable = 0;
					//$TaxLineDetail->setNetAmountTaxable($NetAmountTaxable);

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

					$SalesTax = new QuickBooks_IPP_Object_SalesTax();
					$SalesTax->setTaxable('true');
					$SalesTax->setSalesTaxCodeId($Tax_Rate_Ref);

					$SalesTax->setSalesTaxCodeName($Tax_Name);

					$RefundReceipt->addSalesTax($SalesTax);

					if($is_qbo_dual_tax && $TaxPercent_2>0){
						$SalesTax_2 = new QuickBooks_IPP_Object_SalesTax();
						$SalesTax_2->setTaxable('true');
						$SalesTax_2->setSalesTaxCodeId($Tax_Rate_Ref_2);

						$SalesTax_2->setSalesTaxCodeName($Tax_Name_2);

						$RefundReceipt->addSalesTax($SalesTax_2);
					}

				  //Shipping Tax Line
				  if($qbo_tax_code_shipping!='' && $Tax_Rate_Ref_Shipping!='' && !$is_partial){
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

					$RefundReceipt->addSalesTax($SalesTax_2);
					*/
				  }

					$RefundReceipt->addTxnTaxDetail($TxnTaxDetail);

				}
				
				/**/
				if($this->option_checked('mw_wc_qbo_sync_qb_ap_tx_aft_discount')){
					$RefundReceipt->setApplyTaxAfterDiscount(true);
				}else{
					//$RefundReceipt->setApplyTaxAfterDiscount('0');
				}
				
				//$this->_p($refund_data);
				//$this->_p($rd_dtls);
				//$this->_p($RefundReceipt);
				//return false;
				
				$log_title = "";
				$log_details = "";
				$log_status = 0;
				
				if ($resp = $RefundReceiptService->add($Context, $realm, $RefundReceipt)){
					$qbo_rfnd_id = $this->qbo_clear_braces($resp);
					$log_title.="Export Refund #$wc_rfnd_id Order #$ord_id_num\n";
					$log_details.="Refund #$wc_rfnd_id has been exported, QuickBooks Refund ID is #$qbo_rfnd_id";
					$log_status = 1;
					$this->save_log($log_title,$log_details,'Refund',$log_status,true);
					$this->add_qbo_item_obj_into_log_file('Refund Add',$refund_data,$RefundReceipt,$this->get_IPP()->lastRequest(),$this->get_IPP()->lastResponse(),true);
					return $qbo_rfnd_id;

				}else{
					$res_err = $RefundReceiptService->lastError($Context);
					$log_title.="Export Refund Error #$wc_rfnd_id Order #$ord_id_num\n";
					$log_details.="Error:$res_err";
					$this->save_log($log_title,$log_details,'Refund',$log_status,true,true);
					$this->add_qbo_item_obj_into_log_file('Refund Add',$refund_data,$RefundReceipt,$this->get_IPP()->lastRequest(),$this->get_IPP()->lastResponse());
					return false;
				}
			}
		}
	}
	return false;
}