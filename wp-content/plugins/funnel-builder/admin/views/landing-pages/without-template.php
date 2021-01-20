<?php
/**
 * Without template
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
 BWF_Admin_Breadcrumbs::render_sticky_bar();
?>
    <div class="wrap" id="wffn_lp_edit_vue_wrap">
        <div class="bwf_breadcrumb">
            <div class="bwf_before_bre"></div>
            <div class="wf_funnel_card_switch">
                <label class="wf_funnel_switch">
                    <input type="checkbox" <?php checked( 'publish', WFFN_Core()->landing_pages->get_status() ); ?>>
                    <div class="wf_funnel_slider"></div>
                </label>
            </div>
			<?php echo BWF_Admin_Breadcrumbs::render(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <div class="bwf_after_bre">
                <a v-on:click="updateLanding()" href="javascript:void(0);" class="bwf_edt">
                    <i class="dashicons dashicons-edit"></i> <?php esc_html_e( 'Edit', 'funnel-builder' ); ?>
                </a>
                <a v-bind:href="view_url" target="_blank" class="wffn-step-preview wffn-step-preview-admin">
                    <i class="dashicons dashicons-visibility wffn-dash-eye"></i>
                    <span class="preview_text"><?php esc_html_e( 'View', 'funnel-builder' ); ?></span>
                </a>
            </div>
        </div>
		<?php
		WFFN_Core()->landing_pages->admin->get_tabs_html( WFFN_Core()->landing_pages->get_edit_id() );
		?>
        <div class="wffn_content_wrap">
            <div class="wffn_global_loader"><span class="spinner"></span></div>
			<?php
			$admin_instance = WFFN_Landing_Pages::get_instance();
			WFFN_Core()->admin->include_template_preview_helpers( $admin_instance, 'wflp' );
			?>
        </div>
    </div>
<?php
include_once dirname( __DIR__ ) . '/models/wffn-edit-landing.php';