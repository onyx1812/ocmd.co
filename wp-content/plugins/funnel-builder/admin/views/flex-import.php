<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff
/**
 * Adding analytics page
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
?>
<div class="">
    <div class="postbox">
        <div class="inside">

            <div class="wffn_flex_import_page">
				<?php if ( false === WFFN_Core()->import->is_imported ) { ?>

                    <div class="wffn_import_head"><?php esc_html_e( 'Import Funnels from a JSON file', 'funnel-builder' ); ?></div>
                    <div class="wf_funnel_clear_10"></div>
                    <div class="wffn_import_para"><?php esc_html_e( 'Note: Designs made using page builders needs to be imported separately.', 'funnel-builder' ); ?></div>
                    <div class="wf_funnel_clear_10"></div>

                    <form method="POST" enctype="multipart/form-data">
                        <p>
                            <input type="file" name="file" required>
                            <input type="hidden" name="wffn-action" value="import">
                        </p>
                        <div class="wf_funnel_clear_10"></div>
                        <p style="margin-bottom:0">
                            <input type="hidden" id="wffn-action" name="wffn-action-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wffn-action-nonce' ) ); ?>">
                            <input type="submit" name="submit" class="wf_funnel_btn wf_funnel_btn_primary" value="<?php esc_html_e( 'Upload Exported File', 'funnel-builder' ); ?>">
                        </p>
                    </form>
				<?php } else { ?>
                    <div class="wffn_import_head"><?php esc_html_e( 'Import Success', 'funnel-builder' ); ?></div>
                    <div class="wf_funnel_clear_10"></div>
                    <div class="wffn_import_para"><?php esc_html_e( 'Funnels have been imported successfully.', 'funnel-builder' ); ?></div>
                    <div class="wf_funnel_clear_10"></div>
					<?php $funnel_url = add_query_arg( array( 'page' => 'bwf_funnels' ), admin_url( 'admin.php' ) ); ?>
                    <a href="<?php echo esc_url( $funnel_url ); ?>" class="wf_funnel_btn wf_funnel_btn_primary"><?php esc_html_e( 'Go to Funnels', 'funnel-builder' ); ?></a>
				<?php } ?>
            </div>

        </div>
    </div>
</div>
