<?php
if ( ! defined( 'ABSPATH' ) )
exit;

global $wpdb;
global $MSQS_QL;
global $MWQS_OF;

$disable_section = true;

if($MSQS_QL->option_checked('mw_wc_qbo_sync_pause_up_qbo_conection')){
	$MSQS_QL = new MyWorks_WC_QBO_Sync_QBO_Lib(true);	
}

$page_url = 'admin.php?page=myworks-wc-qbo-sync-compt';

if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_save_compt_stng', 'map_wc_qbo_update_compt_stng' ) ) {
	$fp_dps = false;
	//$MSQS_QL->_p($_POST);die;
	
	//WooCommerce Deposits
	
	
	//Visual Products Configurator
	
	
	//WooCommerce EU VAT Number
	
	
	//WooCommerce Subscriptions
	if(isset($_POST['comp_wcsubscriptions'])){
		$mw_wc_qbo_sync_enable_wc_subs_rnord_sync = '';
		if(isset($_POST['mw_wc_qbo_sync_enable_wc_subs_rnord_sync'])){
			$mw_wc_qbo_sync_enable_wc_subs_rnord_sync = 'true';
		}
		update_option('mw_wc_qbo_sync_enable_wc_subs_rnord_sync',$mw_wc_qbo_sync_enable_wc_subs_rnord_sync);
		
		//
		$wc_p_methods = array();
		$available_gateways = WC()->payment_gateways()->payment_gateways;
		if(is_array($available_gateways) && count($available_gateways)){
			foreach($available_gateways as $key=>$value){
				if($value->enabled=='yes'){
					$wc_p_methods[$value->id] = $value->title;
				}		
			}
		}
		update_option('mw_wc_qbo_sync_available_gateways',$wc_p_methods);
	}
	
	//WooCommerce Measurement Price Calculator
	
	
	//WooCommerce AvaTax	
	if(isset($_POST['comp_avatax'])){
		$mw_wc_qbo_sync_wc_avatax_support = '';
		if(isset($_POST['mw_wc_qbo_sync_wc_avatax_support'])){
			$mw_wc_qbo_sync_wc_avatax_support = 'true';
		}
		update_option('mw_wc_qbo_sync_wc_avatax_support',$mw_wc_qbo_sync_wc_avatax_support);
		
		$mw_wc_qbo_sync_wc_avatax_map_qbo_product = '';
		if(isset($_POST['mw_wc_qbo_sync_wc_avatax_map_qbo_product'])){			
			$mw_wc_qbo_sync_wc_avatax_map_qbo_product = (int) $_POST['mw_wc_qbo_sync_wc_avatax_map_qbo_product'];
		}
		update_option('mw_wc_qbo_sync_wc_avatax_map_qbo_product',$mw_wc_qbo_sync_wc_avatax_map_qbo_product);
	}	
	
	//Taxify for WooCommerce
	/*
	if(isset($_POST['comp_taxify'])){
		$mw_wc_qbo_sync_wc_taxify_support = '';
		if(isset($_POST['mw_wc_qbo_sync_wc_taxify_support'])){
			$mw_wc_qbo_sync_wc_taxify_support = 'true';
		}
		update_option('mw_wc_qbo_sync_wc_taxify_support',$mw_wc_qbo_sync_wc_taxify_support);
		
		$mw_wc_qbo_sync_wc_taxify_map_qbo_product = '';
		if(isset($_POST['mw_wc_qbo_sync_wc_taxify_map_qbo_product'])){			
			$mw_wc_qbo_sync_wc_taxify_map_qbo_product = (int) $_POST['mw_wc_qbo_sync_wc_taxify_map_qbo_product'];
		}
		update_option('mw_wc_qbo_sync_wc_taxify_map_qbo_product',$mw_wc_qbo_sync_wc_taxify_map_qbo_product);
	}
	*/
	
	//WooCommerce Shipment Tracking
	
	
	//WooCommerce Cost of Goods
	if(isset($_POST['comp_wcogsf'])){
		$mw_wc_qbo_sync_wcogs_fiels = '';
		if(isset($_POST['mw_wc_qbo_sync_wcogs_fiels'])){
			$mw_wc_qbo_sync_wcogs_fiels = 'true';
		}
		update_option('mw_wc_qbo_sync_wcogs_fiels',$mw_wc_qbo_sync_wcogs_fiels);
	}
	
	//WooCommerce - Payment Gateways Discounts and Fees
	
	
	//WooCommerce Product Bundles
	if(isset($_POST['comp_wpbs'])){
		$mw_wc_qbo_sync_compt_wpbs = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_wpbs'])){
			$mw_wc_qbo_sync_compt_wpbs = 'true';
		}
		update_option('mw_wc_qbo_sync_compt_wpbs',$mw_wc_qbo_sync_compt_wpbs);
		
		$mw_wc_qbo_sync_compt_wpbs_ap_item = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_wpbs_ap_item'])){			
			$mw_wc_qbo_sync_compt_wpbs_ap_item = (int) $_POST['mw_wc_qbo_sync_compt_wpbs_ap_item'];
		}
		update_option('mw_wc_qbo_sync_compt_wpbs_ap_item',$mw_wc_qbo_sync_compt_wpbs_ap_item);
	}
	
	//WooCommerce Order Fee Line Item (NP)
	if(isset($_POST['comp_woflts'])){
		$mw_wc_qbo_sync_compt_np_oli_fee_sync = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_np_oli_fee_sync'])){
			$mw_wc_qbo_sync_compt_np_oli_fee_sync = 'true';
		}
		update_option('mw_wc_qbo_sync_compt_np_oli_fee_sync',$mw_wc_qbo_sync_compt_np_oli_fee_sync);
		
		$mw_wc_qbo_sync_compt_gf_qbo_item = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_gf_qbo_item'])){
			$fp_dps = true;
			$mw_wc_qbo_sync_compt_gf_qbo_item = (int) $_POST['mw_wc_qbo_sync_compt_gf_qbo_item'];
		}
		update_option('mw_wc_qbo_sync_compt_gf_qbo_item',$mw_wc_qbo_sync_compt_gf_qbo_item);
		
		//
		$mw_wc_qbo_sync_compt_np_oli_fee_qb_class = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_np_oli_fee_qb_class'])){
			$mw_wc_qbo_sync_compt_np_oli_fee_qb_class = trim($_POST['mw_wc_qbo_sync_compt_np_oli_fee_qb_class']);
		}
		update_option('mw_wc_qbo_sync_compt_np_oli_fee_qb_class',$mw_wc_qbo_sync_compt_np_oli_fee_qb_class);		
		
	}
	
	//WooCommerce Payment Gateway Based Fees
	
	
	//WooCommerce Donation Or Tip On Cart And Checkout
	
	
	//Woo Add Custom Fee
	
	
	//WooCommerce Conditional Product Fees for Checkout Pro
		
	
	//WooCommerce Custom Fields
	
	
	//WooCommerce Checkout Field Editor Pro
	
	
	//WooCommerce Checkout Field Editor
		
	
	//WooCommerce User Role -> QuickBooks Location Map (NP)
	
	
	//WooCommerce Hear About Us
	
	
	//WooCommerce Admin Custom Order Fields
	
	
	//WooCommerce Order Delivery
	
	
	//WooCommerce Sequential Order Numbers Pro
	if(isset($_POST['comp_wsnop'])){
		$mw_wc_qbo_sync_compt_p_wsnop = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_p_wsnop'])){
			$mw_wc_qbo_sync_compt_p_wsnop = 'true';
		}
		update_option('mw_wc_qbo_sync_compt_p_wsnop',$mw_wc_qbo_sync_compt_p_wsnop);
	}
	
	//Custom Order Numbers for WooCommerce
	if(isset($_POST['comp_confw'])){
		$mw_wc_qbo_sync_compt_p_wsnop = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_p_wsnop'])){
			$mw_wc_qbo_sync_compt_p_wsnop = 'true';
		}
		update_option('mw_wc_qbo_sync_compt_p_wsnop',$mw_wc_qbo_sync_compt_p_wsnop);
	}
	
	if(isset($_POST['comp_wsnop_fb_omk'])){
		$mw_wc_qbo_sync_compt_p_wsnop = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_p_wsnop'])){
			$mw_wc_qbo_sync_compt_p_wsnop = 'true';
		}
		update_option('mw_wc_qbo_sync_compt_p_wsnop',$mw_wc_qbo_sync_compt_p_wsnop);
		
		$mw_wc_qbo_sync_compt_p_wconmkn = '';
		if(isset($_POST['mw_wc_qbo_sync_compt_p_wconmkn'])){
			$mw_wc_qbo_sync_compt_p_wconmkn = trim($_POST['mw_wc_qbo_sync_compt_p_wconmkn']);
			update_option('mw_wc_qbo_sync_compt_p_wconmkn',$mw_wc_qbo_sync_compt_p_wconmkn);
		}
	}
	
	//WooCommerce TM Extra Product Options
	
	
	//WooCommerce Product Add-ons
	

	//WooCommerce Appointments
	
	
	//WooCommerce USER  ==> QuickBooks Online Vendor (NP)
	

	//WooCommerce Product ==> QuickBooks Online Product (NP)
	
	
	//Aelia Currency Switcher for WooCommerce
	
	
	//
	$MSQS_QL->set_session_val('compt_settings_save_msg',__('Compatibility settings saved successfully.','mw_wc_qbo_sync'));
	$MSQS_QL->redirect($page_url);
}

