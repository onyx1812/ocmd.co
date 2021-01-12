<?php
use \CodeClouds\Unify\Service\Notice;
?>

<div class="unify-table-area dash-in">
	<form id="unify_connections_post" class="unify_connections_post" name="unify_connections_post" method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" autocomplete="off">
		<div class="container-fluid unify-mid-heading p-0 mb-4">
			<div class="row">
				<div class="col-12">
					<div class="page-block-top-heading clearfix">
						<h2 class="mid-heading"><span class="st-gray">Connectivity&nbsp;&nbsp;|</span>&nbsp;&nbsp;<?php echo (!empty($_GET['post']) ? 'Update' : 'New'); ?> Connection</h2></div>
				</div>
			</div>
		</div>
		<div class="container-fluid unify-search p-0 bottom-mg-gap uni-shadow-box">
			<div class="row clearfix m-0">
				<div class="col-7 unify-top-search-left pr-0 pl-0">
					<div class="unify-search-col clearfix">

						<div class="dropdown-box-group">
							<div class="dropdown dropdown-opt">
								<label>Status &nbsp;  | </label>
								<?php $stat = ['active' => 'Active', 'pending' => 'Pending Review', 'publish' => 'Publish', 'draft' => 'Draft']; ?>
								<button type="button" data-toggle="dropdown" class="btn btn-light dropdown-toggle" id="post-stat" >
									<?php echo $stat[$conn_data['post_status']]; ?>
								</button>
								<div class="dropdown-menu uni-shadow-box" style="background: rgb(255, 255, 255); display: none;" id="post-stat-action" >
									<a class="dropdown-item selct-stat-val" val="active" >Active</a>
									<a class="dropdown-item selct-stat-val" val="publish" >Publish</a>
									<a class="dropdown-item selct-stat-val" val="draft" >Draft</a>
								</div> 
							</div>
							
						</div>
					</div>
				</div>
				<div class="col-5 unify-top-search-right pl-0 pr-0">
					<!--             	<div class="unify-search-right">
										<input type="text" id="search" placeholder="Search...">
										<button type="button" class="cst-top-search-btn btn btn-primary"><i class="fas fa-search"></i></button>
									</div> -->
					<div class="add-configuration-inner"><a href="javascript:void(0);" id="submit_connection" class="btn btn-primary btn-block"><?php echo (!empty($_GET['post']) ? 'Update Connection' : 'Save Connection'); ?></a></div>
				</div>
			</div>
		</div>
		
		<?php 
			
			if (!session_id()) { session_start(); }
			if(Notice::hasFlashMessage('unify_notification')){
				include_once __DIR__ . '/Notice/notice.php';
			}
		
		?>
		
		<div class="container-fluid  danger-bg unify-search p-0 mb-2 uni-shadow-box" style="display: none;">
			<div class="row clearfix m-0">
				<div class="col-12 text-danger danger-bg-text ">
					<p><?php echo html_entity_decode($notice['msg_txt']); ?>
						<?php if(!empty($notice['msg_url'])){ ?>
							<a class="change-pre" href="<?php echo $notice['msg_url']; ?>">Undo</a>
						<?php } ?>
					</p> 
					<span class="cross-position"><img alt="" width="10" height="10" src="<?php echo plugins_url('unify/assets/images/close-red.svg'); ?>" style=""></span>
				</div>
			</div>
		</div>
		
		<div class="container-fluid unify-table p-0 tran-bg-in ">
			<div class="row clearfix m-0">
				<div class="col-md-6 pl-0 pr-2 ">
					<div class="crd-white-box  border-0 bottom-mg-gap">
						<div class="inner-white-box uni-shadow-box">
							<h3 class="mid-blue-heading">New Connection Name</h3>
							<div class="inner-api-cont mt-4">
								<div class="form-group m-0 mt-1">
                                                                    <label for="Campaign_ID">Post Title</label>
									<!-- <label for="settings_name">Label</label>  -->
									<!-- <span class="text-danger">*</span>  -->
									<input type="text" id="post_title" name="post_title" value="<?php echo $conn_data['post_title']; ?>"  class="form-control" require > 
									<div class="invalid-feedback"></div>
								</div>
							</div>
						</div>
					</div>

					<div id="unify_campaign_details"class="crd-white-box  border-0 bottom-mg-gap" style=" ">
						<div class="inner-white-box uni-shadow-box">
							<h3 class="mid-blue-heading">Campaign Details</h3>
							<div class="inner-api-cont mt-4">
								<div id="unify_connection_campaign_details">
									<div class="form-group">
										<div id="unify_connection_campaign_id">
										<label for="Campaign_ID">Campaign ID</label> 
										</div>
										<!-- <span class="text-danger">*</span>  -->
										<input type="text" id="unify_connection_campaign_id" name="unify_connection_campaign_id" value="<?php echo $conn_data['unify_connection_campaign_id']; ?>" class="form-control" require > 
										<!-- <div class="invalid-feedback"></div> -->
									</div>

									<div class="form-group m-0" id="unify_connection_shipping_id">
										<label for="Default Shipping ID">Default Shipping ID</label> 
										<!-- <span class="text-danger">*</span>  -->
										<input  type="text" id="unify_connection_shipping_id" name="unify_connection_shipping_id" class="form-control" value="<?php echo $conn_data['unify_connection_shipping_id']; ?>"   class="form-control"  > 
										<!-- <div class="invalid-feedback"></div> -->
									</div>
								</div>
								
								
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6 pl-2 pr-0">
					<div class="crd-white-box  border-0 bottom-mg-gap">
						<div class="inner-white-box uni-shadow-box">
							<h3 class="mid-blue-heading">API Credentials</h3>
							<div class="inner-api-cont mt-4">
								<div class="form-group">
									<label for="settings_name">Select CRM</label> 
										<!-- <span class="text-danger">*</span> -->									
                                    <select name="unify_connection_crm_select" id="unify_connection_crm_select" class="custom-select sources" placeholder="Please select a connection" >
										<option class="" value="" >Please select a connection</option>
										<?php foreach ($all_connection as $key => $value)
										{

											?>
											<option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
<?php } ?>

                                    </select>
									<input type="hidden" name="unify_connection_crm" id="unify_connection_crm" value="<?php echo $conn_data['unify_connection_crm']; ?>" data-txt="<?php echo ucfirst($conn_data['unify_connection_crm']); ?>" />
									<!--  <div class="invalid-feedback"></div> -->
								</div>

								<div class="form-group" id="unify_connection_endpoint_div" style="display:none;" >
									<label for="Endpoint">Endpoint</label> 
									<!-- <span class="text-danger">*</span>  -->
									<input type="text" id="unify_connection_endpoint" name="unify_connection_endpoint" value="<?php echo $conn_data['unify_connection_endpoint']; ?>" class="form-control" > 
									<!-- <div class="invalid-feedback"></div> -->
								</div>


								<div class="form-group" id="unify_connection_secret_div" style="display:none;" >
									<label for="Endpoint">Secret</label> 
									<!-- <span class="text-danger">*</span>  -->
									<input type="text" id="unify_connection_secret" name="unify_connection_secret" value="<?php echo $conn_data['unify_connection_secret']; ?>" class="form-control" > 
									<!-- <div class="invalid-feedback"></div> -->
								</div>

								<div id="unify_connection_api_username_details">
									<div class="form-group">
										<div id="unify_connection_username_label">
										<label for="Username">Username</label>
										</div> 
										<!-- <span class="text-danger">*</span>  -->
										<input type="text" id="unify_connection_api_username" name="unify_connection_api_username" value="<?php echo $conn_data['unify_connection_api_username']; ?>" class="form-control" require > 
										<!-- <div class="invalid-feedback"></div> -->
									</div>

									<div class="form-group m-0" id="unify_connection_password_label">
										<label for="Password">Password</label> 
										<!-- <span class="text-danger">*</span>  -->
										<input type="password" id="unify_connection_api_password" name="unify_connection_api_password" autocomplete="none" value="<?php echo esc_attr($conn_data['unify_connection_api_password']); ?>" class="form-control" require > 
										<!-- <div class="invalid-feedback"></div> -->
									</div>
								</div>	
							</div>
						</div>
					</div>
					
					<div class="crd-white-box  border-0 bottom-mg-gap" id="offer_model_ent_div" style="display:none;" >
						<div class="inner-white-box uni-shadow-box">
							<div class="col-md-12">
								<h3 class="mid-blue-heading">Billing Method</h3>
								<div class="inner-api-cont mt-4">
									<div class="row">
										<div class="col-md-6 unify_billing_sub_title">Enable</div>
										<div class="col-md-6 text-right">
											<div class="form-group">
												<div class="ad-on-btn-in">
                                            	<div class="slide-opt-box">
                                                <span class="switch">
                                                    <input type="checkbox" id="unify_connection_offer_model_select" class="switch">
                                                    <label for="unify_connection_offer_model_select"></label>
												</span>
                                            </div>
                                        </div>
											</div>
										</div>	
									</div>	
								</div>
							</div>
							<div class="col-md-12 unify_order_note_enable_div">
								<div class="inner-api-cont mt-4">
									<div class="row">
										<div class="col-md-6 unify_billing_sub_title">Enable Order Note for Multiple variants</div>
										<div class="col-md-6 text-right">
											<div class="form-group">
												<div class="ad-on-btn-in">
                                            	<div class="slide-opt-box">
                                                <span class="switch">
                                                    <input type="checkbox" id="unify_order_note_enable" class="switch">
                                                    <label for="unify_order_note_enable"></label>
												</span>
                                            </div>
                                        </div>
											</div>
										</div>
									</div>	
								</div>
							</div>
						</div>

					</div>

					<div class="crd-white-box  border-0 bottom-mg-gap" id="response_crm_ent_div" style="display:none;" >
						<div class="inner-white-box uni-shadow-box">
							<div class="col-md-12">
								<h3 class="mid-blue-heading">Latest Version</h3>
								<div class="inner-api-cont mt-4">
									<div class="row">
										<div class="col-md-6 unify_billing_sub_title">Enable</div>
										<div class="col-md-6 text-right">
											<div class="form-group">
												<div class="ad-on-btn-in">
                                            	<div class="slide-opt-box">
                                                <span class="switch">
                                                    <input type="checkbox" id="unify_connection_response_crm_type_enable" class="switch">
                                                    <label for="unify_connection_response_crm_type_enable"></label>
												</span>
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
		</div>
		

		
		<input type="hidden" name="unify_connection_offer_model" id="unify_connection_offer_model" value="<?php echo $conn_data['unify_connection_offer_model']; ?>"/>
		<input type="hidden" name="unify_order_note" id="unify_order_note" value="<?php echo $conn_data['unify_order_note']; ?>" />
		<input type="hidden" name="ID" value="<?php echo ((empty($_GET['post'])) ? '' : $_GET['post']); ?>" />
		<input type="hidden" name="post_status" id="post_status" value="<?php echo $conn_data['post_status']; ?>" />
		<input type="hidden" name="action" value="unify_connections_post" />
		<input type="hidden" id="post_type" name="post_type" value="unify_connections">
		<input type="hidden" name="unify_response_crm_type_enable" id="unify_response_crm_type_enable" value="<?php echo $conn_data['unify_response_crm_type_enable']; ?>" />
		<?php wp_nonce_field('codeclouds-unify-connection'); ?>
	</form>
</div> 