<?php
use \CodeClouds\Unify\Service\Request;
use \CodeClouds\Unify\Service\Notice;
?>
<div class="unify-table-area dash-in dashboard">
    <div class="container-fluid unify-mid-heading p-0 mb-4">
        <div class="row">
            <div class="col-12">
                <div class="page-block-top-heading clearfix">
                    <h2 class="mid-heading">Upgrade to Pro</h2></div>
            </div>
        </div>
    </div>
	<?php 
			
			if (!session_id()) { session_start(); }
			if(Notice::hasFlashMessage('unify_notification')){
				include_once __DIR__ . '/Notice/notice.php';
			}
		
		?>
    <div class="container-fluid unify-table p-0 tran-bg-in ">
        <div class="row clearfix m-0">
            <div class="col-md-4 pl-0 pr-2 ">
                <div class="crd-white-box  border-0 uni-shadow-box">
                    <div class="inner-white-box">
                        <h2 class="lg-bld-heading m-0">Hi there, <?php echo ucfirst($current_user->display_name); ?></h2>
                        <p class="prd-dp-text mt-4">Did you know Unify offers a pro version?</p>
                        <p class="prd-dp-text mt-3">Take a look at the features being offered in <strong>Unify Pro</strong> compared to the free version <span class="arrow-int">&#8594;</span></p>
                    </div>


                    <div class="dash-free-from blue-lt inner-white-box">
						<form name="request_unify_pro_form" id="request_unify_pro_form"  method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" >
							<input type="hidden" name="post_type" id="post_type" value="unify_connections" />
							<input type="hidden" name="action" id="action" value="request_unify_pro" />
							<h3>Interested in Unify Pro features?</h3>
							<p>Request an upgrade to Unify Pro through this form</p>

							<div class="form-group">
								<span class="grp-all"><input type="text" name="full_name" id="full_name" class="fld-cst" placeholder="Full Name"></span>
							</div>

							<div class="form-group">
								<span class="grp-all"><input type="text" name="company_name" id="company_name" class="fld-cst" placeholder="Business/Company Name"></span>
							</div>

							<div class="form-group">
								<span class="grp-all"><input type="email" name="email_address" id="email_address" class="fld-cst" placeholder="Email"></span>
							</div>

							<div class="form-group">
								<span class="grp-all"><input type="text" name="phone_number" id="phone_number" class="fld-cst" placeholder="Phone"></span>
							</div>

							<div class="form-group">
								<span class="grp-all pull-tab-wrap">
									<span class="pull-tab"></span>
									<textarea name="comment" id="comment" cols="" rows="" class="fld-cst-txt" placeholder="Leave a comment"></textarea></span>
							</div>
							<div class="form-group mt-4 text-center">
								<input type="submit" onclick="javascript:void(0);" value="Get Unify Pro" id="submit_unify_pro" class=""><span class="ajax-loader"></span>
							</div>
							
							<?php wp_nonce_field('request_unify_pro_chk'); ?>
						</form>
                    </div>
                </div>
                
            </div>

            <div class="col-md-8 pl-2 pr-0">
                <div class="crd-white-box uni-shadow-box border-0">
                    <div class="inner-white-box p-0">
                        <table class="comp">
                          <tbody><tr>
                            <th class="uni-nornal-col pl-40" style="width:60%">List of all features</th>
                            <th class="uni-free-col">Unify Free</th>
                            <th class="blue-lt uni-pro-col">Unify Pro</th>
                          </tr>
                          <tr>
                            <td class="pl-40">Process payments through supported CRMs (LimeLight CRM and Konnektive CRM)</td>
                            <td class="uni-free-col-in" ><i class="fa fa-check"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>
                          <tr>
                            <td class="pl-40">Map products between your storefront and your CRM</td>
                            <td class="uni-free-col-in"><i class="fa fa-check"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>
                          <tr>
                            <td class="pl-40">Batch import products</td>
                            <td class="uni-free-col-in"><i class="fa fa-check"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>
                          <tr>
                            <td class="pl-40">Support for tax profiles</td>
                            <td class="uni-free-col-in"><i class="fa fa-check"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>
                          <tr>
                            <td class="pl-40">Support for billing model</td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>

                          <tr>
                            <td class="pl-40">Subscription management through membership portal</td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>

                          <tr>
                            <td class="pl-40">Void and Refund requests through Unify</td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>

                          <tr>
                            <td class="pl-40">Basic support ticket system</td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>

                          <tr>
                            <td class="pl-40">Lightweight customer support chat</td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>

                          <tr>
                            <td class="pl-40">Customer membership portal where customers can manage and track their orders </td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>

                          <tr>
                            <td class="pl-40">Control Panel </td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>

                          <tr>
                            <td class="pl-40">Various built in plugins, including coupon management, address validation, fraud detection, and more</td>
                            <td class="gray uni-free-col-in"><i class="fas fa-minus"></i></td>
                            <td class="blue-lt uni-pro-col-in"><i class="fa fa-check"></i></td>
                          </tr>
                        </tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 