$fee_compt_enable = false;
$fee_li_chk_arr = $MSQS_QL->get_row("SELECT `order_item_id` FROM {$wpdb->prefix}woocommerce_order_items WHERE `order_item_type` = 'fee' AND `order_item_name` != '' AND `order_id` > 0 LIMIT 0, 1");
if(is_array($fee_li_chk_arr) && !empty($fee_li_chk_arr)){
	$fee_compt_enable = true;
}

$option_keys = $MWQS_OF->get_plugin_option_keys();
$admin_settings_data = $MSQS_QL->get_all_options($option_keys);
//$MSQS_QL->_p($admin_settings_data);
$is_compt = false;

//
$is_fee_plugin = false;

$mw_qbo_product_list = '';
if(!$MSQS_QL->option_checked('mw_wc_qbo_sync_select2_ajax')){
	$mw_qbo_product_list = $MSQS_QL->get_product_dropdown_list('');
}


$mw_qbo_class_list = $MSQS_QL->get_class_dropdown_list('',true);


//$mw_qbo_location_list = $MSQS_QL->get_department_dropdown_list('');

$list_selected = '';
if(!$MSQS_QL->option_checked('mw_wc_qbo_sync_select2_ajax')){	
	$list_selected.='jQuery(\'#mw_wc_qbo_sync_compt_gf_qbo_item\').val('.$admin_settings_data['mw_wc_qbo_sync_compt_gf_qbo_item'].');';
	$list_selected.='jQuery(\'#mw_wc_qbo_sync_compt_wpbs_ap_item\').val('.$admin_settings_data['mw_wc_qbo_sync_compt_wpbs_ap_item'].');';
	
	
	$list_selected.='jQuery(\'#mw_wc_qbo_sync_wc_avatax_map_qbo_product\').val('.$admin_settings_data['mw_wc_qbo_sync_wc_avatax_map_qbo_product'].');';
	$list_selected.='jQuery(\'#mw_wc_qbo_sync_wc_taxify_map_qbo_product\').val('.$admin_settings_data['mw_wc_qbo_sync_wc_taxify_map_qbo_product'].');';
	
	$list_selected.='jQuery(\'#mw_wc_qbo_sync_compt_np_oli_fee_qb_class\').val('."'".$admin_settings_data['mw_wc_qbo_sync_compt_np_oli_fee_qb_class']."'".');';
}
?>

