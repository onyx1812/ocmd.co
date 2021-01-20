<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff: The "WordPress.
/** WITH TEMPLATES */
?>
    <div v-if="isWelcome == true" id="wffn_design_container">
        <input type="hidden" id="new_funnel_name"/>
        <div class="wffn_tab_container">
            <div class="wffn_tabs_templates_part">
                <div class="wffn_temp_anchor" v-for="(design_name,type) in design_types" v-if="_.size(designs[type])>0" v-on:click="setTemplateType(type)" v-bind:data-select="(current_template_type==type)?'selected':''">
                    <input type="radio" name="wffn_tabs">
                    <label> <span>{{design_name}}</span></label>
                </div>
            </div>
            <section id="wffn_content1" class="wffn_tab-content" style="display: block" v-for="(templates,type) in designs" v-if="(current_template_type==type) && (_.size(templates)>0)">

                <div class="wffn_filter_container" v-if="_.size(filters)>0">
                    <div v-for="(name,i) in filters" :data-filter-type="i" v-bind:class="'wffn_filter_container_inner'+(currentStepsFilter==i?' wffn_selected_filter':'')">
                        <div v-on:click="currentStepsFilter = i" class="wffn_template_filters">{{name}}</div>
                    </div>
                </div>

                <div class="wffn_pick_template">

                    <div v-for="(template,slug) in templates" v-on:data-slug="slug" class="wffn_temp_card" v-if="currentStepsFilter === 'all' || checkInArray(template.group, currentStepsFilter) != '' ">

                        <div class="wffn_template_sec wffn_build_from_scratch" v-if="template.build_from_scratch">
                            <div class="wffn_template_sec_design">
                                <div class="wffn_temp_overlay">
                                    <div class="wffn_temp_middle_align">
                                        <div class="wffn_add_tmp_se">

											<?php $starter_link = wp_nonce_url( admin_url( 'admin.php?page=bwf_funnels&action=wffn_maybe_create_starter_steps&funnel_id=' . WFFN_Core()->admin->get_funnel()->get_id() ), 'wffn_maybe_create_starter_steps' ); ?>
                                            <a class="wffn_steps_btn_add" href="javascript:void(0);" v-on:click="createStarter(`<?php echo esc_url( $starter_link ) ?>`,$event)"><span>+</span></a>

                                        </div>
                                        <div class="wffn_clear_30"></div>
                                        <div class="wffn_clear_10"></div>
                                        <div class="wffn_p witha">
											<?php $starter_link = wp_nonce_url( admin_url( 'admin.php?page=bwf_funnels&action=wffn_maybe_create_starter_steps&funnel_id=' . WFFN_Core()->admin->get_funnel()->get_id() ), 'wffn_maybe_create_starter_steps' ); ?>
                                            <a href="javascript:void(0);" v-on:click="createStarter(`<?php echo esc_url( $starter_link ) ?>`,$event)"><?php esc_html_e( 'Create Your Funnel', 'funnel-builder' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wffn_template_sec" v-else>
                            <div class="wffn_template_sec_ribbon" v-bind:class="`yes`===template.pro?`wffn-pro`:``" v-if="`yes`===template.pro">{{getRibbonName(template)}}</div>
                            <div v-bind:class="(template.is_pro?'wffn_template_sec_design_pro':'wffn_template_sec_design')">
                                <!-- USE THIS CLASS FOR PRO   and Use This Template btn will be Get Pro -->
                                <img v-bind:src="template.thumbnail" class="wffn_img_temp">
                                <div class="wffn_tmp_pro_tab"><?php esc_html_e( 'PRO', 'funnel-builder' ) ?></div>
                                <div class="wffn_temp_overlay">
                                    <div class="wffn_temp_middle_align">

                                        <div class="wffn_pro_template" v-if="`yes`===template.pro&&false===template.license_exist">
                                            <a class="wffn_btn_white wffn_display_block">{{template.name}}</a>
                                            <a v-bind:href="template.preview" target="_blank" class="wffn_steps_btn wffn_steps_btn_success"><?php esc_html_e( 'Preview', 'funnel-builder' ) ?></a>
                                            <a href="<?php echo esc_url( 'https://buildwoofunnels.com/exclusive-offer/' ) ?>" class="wffn_steps_btn wf_funnel_btn_danger"><?php esc_html_e( 'Get PRO', 'funnel-builder' ); ?></a>
                                        </div>

                                        <div class="wffn_pro_template" v-else>
											<a class="wffn_btn_white wffn_display_block">{{template.name}}</a>
											<a v-bind:href="template.preview" target="_blank" class="wffn_steps_btn wffn_steps_btn_success"><?php esc_html_e( 'Preview', 'funnel-builder' ) ?></a>
											<a href="javascript:void(0)" class="wffn_steps_btn wffn_import_template wffn_steps_btn_green" v-on:click="triggerImport(template,slug,type,$event)"><span class="dashicons dashicons-update"></span><span class="wffn_import_text"><?php esc_html_e( 'Import', 'funnel-builder' ) ?></span><span class="wffn_importing_text"><?php esc_html_e( 'Importing...', 'funnel-builder' ) ?></span></a>
										</div>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="wffn_clear_20"></div>
                <div class="wffn_clear_20"></div>
            </section>

        </div>
    </div>
<?php

?>
    <div id="wffn_design_welcome_wrap" v-if="isWelcome == false && _.size(funnel_id) != 0">
        <div class="wffn_welcome_wrap">
            <div class="wffn_welcome_wrap_in">
                <div class="wffn_welc_head">
                    <div class="wffn_welc_icon"><img src="<?php echo esc_url( WFFN_Core()->get_plugin_url() ) ?>/admin/assets/img/clap.png" alt="" title=""/></div>
                    <div class="wffn_welc_title"> <?php esc_html_e( 'No steps are setup on this funnel.', 'funnel-builder' ); ?></div>
                </div>
                <div class="wffn_welc_text">
                    <p><?php esc_html_e( 'You can start creating steps by clicking "Add First Step" button.', 'funnel-builder' ); ?></p></div>
                <button v-on:click="openChooseStep()" class="button-primary wffn_funnel_btn_large_settings wffn_welc_btn">
                    <span class="dashicons dashicons-plus"></span> <?php esc_html_e( "Add First Step", 'funnel-builder' ); ?></button>

            </div>
        </div>
    </div>
    <!------  WITH TEMPLATES  ------->
<?php ?>