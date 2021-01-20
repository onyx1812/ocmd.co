<?php
global $wfob_is_rules_saved;
?>
<div id="wfob_bump_rule_add_settings" data-is_rules_saved="<?php echo ( 'yes' === $wfob_is_rules_saved ) ? 'yes' : 'no'; ?>">
    <div class="wfob_welcome_wrap">
        <div class="wfob_welcome_wrap_in">
            <div class="wfob_welc_head">
                <div class="wfob_welc_icon"><img src="<?php echo WFOB_PLUGIN_URL; ?>/admin/assets/img/clap.png" alt="" title=""/></div>
                <div class="wfob_welc_title"> <?php _e( 'You’re ready to go', 'woofunnels-order-bump' ); ?>
                </div>
            </div>
            <div class="wfob_welc_text"><p><?php _e( 'As a first step you need to set up Rules (set of conditions) to trigger this bump.', 'woofunnels-order-bump' ); ?></p></div>

            <button class="button-primary wfob_bump_rule_add_settings wfob_welc_btn"><?php echo __( '+ Add Rules', 'woofunnels-order-bump' ); ?></button>
        </div>
    </div>
</div>
