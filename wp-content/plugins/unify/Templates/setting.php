<?php
use \CodeClouds\Unify\Service\Notice;
?>
<div class="unify-table-area dash-in">
    <div class="container-fluid unify-mid-heading p-0 mb-4">
        <div class="row">
            <div class="col-12">
                <div class="page-block-top-heading clearfix">
                    <h2 class="mid-heading">Settings&nbsp;&nbsp;</h2></div>
            </div>
        </div>
    </div>

    <div class="container-fluid unify-search p-0 mgbt-25 uni-shadow-box">
        <div class="row clearfix m-0">
            <div class="col-12 unify-top-search-left pr-0 pl-0">
                <div class="unify-white-menu clearfix">
                    <ul class="option-row-simple-menu"> 
                        <li class="btn btn-link active"><a href="<?php echo admin_url('admin.php?page=unify-settings')?>">General</a></li>
                        <!--<li class="btn btn-link"><a href="">Pro Settings</a></li>-->
                    </ul>
                </div>
            </div>
        </div>
    </div>
	
	<?php
	
	if (!session_id()) { session_start(); }
	
	if (Notice::hasFlashMessage('unify_notification'))
	{
		include_once __DIR__ . '/Notice/notice.php';
	}
	
	?>

    <div class="container-fluid unify-table p-0 tran-bg-in ">
        <div class="row clearfix m-0">
            <div class="col-md-6 pl-0 pr-2 ">
			
				<div class="crd-white-box  border-0 bottom-mg-gap">
					<form name="unify_settings_form_post" id="unify_settings_form_post" method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" >
						<div class="inner-white-box uni-shadow-box">
							<h3 class="mid-blue-heading" >Settings</h3>
							
							<div class="inner-api-cont mt-4">
								<div class="form-row m-0">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="enabled" class="mb-0">Enable</label>
                                        <small class="form-text text-muted"></small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="enabled" name="enabled" value="yes" type="checkbox" class="switch" <?php echo (empty($setting_data) || (!empty($setting_data['enabled']) && $setting_data['enabled'] == 'yes')) ? 'checked="checked"' : ''; ?>  > 
                                                    <label for="enabled"></label>
												</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>

							<div class="inner-api-cont mt-4">
								<div class="form-group m-0">
									<label for="title">Title</label>
									<small class="form-text text-muted">This controls the title which the user sees during checkout.</small>
									<input type="text" id="title" name="title" value="<?php echo (!empty($setting_data['title'])) ? $setting_data['title'] : ''; ?>" class="form-control">
								</div>
							</div>

							<div class="inner-api-cont mt-4">
								<div class="form-group m-0">
									<label for="description">Description</label>
									<small class="form-text text-muted">This controls the description which the user sees during checkout.</small>
									<input type="text" id="description" name="description" value="<?php echo (!empty($setting_data['description'])) ? $setting_data['description'] : ''; ?>" class="form-control">
								</div>
							</div>

							<div class="inner-api-cont mt-4">
								<div class="form-group m-0" id="connection_error" >
									<label for="connection">Connections</label>
									<small class="form-text text-muted">Choose a connection for payment process.</small>
									<select name="connection" id="connection" class="custom-select sources" placeholder="<?php echo ((!empty($setting_data['connection']) && array_key_exists($setting_data['connection'], $connection_list))) ? $connection_list[$setting_data['connection']]['title'] : 'Please select a connection'; ?>" >
										<?php
										
										foreach ($connection_list as $k => $conn)
										{

											?>
											<option value="<?php echo $k; ?>" data-crm="<?php echo $conn['crm']; ?>" data-billing-model="<?php echo $conn['billing_model']; ?>" >
												<?php echo $conn['title']; ?>
											</option>
<?php } ?>

                                    </select>
								</div>
							</div>

							<div class="inner-api-cont mt-4" id="shipment_price_settings_div" >
								<div class="form-group m-0">
									<label for="shipment_price_settings">Shipment Price Settings</label>
									<small class="form-text text-muted gp-15">If you have various shipping method for a product then you can turn on this feature. In Single Order option, all the product will be clubbed under one order and the shipping will be charged based on WooCommerce Shipping calculation and will reflect in one product price and with free shipping ID. In Multi-Order all the product will be clubbed under unique Shipping ID(s) as a result more than 1 order will be in generated in Limelight.</small>
									<select name="shipment_price_settings" id="shipment_price_settings" class="custom-select sources" placeholder="<?php echo ((!empty($setting_data['shipment_price_settings']) && array_key_exists($setting_data['shipment_price_settings'], $shipment_list))) ? $shipment_list[$setting_data['shipment_price_settings']] : 'Please choose an option'; ?>"  >
										<?php
										foreach ($shipment_list as $k => $conn_sett)
										{

											?>
											<option value="<?php echo $k; ?>"  ><?php echo $conn_sett; ?></option>
<?php } ?>

                                    </select>
								</div>
							</div>

