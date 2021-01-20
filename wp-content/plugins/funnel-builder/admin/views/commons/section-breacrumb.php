<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
/**
 * Breadcrumb section
 */
?>
<div class="bwf_breadcrumb wffn-hide" id="wffn_breadcrumb_container_vue">

	<?php echo BWF_Admin_Breadcrumbs::render(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 	?>
    <div class="bwf_after_bre">
        <a v-on:click="updateFunnel()" href="javascript:void(0);" class="bwf_edt">
            <i class="dashicons dashicons-edit"></i> <?php esc_html_e( 'Edit', 'funnel-builder' ); ?>
        </a>
        <a v-if="false !== getViewLink()" v-bind:href="decodeHtml(getViewLink())" target="_blank" class="wffn-step-preview wffn-step-preview-admin">
            <i class="dashicons dashicons-visibility wffn-dash-eye"></i>
            <span class="preview_text"><?php esc_html_e( 'View', 'funnel-builder' ); ?></span>
        </a>
    </div>
</div>