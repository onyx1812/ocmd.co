<?php
/**
 * Adding funnel page
 */
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
include_once( __DIR__ . '/commons/single-wffn-head.php' );
?>
    <div class="wffn-sec-head_wrap"><?php include_once( __DIR__ . '/commons/section-head.php' ); ?> </div>
    <div class="wffn-flex-container " id="wffn_flex_container_vue">
        <div class="top wffn-loading" v-bind:class="`1`===is_initialized?'wffn-hide':''">
            <div align="center"><img src="<?php echo esc_url( WFFN_Core()->get_plugin_url() ); ?>/admin/assets/img/readiness-loader.gif"></div>
        </div>
        <div class="wffn-flex-container-steps wffn-hide" v-bind:class="`1`===is_initialized?'wffn-show':''">
            <div v-if="`first`===current_state" class="wffn-funnel-new" v-bind:class="`1`===is_initialized?'':'wffn-hide'">
                <div class="clear"></div>
				<?php include_once( __DIR__ . '/funnels/without-template.php' ); ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="wf_funnel_clear_30"></div>
<?php
include_once( __DIR__ . '/commons/single-wffn-foot.php' );