<?php
	$wu_roles = get_editable_roles();
?>

<?php if(isset($_GET['debug']) && $_GET['debug'] ==1):?>
	<div style="margin:10px;">
		<h2>Available Addons</h2>
		<div style="background:white;padding:5px;">
			<?php 
				$a_addons = $MSQS_QL->get_compt_plugin_license_addons_arr();
				$MSQS_QL->_p($a_addons);
			?>
		</div>
	</div>
<?php endif;?>

<h2 class="compt_addon_heading">Compatibility Included</h2>

<div class="container map-coupon-code-outer">
	<form method="post" action="<?php echo $page_url;?>">
	<?php wp_nonce_field( 'myworks_wc_qbo_sync_save_compt_stng', 'map_wc_qbo_update_compt_stng' ); ?>
	
		<!--WooCommerce Measurement Price Calculator-->
				
		
		<!--WooCommerce Deposits-->
		
		
		<!--Visual Products Configurator-->
		
		
		<!--WooCommerce EU VAT Number-->
		
		
		<!--WooCommerce Subscriptions-->
		<?php if($MSQS_QL->is_plugin_active('woocommerce-subscriptions','woocommerce-subscriptions')):?>
		<?php $is_compt=true;?>
		<div class="page_title">
			<h4 title="woocommerce-subscriptions"><?php _e( 'WooCommerce Subscriptions', 'mw_wc_qbo_sync' );?></h4>
		</div>		
		<div class="card">
			<div class="card-content">
				<div class="col s12 m12 l12">
				<div class="myworks-wc-qbo-sync-table-responsive">
					<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
					<tr>
						<td colspan="3">
							<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
						</td>
					</tr>
					<tr>
						<td width="60%"><?php _e( 'Enable Renewal Orders Sync', 'mw_wc_qbo_sync' );?> :</td>
						<td width="20%">
							<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_enable_wc_subs_rnord_sync" id="mw_wc_qbo_sync_enable_wc_subs_rnord_sync" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_enable_wc_subs_rnord_sync']=='true') echo 'checked' ?>>
						</td>
						<td width="20%">
							<div class="material-icons tooltipped tooltip">?
								<span class="tooltiptext">
									<?php _e( 'Syncing renewal orders automatically to QB', 'mw_wc_qbo_sync' );?>
								</span>
							</div>
						</td>						
					</tr>
					<tr>
						<td colspan="3">
							<input type="submit" name="comp_wcsubscriptions" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
						</td>
					</tr>
					</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		
		<!--WooCommerce Product Bundles-->
		<?php if($MSQS_QL->is_plugin_active('woocommerce-product-bundles')):?>
		<?php $is_compt=true;?>
			<div class="page_title">
			<h4 title="woocommerce-product-bundles"><?php _e( 'WooCommerce Product Bundles', 'mw_wc_qbo_sync' );?></h4>
			</div>
			
			<div class="card">
				<div class="card-content">
					<div class="col s12 m12 l12">
					<div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td><?php _e( 'Enable bundle product support', 'mw_wc_qbo_sync' );?>:</td>
							<td>
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_compt_wpbs" id="mw_wc_qbo_sync_compt_wpbs" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_compt_wpbs']=='true') echo 'checked' ?>>
							</td>
							<td>
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Enable support for syncing orders that contain bundled products.', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						
						<tr>
							<td><?php _e( 'QuickBooks product used to keep line item total accurate', 'mw_wc_qbo_sync' );?>:</td>
							<td>
								<?php
									$dd_options = '<option value=""></option>';
									$dd_ext_class = '';
									if($MSQS_QL->option_checked('mw_wc_qbo_sync_select2_ajax')){
										$dd_ext_class = 'mwqs_dynamic_select';
										if((int) $admin_settings_data['mw_wc_qbo_sync_compt_wpbs_ap_item']){
											$itemid = (int) $admin_settings_data['mw_wc_qbo_sync_compt_wpbs_ap_item'];
											$qb_item_name = $MSQS_QL->get_field_by_val($wpdb->prefix.'mw_wc_qbo_sync_qbo_items','name','itemid',$itemid);
											if($qb_item_name!=''){
												$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
											}
										}
									}else{
										$dd_options.=$mw_qbo_product_list;
									}
								?>
								<select name="mw_wc_qbo_sync_compt_wpbs_ap_item" id="mw_wc_qbo_sync_compt_wpbs_ap_item" class="filled-in production-option mw_wc_qbo_sync_select <?php echo $dd_ext_class;?>">
									<?php echo $dd_options;?>
								</select>
							</td>
							<td>
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Select a QuickBooks Product that will be inserted as the last line of a bundle in QuickBooks if the WooCommerce line item total for a bundle does not match the QuickBooks bundle total - and this product will be used as an adjustment line item to ensure the line item total is correct.', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>					
						<tr>
							<td colspan="3">
								<input type="submit" name="comp_wpbs" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>	
				</div>
			</div>
		</div>		
		<?php endif;?>
		
		<!--WooCommerce Order Fee Line Item (NP)-->
		<?php if($enable_this=true && $fee_compt_enable):?>
		<?php $is_compt=true;?>
			<div class="page_title">
			<h4 title=""><?php _e( 'WooCommerce Order Fee Line Items', 'mw_wc_qbo_sync' );?></h4>
			</div>			
			
			<div class="card">
				<div class="card-content">
					<div class="col s12 m12 l12">
					<div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td><?php _e( 'Sync fee line items in a WooCommerce Order to QuickBooks', 'mw_wc_qbo_sync' );?>:</td>
							<td>
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_compt_np_oli_fee_sync" id="mw_wc_qbo_sync_compt_np_oli_fee_sync" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_compt_np_oli_fee_sync']=='true') echo 'checked' ?>>
							</td>
							<td>
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Enable/Disable syncing "fee" line items in WooCommerce Orders to QuickBooks line items.', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						
						<tr>
							<td><?php _e( 'QuickBooks product for fee line item', 'mw_wc_qbo_sync' );?>:</td>
							<td>
								<?php
									$dd_options = '<option value=""></option>';
									$dd_ext_class = '';
									if($MSQS_QL->option_checked('mw_wc_qbo_sync_select2_ajax')){
										$dd_ext_class = 'mwqs_dynamic_select';
										if((int) $admin_settings_data['mw_wc_qbo_sync_compt_gf_qbo_item']){
											$itemid = (int) $admin_settings_data['mw_wc_qbo_sync_compt_gf_qbo_item'];
											$qb_item_name = $MSQS_QL->get_field_by_val($wpdb->prefix.'mw_wc_qbo_sync_qbo_items','name','itemid',$itemid);
											if($qb_item_name!=''){
												$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
											}
										}
									}else{
										$dd_options.=$mw_qbo_product_list;
									}
								?>
								<select name="mw_wc_qbo_sync_compt_gf_qbo_item" id="mw_wc_qbo_sync_compt_gf_qbo_item" class="filled-in production-option mw_wc_qbo_sync_select <?php echo $dd_ext_class;?>">
									<?php echo $dd_options;?>
								</select>
							</td>
							<td>
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Choose the QuickBooks product that will be used in QuickBooks to represent "fee" line items in WooCommerce.', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						
						<?php 
						if(!$MSQS_QL->is_plg_lc_p_l() && ($MSQS_QL->get_qbo_company_setting('ClassTrackingPerTxn') || $MSQS_QL->get_qbo_company_setting('ClassTrackingPerTxnLine'))):
						?>
						<tr>
							<td width="60%"><?php _e( 'QuickBooks class for fee line item', 'mw_wc_qbo_sync' );?> :</td>
							<td width="20%">							
								<select name="mw_wc_qbo_sync_compt_np_oli_fee_qb_class" id="mw_wc_qbo_sync_compt_np_oli_fee_qb_class" class="filled-in production-option mw_wc_qbo_sync_select">
								<option value=""></option>
								<?php echo $mw_qbo_class_list;?>
								</select>
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Choose the QuickBooks class that will be used in QuickBooks to represent "fee" line items in WooCommerce.', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						<?php endif;?>
						
						<tr>
							<td colspan="3">
								<input type="submit" name="comp_woflts" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>	
				</div>
			</div>
		</div>
		<?php $is_fee_plugin=true;?>
		<?php endif;?>
		
		<!--WooCommerce - Payment Gateways Discounts and Fees-->
		<!--WooCommerce Gateway Fee Plugin-->
		
		
		<!--WooCommerce Payment Gateway Based Fees-->
		
		
		<!--WooCommerce Donation Or Tip On Cart And Checkout-->
		
		
		<!--Woo Add Custom Fee-->
		
		
		<!--WooCommerce Conditional Product Fees for Checkout Pro-->
		
		
		<!--WooCommerce Order Delivery-->
		
		
		<!--WooCommerce Sequential Order Numbers Pro-->
		<?php $is_cwonp_np_eb = false;?>
		<?php
		/**/
		$is_sequential_plugin_active = false;
		$is_seq_pro = false;
		$seq_p_name = '';
		$seq_p_file = '';
		//woocommerce-sequential-order-numbers
		if($MSQS_QL->is_plugin_active('woocommerce-sequential-order-numbers-pro','',true)){
			$is_sequential_plugin_active = true;
			$is_seq_pro = false;
			
			$seq_p_name = 'WooCommerce Sequential Order Numbers Pro';
			$seq_p_file = 'woocommerce-sequential-order-numbers-pro';
		}
		
		if($MSQS_QL->is_only_plugin_active('woocommerce-sequential-order-numbers')){
			$is_sequential_plugin_active = true;
			
			$seq_p_name = 'WooCommerce Sequential Order Numbers';
			$seq_p_file = 'woocommerce-sequential-order-numbers';
		}
		
		?>
		<?php if($is_sequential_plugin_active):?>
		<?php $is_compt=true;?>
		<?php $is_cwonp_np_eb = true;?>
		<div class="page_title">
			<h4 title="<?php echo $seq_p_file;?>"><?php _e( $seq_p_name, 'mw_wc_qbo_sync' );?></h4>
		</div>		
			<div class="card">
				<div class="card-content">
					<div class="col s12 m12 l12">
					<div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td width="60%"><?php _e( 'Enable '.$seq_p_name.' Support', 'mw_wc_qbo_sync' );?>:</td>
							<td width="20%">
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_compt_p_wsnop" id="mw_wc_qbo_sync_compt_p_wsnop" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_compt_p_wsnop']=='true') echo 'checked' ?>>
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'When enabled, orders will sync into QuickBooks using the "pretty" order number created by '.$seq_p_name.' - instead of the WooCommerce Order ID.', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						<tr>
							<td colspan="3">
								<input type="submit" name="comp_wsnop" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php else:?>
		<!--Custom Order Number (NP)-->
		<?php
			$np_custom_order_number = true;
		?>		
		<?php if($np_custom_order_number && $MSQS_QL->chk_compt_addons_active('np_custom_order_number','np_custom_order_number')):?>
		<?php $is_compt=true;?>
		<?php $is_cwonp_np_eb = true;?>
		<div class="page_title">
			<h4 title="Custom Order Number"><?php _e( 'Custom Order Number', 'mw_wc_qbo_sync' );?></h4>
		</div>		
		<div class="card">
			<div class="card-content">
				<div class="col s12 m12 l12">
					<div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td width="60%"><?php _e( 'Enable Custom Order Number Support ', 'mw_wc_qbo_sync' );?> :</td>
							<td width="20%">
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_compt_p_wsnop" id="mw_wc_qbo_sync_compt_p_wsnop" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_compt_p_wsnop']=='true') echo 'checked' ?>>
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Custom Order Number - Filter Hook (woocommerce_order_number)', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						<tr>
							<td colspan="3">							
								<input type="submit" name="comp_wsnop" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		
		<?php endif;?>
		
		<!--Custom Order Numbers for WooCommerce-->
		<?php if($MSQS_QL->is_plugin_active('custom-order-numbers-for-woocommerce')):?>
		<?php $is_compt=true;?>
		<?php $is_cwonp_np_eb = true;?>
		<div class="page_title">
			<h4 title="woocommerce-sequential-order-numbers-pro"><?php _e( 'Custom Order Numbers for WooCommerce', 'mw_wc_qbo_sync' );?></h4>
		</div>		
			<div class="card">
				<div class="card-content">
					<div class="col s12 m12 l12">
					<div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td width="60%"><?php _e( 'Enable Custom Order Number ', 'mw_wc_qbo_sync' );?> :</td>
							<td width="20%">
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_compt_p_wsnop" id="mw_wc_qbo_sync_compt_p_wsnop" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_compt_p_wsnop']=='true') echo 'checked' ?>>
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Custom Order Numbers for WooCommerce', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						<tr>
							<td colspan="3">
								<input type="submit" name="comp_confw" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		
		<!--Custom Order Number Field (NP)-->			
		<?php if(!$is_cwonp_np_eb):?>
		<?php $is_compt=true;?>		
		<div class="page_title">
			<h4 title="Custom Order Number"><?php _e( 'Custom Order Number Field', 'mw_wc_qbo_sync' );?></h4>
		</div>		
		<div class="card">
			<div class="card-content">
				<div class="col s12 m12 l12">
				    <div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td width="60%"><?php _e( 'Enable Custom Order Number Field Support ', 'mw_wc_qbo_sync' );?> :</td>
							<td width="20%">
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_compt_p_wsnop" id="mw_wc_qbo_sync_compt_p_wsnop" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_compt_p_wsnop']=='true') echo 'checked' ?>>
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Custom Order Number Field', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						
						<tr>
							<td width="60%"><?php _e( 'Custom Order Number Field Meta Key Name ', 'mw_wc_qbo_sync' );?> :</td>
							<td width="20%">
								<input type="text" class="filled-in production-option" name="mw_wc_qbo_sync_compt_p_wconmkn" id="mw_wc_qbo_sync_compt_p_wconmkn" value="<?php echo $admin_settings_data['mw_wc_qbo_sync_compt_p_wconmkn'];?>">
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Custom Order Number Field Meta Key', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						
						<tr>
							<td colspan="3">							
								<input type="submit" name="comp_wsnop_fb_omk" id="comp_wsnop_fb_omk" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		
		
		<!--WooCommerce Custom Fields-->
		
		
		
		<!--WooCommerce Checkout Field Editor Pro-->
		
		
		<!--WooCommerce User Role -> QuickBooks Location Map (NP)-->
		
		
		<!--WooCommerce Checkout Field Editor-->
		
		
		
		<!--WooCommerce Hear About Us-->
		
		
		
		<!--WooCommerce Admin Custom Order Fields-->
		
		
		<!--WooCommerce Shipment Tracking-->		
		
		
		<!--WooCommerce Cost of Goods-->
		<?php if($MSQS_QL->is_plugin_active('woocommerce-cost-of-goods')):?>
		<?php $is_compt=true;?>
		<div class="page_title">
			<h4 title="woocommerce-cost-of-goods"><?php _e( 'WooCommerce Cost of Goods', 'mw_wc_qbo_sync' );?></h4>
		</div>		
			<div class="card">
				<div class="card-content">
					<div class="col s12 m12 l12">
					<div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td width="60%"><?php _e( 'Enable Cost of Goods Support', 'mw_wc_qbo_sync' );?>:</td>
							<td width="20%">
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_wcogs_fiels" id="mw_wc_qbo_sync_wcogs_fiels" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_wcogs_fiels']=='true') echo 'checked' ?>>
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'Enable syncing the COGS for a WooCommerce product into QuickBooks Online.', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						<tr>
							<td colspan="3">
								<input type="submit" name="comp_wcogsf" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		
		<!--WooCommerce AvaTax-->
		<?php if($MSQS_QL->is_plugin_active('woocommerce-avatax')):?>
		<?php $is_compt=true;?>
		<div class="page_title">
			<h4 title="woocommerce-avatax"><?php _e( 'WooCommerce AvaTax', 'mw_wc_qbo_sync' );?></h4>
		</div>		
		<div class="card">
			<div class="card-content">
				<div class="col s12 m12 l12">
					<div class="myworks-wc-qbo-sync-table-responsive">
						<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
						<tr>
							<td colspan="3">
								<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						<tr>
							<td width="60%"><?php _e( 'Enable AvaTax Support', 'mw_wc_qbo_sync' );?> :</td>
							<td width="20%">
								<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_wc_avatax_support" id="mw_wc_qbo_sync_wc_avatax_support" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_wc_avatax_support']=='true') echo 'checked' ?>>
							</td>
							<td width="20%">
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'WooCommerce AvaTax', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>						
						</tr>
						<?php 
							//$wc_avatax_specific_tax_locations = get_option('wc_avatax_specific_tax_locations');
							//$qbo_tax_options = '<option value=""></option>';
							//$qbo_tax_options.=$MSQS_QL->get_tax_code_dropdown_list();
						?>
						<tr>
							<td colspan="3">
								<b><?php _e( 'Mapping', 'mw_wc_qbo_sync' );?></b>
							</td>
						</tr>
						
						<tr>
							<td>AVATAX</td>
							
							<td>
								<?php
									$dd_options = '<option value=""></option>';
									$dd_ext_class = '';
									if($MSQS_QL->option_checked('mw_wc_qbo_sync_select2_ajax')){
										$dd_ext_class = 'mwqs_dynamic_select';
										if((int) $admin_settings_data['mw_wc_qbo_sync_wc_avatax_map_qbo_product']){
											$itemid = (int) $admin_settings_data['mw_wc_qbo_sync_wc_avatax_map_qbo_product'];
											$qb_item_name = $MSQS_QL->get_field_by_val($wpdb->prefix.'mw_wc_qbo_sync_qbo_items','name','itemid',$itemid);
											if($qb_item_name!=''){
												$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
											}
										}
									}else{
										$dd_options.=$mw_qbo_product_list;
									}
								?>
								<select name="mw_wc_qbo_sync_wc_avatax_map_qbo_product" id="mw_wc_qbo_sync_wc_avatax_map_qbo_product" class="filled-in production-option mw_wc_qbo_sync_select <?php echo $dd_ext_class;?>">
									<?php echo $dd_options;?>
								</select>
							</td>
							
							<td>
								<div class="material-icons tooltipped tooltip">?
									<span class="tooltiptext">
										<?php _e( 'QBO Product', 'mw_wc_qbo_sync' );?>
									</span>
								</div>
							</td>
						</tr>
						
						<tr>
							<td colspan="3">
								<input type="submit" name="comp_avatax" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		
		<!--Taxify for WooCommerce-->
		<?php if(!$disable_section && $MSQS_QL->is_plugin_active('taxify-for-woocommerce','woocommerce-taxify')):?>
		<?php $is_compt=true;?>
		<div class="page_title">
			<h4 title="taxify-for-woocommerce"><?php _e( 'Taxify for WooCommerce', 'mw_wc_qbo_sync' );?></h4>
		</div>		
		<div class="card">
			<div class="card-content">
				<div class="col s12 m12 l12">
					<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">
					<tr>
						<td colspan="3">
							<b><?php _e( 'Settings', 'mw_wc_qbo_sync' );?></b>
						</td>
					</tr>
					<tr>
						<td width="60%"><?php _e( 'Enable Taxify Support', 'mw_wc_qbo_sync' );?> :</td>
						<td width="20%">
							<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_sync_wc_taxify_support" id="mw_wc_qbo_sync_wc_taxify_support" value="true" <?php if($admin_settings_data['mw_wc_qbo_sync_wc_taxify_support']=='true') echo 'checked' ?>>
						</td>
						<td width="20%">
							<div class="material-icons tooltipped tooltip">?
								<span class="tooltiptext">
									<?php _e( 'Taxify for WooCommerce', 'mw_wc_qbo_sync' );?>
								</span>
							</div>
						</td>						
					</tr>
					<?php 
						/**/
					?>
					<tr>
						<td colspan="3">
							<b><?php _e( 'Mapping', 'mw_wc_qbo_sync' );?></b>
						</td>
					</tr>
					
					<tr>
						<td>Taxify</td>
						
						<td>
							<?php
								$dd_options = '<option value=""></option>';
								$dd_ext_class = '';
								if($MSQS_QL->option_checked('mw_wc_qbo_sync_select2_ajax')){
									$dd_ext_class = 'mwqs_dynamic_select';
									if((int) $admin_settings_data['mw_wc_qbo_sync_wc_taxify_map_qbo_product']){
										$itemid = (int) $admin_settings_data['mw_wc_qbo_sync_wc_taxify_map_qbo_product'];
										$qb_item_name = $MSQS_QL->get_field_by_val($wpdb->prefix.'mw_wc_qbo_sync_qbo_items','name','itemid',$itemid);
										if($qb_item_name!=''){
											$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
										}
									}
								}else{
									$dd_options.=$mw_qbo_product_list;
								}
							?>
							<select name="mw_wc_qbo_sync_wc_taxify_map_qbo_product" id="mw_wc_qbo_sync_wc_taxify_map_qbo_product" class="filled-in production-option mw_wc_qbo_sync_select <?php echo $dd_ext_class;?>">
								<?php echo $dd_options;?>
							</select>
						</td>
						
						<td>
							<div class="material-icons tooltipped tooltip">?
								<span class="tooltiptext">
									<?php _e( 'QBO Product', 'mw_wc_qbo_sync' );?>
								</span>
							</div>
						</td>
					</tr>
					
					<tr>
						<td colspan="3">
							<input type="submit" name="comp_taxify" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save">
						</td>
					</tr>
					</table>
				</div>
			</div>
		</div>
		<?php endif;?>
		
		<!--WooCommerce TM Extra Product Options-->
		
		
		<!--WooCommerce Product Add-ons-->
		
		
		<!--WooCommerce Appointments-->
		
		
		<!--WooCommerce Product ==> QuickBooks Online Product (NP)-->
		
		
		<!--WooCommerce USER  ==> QuickBooks Online Vendor (NP)-->
		
		
		<!--Aelia Currency Switcher for WooCommerce-->
		
		
		<!--If No Plugin-->
		<?php if(!$is_compt):?>
		<table width="100%">
			<tr>
				<td colspan="3">
					<b><?php _e( 'No Compatibility Included.', 'mw_wc_qbo_sync' );?></b>
				</td>
			</tr>
		</table>
		<?php endif;?>
		
	</form>
</div>
<?php MyWorks_WC_QBO_Sync_Admin::get_settings_assets(1);?>
<?php MyWorks_WC_QBO_Sync_Admin::set_setting_alert($MSQS_QL->get_session_val('compt_settings_save_msg','',true)) ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	<?php echo $list_selected;?>
	jQuery('input.mwqs_st_chk').attr('data-size','small');
	jQuery('input.mwqs_st_chk').bootstrapSwitch();
	
	$("#mw_wc_qbo_sync_compt_p_wconmkn").keyup(function(){
	  var re = /^\w+$/;
	  if ($(this).val()!='' && !re.test($(this).val())) {
		$(this).attr('title',"Invalid Text");
		$(this).css("background-color", "pink");
		$("#comp_wsnop_fb_omk").attr('disabled', 'disabled');
		 return false;
	  }
	  
	  $(this).attr('title',"");
	  $(this).css("background-color", "");
	  $("#comp_wsnop_fb_omk").removeAttr("disabled");
	  return true;	 
	});
});
</script>
<?php echo $MWQS_OF->get_select2_js('select','qbo_product');?>