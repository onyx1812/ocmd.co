<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff ?>
<div class="wfacp_template_preview_container">
    <div class="wf_funnel_templates_outer wffn-hide" v-if="'yes'==template_active" v-if="'yes'==template_active" v-bind:class="'yes'==template_active?'wffn-show':''">
        <div class="wf_funnel_heading_choosen_template">
            <div class="wf_funnel_clear_20"></div>
			<?php esc_html_e( 'Customizing', 'funnel-builder' ); ?>
            <b class="wffn_page_title">
				<?php echo esc_html( get_the_title( $admin_instance->edit_id ) ); //phpcs:ignore ?>
            </b>
			<?php esc_html_e( ' Using ', 'funnel-builder' ); ?>
            <b v-html="get_builder_title()"></b> <?php //phpcs:ignore WordPressVIPMinimum.Security.Vuejs.RawHTMLDirectiveFound ?>
            <div class="wf_funnel_clear_20"></div>
        </div>
        <div class="wf_funnel_clear_40"></div>
        <div class="wf_funnel_clear_10"></div>
        <div class="wf_funnel_templates_inner wf_funnel_selected_designed">
            <div class="wf_funnel_templates_design wf_funnel_center_align" v-if="selected_template.build_from_scratch">
                <div class="wf_funnel_temp_card">
                    <div class="wf_funnel_template_sec wf_funnel_build_from_scratch">
                        <div class="wf_funnel_template_sec_design">
                            <div class="wf_funnel_temp_overlay">
                                <div class="wf_funnel_temp_middle_align">

                                    <div v-if="selected_template.build_from_scratch" class="wffn_p"><?php echo esc_html__('Build from scratch', 'funnel-builder'); ?></div>
                                    <div v-else class="wffn_p">{{selected_template.name}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wf_funnel_templates_design wf_funnel_center_align" v-else>
                <div class="wf_funnel_img" style="position: relative">
                    <div class="wf_funnel_template_importing_loader" style="display: none">
                        <span class="spinner"></span>
                    </div>
                    <div>
                        <img v-bind:src="selected_template.thumbnail">
                    </div>
                </div>
            </div>
            <div class="wf_funnel_templates_action wf_funnel_center_align">
                <div class="wf_funnel_clear_5"></div>
                <div v-if="selected_template.build_from_scratch" class="wf_funnel_temp"><b><?php echo esc_html__( 'Template', 'funnel-builder' ) ?>: </b> <?php echo esc_html__('Build from scratch', 'funnel-builder'); ?></div>
                <div  v-else class="wf_funnel_temp"><b><?php esc_html_e( 'Template', 'funnel-builder' ) ?>: </b> {{selected_template.name}}</div>
                <div class="wf_funnel_clear_20"></div>
                <a v-if="'embed_forms'!==selected_template.template_type" target="_blank" class="wf_funnel_btn_temp wf_funnel_btn_blue_temp" v-bind:href="get_edit_link()" v-html="get_button_text()"></a> <?php //phpcs:ignore WordPressVIPMinimum.Security.Vuejs.RawHTMLDirectiveFound ?>
                <div class="wf_funnel_clear_10"></div>
                <a target="_blank" v-bind:href="<?php echo esc_attr($identifier_variable); //phpcs:ignore ?>.view_url" class="wf_funnel_btn_temp wf_funnel_btn_green_temp"><?php esc_html_e( 'Preview', 'funnel-builder' ) ?></a>
            </div>
            <div class="wf_funnel_clear_30"></div>
        </div>
        <div class="clear"></div>
        <div class="wf_funnel_template_bottom">
            <div class="wf_funnel_clear_20"></div>
            <div class="wf_funnel_edit_post_links">
                <a class="wf_funnel_link wf_funnel_blue_link" href=" <?php echo esc_url( admin_url( 'post.php?post=' . $admin_instance->get_edit_id() . '&action=edit' ) ); //phpcs:ignore ?>">
                    <span class="dashicons dashicons-wordpress"></span>
					<?php esc_html_e( ' Switch to Editor', 'funnel-builder' ) ?>
                </a>
            </div>
            <div class="wf_funnel_template_links">
                <a href="javascript:void(0)" class="wf_funnel_link wf_funnel_red_link" v-on:click="get_remove_template()"><?php esc_html_e( 'Remove', 'funnel-builder' ) ?></a>
            </div>
            <div class="wf_funnel_clear_20"></div>
        </div>
		<?php do_action( 'wf_funnel_builder_design_after_template' ); ?>
    </div>
    <!------  WITHOUT TEMPLATES  ------->
    <div class="wf_funnel_clear_40"></div>
    <div class="wf_funnel_templates_outer wffn-hide" v-bind:class="'yes'==template_active?'wffn-show':''">
        <?php WFFN_Core()->admin->get_template_helper_settings_html($admin_instance); //phpcs:ignore ?>
    </div>
</div>
