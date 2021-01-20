<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly
/**
 * Edit Thank You Optin Page model
 */
?>
<!-----  EDIT MODAL  ------->
<div id="wf_oty_edit_modal" class="iziModal" data-izimodal-group="alerts">
  <div id="part-update-oty">

    <div v-if="`1`==current_state" class="wffn-update-oty-form">
      <div class="wf_funnel_popup_header">
        <div class="wf_funnel_pop_title"><?php esc_html_e( 'Edit Thank You Optin Page', 'funnel-builder' ); ?></div>
        <button data-iziModal-close class="icon-close wf_funnel_popup_close"><span class="dashicons dashicons-no-alt"></span>
        </button>
      </div>
      <div class="wf_funnel_pop_body">
        <form class="wffn_forms_update_oty">
          <div class="bwfabt_row">
            <div class="bwfabt_vue_forms">
              <vue-form-generator ref="update_oty_ref" :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
            </div>
          </div>

          <div class="wf_funnel_clear_20"></div>
          <div class="wf_funnel_center_align">
            <input type="button" v-on:click="updateOty()" class="wf_funnel_btn wf_funnel_btn_primary" value="Update"/>
          </div>
          <div class="wf_funnel_clear_10"></div>
        </form>
      </div>
    </div>

    <div v-if="`2`===current_state" class="wffn-updating-oty">
      <div class="wf_funnel_pop_body">
        <div class="bwfabt_row">
          <div class="wf_funnel_center_align">
            <img src="<?php echo esc_url( WFFN_Core()->get_plugin_url() ); ?>/admin/assets/img/readiness-loader.gif"></div>
        </div>
        <div class="wf_funnel_clear_20"></div>
        <div class="wf_funnel_center_align">
          <div class="wf_funnel_h3"><?php esc_html_e( 'Updating Thank You Optin Page', 'funnel-builder' ); ?></div>
          <div class="wf_funnel_p"><?php esc_html_e( 'Please wait it may take couple of moments...', 'funnel-builder' ); ?></div>
        </div>
        <div class="wf_funnel_clear_30"></div>
      </div>
    </div>

    <div v-if="`3`===current_state" class="wffn-funnel-updated">
      <div class="wf_funnel_pop_body">
        <div class="bwfabt_row">
          <div class="wf_funnel_center_align">
            <div class="wf_funnel_clear_40"></div>
            <svg class="wffn_loader wffn_loader_ok" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
              <circle class="path circle" fill="none" stroke="#baeac5" stroke-width="5" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"></circle>
              <polyline class="path check" fill="none" stroke="#39c359" stroke-width="9" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "></polyline>
            </svg>
            <div class="wf_funnel_clear_20"></div>
          </div>
        </div>
        <div class="wf_funnel_clear_20"></div>
        <div class="wf_funnel_center_align">
          <div class="wf_funnel_h3"><?php esc_html_e( 'Great! The Optin thank you page has been successfully updated', 'funnel-builder' ); ?></div>
        </div>
        <div class="wf_funnel_clear_30"></div>
      </div>
    </div>

  </div>
</div>