<!--							<div class="inner-api-cont mt-4" >
								<div class="form-group m-0">
									<label for="shipping_product_id">Shipping Product ID</label>
									<small class="form-text text-muted">Limelight Product ID for Shipping Charge.</small>
									<input type="text" id="shipping_product_id" name="shipping_product_id" value="<?php echo (!empty($setting_data['shipping_product_id'])) ? $setting_data['shipping_product_id'] : ''; ?>" class="form-control" onkeypress='return event.charCode >= 48 && event.charCode <= 57' >
								</div>
							</div>-->
							
							<div class="inner-api-cont mt-4" id="shipping_product_div" >
								<div class="form-group m-0">
									<label for="shipping_product_id">Map Shipping Product</label>
									<small class="form-text text-muted">Configure Limelight Product for Shipping Charge.</small>
									<div class="form-row">
										<div class="col-sm-4 pl-0 pr-1" id="shipping_product_id_div" >
											<input type="text" class="form-control" id="shipping_product_id" name="shipping_product_id" value="<?php echo (!empty($setting_data['shipping_product_id'])) ? $setting_data['shipping_product_id'] : ''; ?>" class="form-control" onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="CRM Product ID" >
										</div>
										<div class="col-sm-4 pl-1 pr-1 shipping_product_offer_div" >
											<input type="text" class="form-control" id="shipping_product_offer_id" name="shipping_product_offer_id" value="<?php echo (!empty($setting_data['shipping_product_offer_id'])) ? $setting_data['shipping_product_offer_id'] : ''; ?>"  onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="CRM Offer ID" >
										</div>
										<div class="col-sm-4 pr-0 pl-1 shipping_product_offer_div">
											<input type="text" class="form-control" id="shipping_product_billing_id" name="shipping_product_billing_id" value="<?php echo (!empty($setting_data['shipping_product_billing_id'])) ? $setting_data['shipping_product_billing_id'] : ''; ?>"  onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="CRM Billing ID" >
										</div>
									</div>
								</div>
							</div>

                            <div class="inner-api-cont mt-4" id="additional_payment_method1" style="display: none;">
                                <div class="form-row m-0">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="paypal_enabled" class="mb-0">Enable Paypal</label>
                                        <small class="form-text text-muted"></small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="paypal_enabled" name="paypal_enabled" value="yes" type="checkbox" class="switch" <?php echo (empty($additional_setting_option) || (!empty($additional_setting_option['enabled']) && $additional_setting_option['enabled'] == 'yes')) ? 'checked="checked"' : ''; ?>  > 
                                                    <label for="paypal_enabled"></label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="inner-api-cont mt-4" id="additional_payment_method1_title" style="display: none;">
                                <div class="form-group m-0">
                                    <label for="title">Title</label>
                                    <small class="form-text text-muted">This controls the title which the user sees during checkout.</small>
                                    <input type="text" id="paypal_payment_title" name="paypal_payment_title" value="<?php echo (!empty($additional_setting_option['title'])) ? $additional_setting_option['title'] : ''; ?>" class="form-control">
                                </div>
                            </div>
                            <div class="inner-api-cont mt-4" id="additional_payment_method1_desc" style="display: none;">
                                <div class="form-group m-0">
                                    <label for="description">Description</label>
                                    <small class="form-text text-muted">This controls the description which the user sees during checkout.</small>
                                    <input type="text" id="paypal_payment_description" name="paypal_payment_description" value="<?php echo (!empty($additional_setting_option['description'])) ? $additional_setting_option['description'] : ''; ?>" class="form-control">
                                </div>
                            </div>
							
							<div class="inner-api-cont mt-4">
								<div class="form-row m-0">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="testmode" class="mb-0">Test Mode</label>
                                        <small class="form-text text-muted">It will disable card number's validation.</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="testmode" name="testmode" value="yes" type="checkbox" class="switch" <?php echo (!empty($setting_data['testmode']) && $setting_data['testmode'] == 'yes') ? 'checked="checked"' : ''; ?> >
                                                    <label for="testmode"></label>
												</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
							
							<div class="inner-api-cont mt-4">
								<div class="form-row m-0">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="enable_debugging" class="mb-0">Enable Debugging</label>
                                        <small class="form-text text-muted">Enable debugging to log API request and response.</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="enable_debugging" name="enable_debugging" value="yes" type="checkbox" class="switch" <?php echo (!empty($setting_data['enable_debugging']) && $setting_data['enable_debugging'] == 'yes') ? 'checked="checked"' : ''; ?> >
                                                    <label for="enable_debugging"></label>
												</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
							
							<div class="upl-cnt-btn text-center mgtp-20">
								<button type="button" class="btn btn-primary gen-col-btn-sm" id="submit_settings" >
									<span class="">Save</span> 
									<span class=""></span>
								</button>
							</div>
							
						</div>		
						
						<input type="hidden" name="connection_val" id="connection_val" value="<?php echo (!empty($setting_data['connection'])) ? $setting_data['connection'] : ''; ?>" />
						<input type="hidden" name="shipment_price_settings_val" id="shipment_price_settings_val" value="<?php echo (!empty($setting_data['shipment_price_settings'])) ? $setting_data['shipment_price_settings'] : ''; ?>" />
						<input type="hidden" name="testmode_val" id="testmode_val" value="<?php echo (!empty($setting_data['testmode'])) ? $setting_data['testmode'] : ''; ?>"/>
						<input type="hidden" name="action" value="unify_settings_form_post" />
						<input type="hidden" id="post_type" name="post_type" value="unify_connections">
						<input type="hidden" name="page" value="<?php echo \CodeClouds\Unify\Service\Request::any('page') ?>" />
						<?php wp_nonce_field('unify-settings-data'); ?>
					</form>
				</div>

            </div>

            <div class="col-md-6 pl-2 pr-0">
                <div class="crd-white-box  border-0 bottom-mg-gap" style="display: none;" >
                    <div class="inner-white-box uni-shadow-box">
                        <h3 class="mid-blue-heading">Activate or Deactivate Modules</h3>
                         <div class="inner-api-cont mt-4">
                                <div class="form-row">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="google_recaptcha" class="mb-0">Google Recaptcha</label>
                                        <small class="form-text text-muted">Enable Google Recaptcha security on your checkout</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="google_recaptcha" type="checkbox" name="allow-customer-register" class="switch"> 
                                                    <label for="google_recaptcha"></label></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="google_autocomplete" class="mb-0">Google Autocomplete</label>
                                        <small class="form-text text-muted">Enable Google Autocomplete to fill out forms faster</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="google_autocomplete" type="checkbox" name="allow-customer-register" class="switch"> 
                                                    <label for="google_autocomplete"></label></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="xverify" class="mb-0">Xverify</label>
                                        <small class="form-text text-muted">Eliminate your hard-bounces and reduce spam complaints</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="xverify" type="checkbox" name="allow-customer-register" class="switch"> 
                                                    <label for="xverify"></label></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="smarty_streets" class="mb-0">Smarty Streets</label>
                                        <small class="form-text text-muted">Address Verification for USPS and international addresses</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="smarty_streets" type="checkbox" name="allow-customer-register" class="switch"> 
                                                    <label for="smarty_streets"></label></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="Kount" class="mb-0">Kount</label>
                                        <small class="form-text text-muted">All-in-one fraud and risk management solution</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="Kount" type="checkbox" name="allow-customer-register" class="switch"> 
                                                    <label for="Kount"></label></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="paypal_checkout" class="mb-0">PayPal Checkout</label>
                                        <small class="form-text text-muted">Enable PayPal checkout to accept payments</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="paypal_checkout" type="checkbox" name="allow-customer-register" class="switch"> 
                                                    <label for="paypal_checkout"></label></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row m-0">
                                    <div class="form-group col-sm-8 p-0 m-0">
                                        <label for="promocode" class="mb-0">PROMO Code</label>
                                        <small class="form-text text-muted">Enable the PROMO/Coupon engine on your checkout</small>
                                    </div> 
                                    <div class="col-sm-4 p-0">
                                        <div class="ad-on-btn-in">
                                            <div class="slide-opt-box">
                                                <span class="switch">
                                                    <input id="promocode" type="checkbox" name="allow-customer-register" class="switch"> 
                                                    <label for="promocode"></label></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div> 