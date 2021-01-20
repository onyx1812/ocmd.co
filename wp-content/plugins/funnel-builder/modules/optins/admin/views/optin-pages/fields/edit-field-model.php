<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wfop_izimodal_default iziModal " id="modal-edit-field" aria-hidden="false" aria-labelledby="modal-edit-field" role="dialog" style="background: rgb(239, 239, 239); z-index: 999; border-radius: 3px; overflow: hidden; max-width: 800px;min-height:450px;">
  <div id="edit-field-form" class="wfop_product_swicther_field_wrap">
    <div class="iziModal-header iziModal-noSubtitle" style="background: rgb(109, 190, 69); padding-right: 40px;">
      <h2 class="iziModal-header-title"><?php _e( 'Edit field', 'funnel-builder' ); ?></h2>
      <div class="iziModal-header-buttons">
        <a href="javascript:void(0)" class="iziModal-button iziModal-button-close" data-izimodal-close=""></a>
      </div>
    </div>

    <div class="iziModal-wrap" style="min-height: 390px;">
      <div class="iziModal-content" style="padding: 0px;">
        <div class="sections">
          <form v-on:submit.prevent="onSubmit">
            <div class="wfop_vue_forms">

              <div class="wfop_without_form_generator" v-if="model.field_type==='wfop_wysiwyg'">
	              <?php include __DIR__ . '/html_field.php'; ?>
              </div>

              <div class="" v-else>
                <div class="wfop_edit_field_wrap">
                  <p class="subtitle_wrap" v-if="''!==model_sub_title">{{edit_model_field_label}}<span>{{model_sub_title}}</span></p>
                </div>
                <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
              </div>
              <fieldset>
                <div class="bwf_form_submit">
                  <input type="submit" class="wfop_btn wfop_btn_primary wfop_update_field_btn" value="<?php _e( 'Update', 'funnel-builder' ); ?>"/>
                </div>
              </fieldset>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>