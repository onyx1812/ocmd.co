<?php

use \CodeClouds\Unify\Service\Helper;

?>
<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
<form id="unify_product_post" name="unify_product_post" method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">

	<!-- For plugins, we also need to ensure that the form posts back to our current page -->
    <input type="hidden" name="page" value="<?php echo \CodeClouds\Unify\Service\Request::any('page') ?>" />
    <!--<input type="hidden" name="action" value="codeclouds_unify_tool_mapping" />-->
	
	<input type="hidden" name="orderby" id="orderby" value="<?php echo $request['orderby']; ?>" />
	<input type="hidden" name="order" id="order" value="<?php echo $request['order']; ?>" />
	
	<input type="hidden" name="check_submit" id="check_submit" value="update_product" />
	
    <!-- Now we can render the completed list table -->

	<div class="container-fluid tran-bg-in p-0 mb-0">
		<div class="row clearfix">
			<div class="col-12">
				<p class="prd-dp-text">You can map your <em class="no-title-set">CRM Product ID</em> with WooCommerce’s <em class="no-title-set">Product ID</em> here</p>
			</div>
		</div>
	</div>

	<div class="container-fluid tran-bg-in p-0 mb-2">
		<div class="row clearfix">
			<div class="col-12">
				<!--                 <ul class="brdc-mid">
									<li class="">
										<a href="" class="active-in" aria-current="">All <span class="count">(3)</span></a> |
									</li>
									<li class="active"><a href="">Published<span class="count">(3)</span></a></li> |
									<li class=""><a href="">Drafts<span class="count">(0)</span></a></li>
								</ul> -->

				<span class="uni-show-num">Showing <?php echo count($data['list']); ?> items</span>
			</div>
		</div>
	</div>
	<div class="container-fluid unify-table uni-shadow-box p-0 ">
		<div class="row">
			<div class="col-12">
				<div class="table-responsive product-table <?php echo (!empty($crm_meta) && !in_array($crm_meta, ['limelight', 'response']))? 'single' : ''; ?>">
					<table class="table table-hover">
						<thead>
							<tr>
								<th class="sm-in-tb">Thumbnail</th>
								<th class="sm-in-tb sortab" data-order-by="ID" data-order="<?php echo (!empty($request['orderby']) && $request['orderby'] == 'ID') ? $request['order'] : 'asc'; ?>" >
									<a href="javascript:void(0);"  id="sort-by-ID" >
										<span>Product ID</span>
										<span class="sorting-arrow">
											<i id='ID-icn' class="fas" <?php echo (!empty($_GET['orderby']) && $request['orderby'] == 'ID') ? 'data-hide="false"' : 'style="display:none;" data-hide="true" '; ?>  ></i>												
										</span>
									</a>
								</th>
								<th class="mid-in-tb sortab" data-order-by="post_title" data-order="<?php echo (!empty($request['orderby']) && $request['orderby'] == 'post_title') ? $request['order'] : 'asc'; ?>"  >
									<!-- Product Name -->
									<a href="javascript:void(0);"  id="sort-by-post_title" >
										<span>Product Name</span>
										<span class="sorting-arrow">
											<i id='post_title-icn' class="fas" <?php echo (!empty($_GET['orderby']) && $request['orderby'] == 'post_title') ? 'data-hide="false"' : 'style="display:none;" data-hide="true" '; ?>  ></i>												
										</span>
									</a>
								</th>
								<th>CRM Product ID</th>
<?php


 if (!empty($crm_meta) && $crm_meta == 'limelight')
{

	?>
									<th>CRM Shipping ID</th>
<?php

if (!empty($crm_model_meta) && $crm_model_meta == 1)
{

	?>									
									<th>Offer ID</th>
									<th>Billing Model ID</th>
							<?php }
								}?>
