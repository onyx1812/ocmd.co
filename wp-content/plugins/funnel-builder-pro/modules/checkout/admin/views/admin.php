<?php
defined( 'ABSPATH' ) || exit;
/** Registering Settings in top bar */
if ( class_exists( 'BWF_Admin_Breadcrumbs' ) ) {
	BWF_Admin_Breadcrumbs::register_node( [ 'text' => __( 'Checkouts', 'woofunnels-aero-checkout' ) ] );
}
BWF_Admin_Breadcrumbs::render_sticky_bar();
?>
    <div class="wrap wfacp_funnels_listing wfacp_global wfacp_post_table">
        <div class="wfacp_clear_30"></div>
        <div class="wfacp_head_bar">
            <div class="wfacp_bar_head"><?php _e( 'Checkouts', 'woofunnels-aero-checkout' ); ?></div>
            <a href="javascript:void(0)" class="page-title-action" data-izimodal-open="#modal-checkout-page" data-iziModal-title="Create New Checkout page" data-izimodal-transitionin="fadeInDown"><?php _e( 'Add New', 'woofunnels-aero-checkout' ); ?></a>
            <a href="<?php echo admin_url( 'admin.php?page=wfacp&section=import' ); ?>" class="page-title-action"><?php _e( 'Import', 'woofunnels-aero-checkout' ); ?></a>
            <a href="<?php echo admin_url( 'admin.php?page=wfacp&section=export' ); ?>" class="page-title-action"><?php _e( 'Export', 'woofunnels-aero-checkout' ); ?></a>

        </div>

        <div id="poststuff">
            <div class="inside">
                <div class="wfacp_page_col2_wrap wfacp_clearfix">
                    <div class="wfacp_page_left_wrap">
                        <form method="GET">
                            <input type="hidden" name="page" value="wfacp"/>
                            <input type="hidden" name="status" value="<?php echo( isset( $_GET['status'] ) ? $_GET['status'] : '' ); ?>"/>
							<?php
							$this->wfacp_pages_table->render_trigger_nav();
							$this->wfacp_pages_table->search_box( 'Search' );
							$this->wfacp_pages_table->data = WFACP_Common::get_post_table_data( 'any', $this->wfacp_pages_table->per_page );
							$this->wfacp_pages_table->prepare_items();
							$this->wfacp_pages_table->display();
							?>
                        </form>
						<?php $this->wfacp_pages_table->order_preview_template(); ?>
                    </div>
                    <div class="wfacp_page_right_wrap">
						<?php do_action( 'wfacp_page_right_content' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include_once __DIR__ . '/global/model.php';
