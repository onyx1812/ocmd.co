<div class="unify-table-area dash-in">
    <div class="container-fluid unify-mid-heading p-0 mb-4">
        <div class="row">
            <div class="col-12">
                <div class="page-block-top-heading clearfix">
                    <h2 class="mid-heading">Dashboard</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid unify-search p-0 bottom-mg-gap uni-shadow-box">
        <div class="row clearfix m-0">
            <div class="col-12 unify-top-search-left pr-0 pl-0">
                <ul class="dash-top-box">
                    <li class="inner-white-box big-box">
                        <h2 class="lg-bld-heading m-0">Hi there, <?php echo ucfirst($current_user->display_name); ?></h2>
                        <span class="quick-txt">Here’s a quick look at your current connections and products mapped in Unify Pro <span class="arrow-int">&#8594;</span></span> </li>
                    <li class="inner-white-box text-center">
                         <span class="out-value"><?php echo $mapped_product->post_count; ?></span>
                        <span class="out-text">Products Mapped</span>
                    </li>
                    <li class="inner-white-box text-center">
                        <span class="out-value"><?php echo $total_publish_posts; ?></span>
                        <span class="out-text">Total Connections</span>
                    </li>
                    <li class="inner-white-box text-center">
                        <span class="out-value"><?php echo $todays_order_count; ?></span>
                        <span class="out-text">Orders Processed Today</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container-fluid unify-table p-0 tran-bg-in ">
        <div class="row clearfix m-0">
            <div class="col-md-6 pl-0 pr-2 ">
				
                <div class="crd-white-box  border-0 bottom-mg-gap uni-shadow-box" onclick="manageConn();" >
                    <div class="inner-white-box text-center hov-box ">
                        <img alt="" width="" height="" src="<?php echo plugins_url('unify/assets/images/icon-connection.png'); ?>" style="">
                            <span class="hov-box-txt">Manage Connections</span>
                    </div>
                </div>
				
				
                <div class="crd-white-box  border-0 bottom-mg-gap uni-shadow-box" onclick="manageSettings();" >
                    <div class="inner-white-box text-center hov-box">
                        <img alt="" width="" height="" src="<?php echo plugins_url('unify/assets/images/icon-plugin.png'); ?>" style="">
                            <span class="hov-box-txt">Plugin Settings</span>
                    </div>
                </div>
					
            </div>

            <div class="col-md-6 pl-2 pr-0">
				
                <div class="crd-white-box  border-0 bottom-mg-gap uni-shadow-box" onclick="manageProdMap();" >
                    <div class="inner-white-box text-center hov-box ">
                        <img alt="" width="" height="" src="<?php echo plugins_url('unify/assets/images/icon-prodmap.png'); ?>" style="">
                        <span class="hov-box-txt">Manage Product Mapping</span>
                    </div>
                </div>
					
				
                <div class="crd-white-box  border-0 bottom-mg-gap uni-shadow-box">
                    <div class="inner-white-box text-center hov-box ">
                        <img alt="" width="" height="" src="<?php echo plugins_url('unify/assets/images/icon-portal.png'); ?>" style="">
                        <span class="hov-box-txt">Go to Customer Portal</span>
                    </div>
                </div>
				
            </div>
        </div>
    </div>

</div> 

<script type = "text/javascript">
	
	function manageConn() {
	   window.location = "<?php echo admin_url('admin.php?page=unify-connection'); ?>";
	}
	
	function manageSettings() {
	   window.location = "<?php echo admin_url('admin.php?page=unify-settings'); ?>";
	}
	
	function manageProdMap() {
	   window.location = "<?php echo admin_url('admin.php?page=unify-tools&section=product-mapping'); ?>";
	}
				
</script>