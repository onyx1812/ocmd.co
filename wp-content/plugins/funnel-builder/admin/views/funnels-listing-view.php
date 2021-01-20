<?php
/**
 * Primary listing vieew
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
include_once( __DIR__ . '/commons/single-wffn-head.php' );
WFFN_Core()->admin->render_primary_nav();
?>
    <div class="wffn_clear_10"></div>
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Funnels', 'funnel-builder' ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwf_funnels&section=new' ) ); ?>" class="page-title-action"> <?php esc_html_e( 'Add New', 'funnel-builder' ); ?> </a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwf_funnels&section=import' ) ); ?>" class="page-title-action"> <?php esc_html_e( 'Import', 'funnel-builder' ); ?> </a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bwf_funnels&section=export' ) ); ?>" class="page-title-action"> <?php esc_html_e( 'Export', 'funnel-builder' ); ?> </a>
    <div id="wffn_flex_listing_vue">
        <hr class="wp-header-end">
        <div id="poststuff">
            <div class="inside">
                <div class="wffn_page_col2_wrap wffn_clearfix">
                    <form method="GET">
                        <input type="hidden" name="page" value="bwf_funnels"/>
                        <input type="hidden" name="status" value="<?php esc_attr( isset( $_GET['status'] ) ? wffn_clean( $_GET['status'] ) : '' );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>"/>
						<?php
						$funnels = WFFN_Core()->admin->get_funnels();

						$table       = new WFFN_Funnels_Listing_Table();
						$table->data = $funnels;
						$table->render_trigger_nav();
						$table->search_box( 'Search Funnels' );
						wp_nonce_field( 'wffn_bulk_del', '_wpnonce' );
						$table->data = $funnels;
						$table->prepare_items();
						$table->display();
						?>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <div class="flex-all-popups" id="flex_all_popups">
        <div id="wffn_all_popups_vue" class="popup_vue">
            <flex_popup v-bind:popup="popup_data"></flex_popup>
        </div>
    </div>
<?php
include_once( __DIR__ . '/vue-templates/flex-popup.vue' ); //phpcs:ignore WordPressVIPMinimum.Files.IncludingNonPHPFile


include_once( __DIR__ . '/commons/single-wffn-foot.php' );
