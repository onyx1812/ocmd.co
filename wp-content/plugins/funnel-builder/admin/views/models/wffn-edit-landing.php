<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
/**
 * Edit Sales page model
 */
?>
<!-----  EDIT MODAL  ------->
<div id="wf_landing_edit_modal" class="iziModal" data-izimodal-group="alerts">
    <div id="part-update-landing">

        <div v-if="`1`==current_state" class="wffn-update-landing-form">
            <div class="wf_funnel_popup_header">
                <div class="wf_funnel_pop_title"><?php esc_html_e( 'Edit Sales Page', 'funnel-builder' ); ?></div>
                <button data-iziModal-close class="icon-close wf_funnel_popup_close"><span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
            <div class="wf_funnel_pop_body">
                <form class="wffn_forms_update_landing">
                    <div class="bwfabt_row">
                        <div class="bwfabt_vue_forms">
                            <vue-form-generator ref="update_landing_ref" :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                        </div>
                    </div>

                    <div class="wf_funnel_clear_20"></div>
                    <div class="wf_funnel_center_align">
                        <input type="button" v-on:click="updateLanding()" class="wf_funnel_btn wf_funnel_btn_primary" value="Update"/>
                    </div>
                    <div class="wf_funnel_clear_10"></div>
                </form>
            </div>
        </div>



    </div>
</div>
