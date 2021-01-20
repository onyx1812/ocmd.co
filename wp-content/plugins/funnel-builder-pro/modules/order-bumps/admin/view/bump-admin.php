<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** Registering Settings in top bar */
if ( class_exists( 'BWF_Admin_Breadcrumbs' ) ) {
	BWF_Admin_Breadcrumbs::register_node( [ 'text' => __( 'OrderBumps', 'woofunnels-order-bump' ) ] );
}
BWF_Admin_Breadcrumbs::render_sticky_bar();
?>
    <div class="wrap wfob_funnels_listing wfob_global" id="wfob_admin_post_table">
        <div class="wfob_clear_30"></div>
        <div class="wfob_head_bar">
            <div class="wfob_bar_head"><?php _e( 'OrderBumps', 'woofunnels-order-bump' ); ?></div>
            <a href="javascript:void(0)" class="page-title-action" data-izimodal-open="#modal-add-bump" data-iziModal-title="Create New Offer" data-izimodal-transitionin="fadeInDown"><?php echo __( 'Add New', 'woofunnels-order-bump' ); ?></a>
            <a href="<?php echo admin_url( 'admin.php?page=wfob&section=import' ); ?>" class="page-title-action"><?php echo __( 'Import', 'woofunnels-order-bump' ); ?></a>
            <a href="<?php echo admin_url( 'admin.php?page=wfob&section=export' ); ?>" class="page-title-action"><?php echo __( 'Export', 'woofunnels-order-bump' ); ?></a>
        </div>
        <div id="poststuff">
            <div class="inside">
                <div class="wfob_page_col2_wrap wfob_clearfix">
                    <div class="wfob_page_left_wrap">
                        <form method="GET">
                            <input type="hidden" name="page" value="wfob"/>
                            <input type="hidden" name="status" value="<?php echo( isset( $_GET['status'] ) ? $_GET['status'] : '' ); ?>"/>
							<?php
							$table = new WFOB_Post_Table();
							$table->render_trigger_nav();
							$table->search_box( 'Search' );
							$table->data = WFOB_Common::get_post_table_data();
							$table->prepare_items();
							$table->display();
							?>
                        </form>
						<?php $table->order_preview_template(); ?>
                    </div>
                    <div class="wfob_page_right_wrap">
						<?php do_action( 'wfob_page_right_content' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
do_action( 'wfob_admin_footer' );

?>
<?php include __DIR__ . '/global/model.php';
