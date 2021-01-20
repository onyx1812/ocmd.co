<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<style>
    .wfob_wrap_l {
        background: #fff;
    }
</style>
<style [data-type='wfob' ]></style>
<?php
$product = WFOB_Common::get_bump_products( WFOB_Common::get_id() );
?>
<div class="wfob_design_setting" id="wfob_bump_setting">


    <form data-wfoaction="global_settings" v-on:submit.prevent="save" @change="onchange()">
        <div class="wfob_design_setting_inner">
            <div class="wfob_fsetting_table_head">
                <div class="wfob_fsetting_table_head_in wfob_clearfix">
                    <div class="wfob_fsetting_table_title "><?php echo __( '<strong>Settings</strong>', 'woofunnels-order-bump' ); ?>
                    </div>
                    <div class="setting_save_buttons wfob_form_submit">
                        <span class="wfob_spinner spinner"></span>
                        <button class="wfob_save_btn_style"><?php _e( 'Save Settings', 'woofunnels-order-bump' ); ?></button>
                    </div>
                </div>
            </div>


            <div class="wfob_settings_sections wfob_local_setting_wrap">
                <div class="wfob_content_wrap wfob_sec_wrap_h">
                    <div class="wfob_wrap_l">
                        <div class="wfob-product-tabs-view-horizontal wfob-product-widget-tabs">
                            <div class="wfob-product-widget-container">
                                <div class="wfob-product-tabs wfob-tabs-style-line" role="tablist">
                                    <div class="wfob-product-tabs-content-wrapper">
                                        <div class="wfob_global_setting_inner">
                                            <div class="wfob_vue_forms">
                                                <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </form>

</div>