<?php
if ((!empty($crm_meta) && $crm_meta == 'response') && (empty($crm_model_meta) && $crm_model_meta != 1))
{

	?>
							 		<th>Group ID</th>
							<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							if (!empty($data['list']))
							{
								foreach ($data['list'] as $k => $prod_list)
								{

									?>
									<tr>
										<td class=""><span class="prd-thumb"><img alt="" width="35" height="35" src="<?php echo (empty(\wp_get_attachment_image_src(\get_post_thumbnail_id($prod_list['ID']), 'single-post-thumbnail')[0]) ? plugins_url('unify/assets/images/placeholder.png') : \wp_get_attachment_image_src(\get_post_thumbnail_id($prod_list['ID']), 'single-post-thumbnail')[0]); ?>" style="" ></span></td>
										<td class=""><?php echo $prod_list['ID'] ?></td>
										<td class=""><?php echo $prod_list['post_title'] ?></td>
										<td><p class="product-field"><input type="text" name="map[<?php echo $prod_list['ID'] ?>][codeclouds_unify_connection]" value="<?php echo empty($prod_list['codeclouds_unify_connection']) ? '' : $prod_list['codeclouds_unify_connection']; ?>" class="form-control" aria-required="true" aria-invalid="false" /></p></td>
										<?php if (!empty($crm_meta) && $crm_meta == 'limelight')
										{

											?>
											<td><p class="product-field"><input type="text" name="map[<?php echo $prod_list['ID'] ?>][codeclouds_unify_shipping]" value="<?php echo (empty($prod_list['codeclouds_unify_shipping'])) ? '' : $prod_list['codeclouds_unify_shipping']; ?>" class="form-control" aria-required="true" aria-invalid="false" /></p></td>

										<?php if (!empty($crm_model_meta) && $crm_model_meta == 1)
										{

											?>	
											<td><p class="product-field"><input type="text" name="map[<?php echo $prod_list['ID'] ?>][codeclouds_unify_offer_id]" value="<?php echo empty($prod_list['codeclouds_unify_offer_id']) ? '' : $prod_list['codeclouds_unify_offer_id']; ?>" class="form-control" aria-required="true" aria-invalid="false" /></p></td>
											<td><p class="product-field"><input type="text" name="map[<?php echo $prod_list['ID'] ?>][codeclouds_unify_billing_model_id]" value="<?php echo empty($prod_list['codeclouds_unify_billing_model_id']) ? '' : $prod_list['codeclouds_unify_billing_model_id']; ?>" class="form-control" aria-required="true" aria-invalid="false" /></p></td>
									<?php } 
									}
									?>
											
									<?php
									if ((!empty($crm_meta) && $crm_meta == 'response') && (empty($crm_model_meta) && $crm_model_meta != 1))
									{

										?>
										<td><p class="product-field"><input type="text" name="map[<?php echo $prod_list['ID'] ?>][codeclouds_unify_group_id]" value="<?php echo empty($prod_list['codeclouds_unify_group_id']) ? '' : $prod_list['codeclouds_unify_group_id']; ?>" class="form-control" aria-required="true" aria-invalid="false" /></p></td>

										<?php
									}
									?>
									</tr>
									<?php
								}
							}
							else
							{
								echo '<tr>
									<td colspan="' . (!empty($crm_meta) && $crm_meta == 'limelight') ? '4' : '7' . '">Data not found!</td>
								</tr>';
							}

							?>							

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid tran-bg-in p-0">
		<div class="row">
			<div class="col-12 text-right mgtp-20">
				<button type="submit" onclick="document.getElementById('check_submit').value='update_product'" class="btn btn-primary gen-col-btn">
					<span class="">Update Products</span> 
					<span class=""></span>
				</button>
			</div>
		</div>
	</div>

<?php if($data['total'] > 1) { echo Helper::getPaginationTemplate($prev_dis, $next_dis, $request['paged'], $data['total']); }  ?>

	<input type="hidden" name="action" value="unify_product_post" />
	<input type="hidden" id="post_type" name="post_type" value="unify_connections">
	<input type="hidden" id="section" name="section" value="product-mapping-new">
<?php wp_nonce_field('unify-product'); ?>
</form>