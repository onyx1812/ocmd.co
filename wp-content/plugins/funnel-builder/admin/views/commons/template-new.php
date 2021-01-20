<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff ?>
<div id="wffn_design_container">
	<div class="wffn_tab_container wffn-hide" v-if="'no'==template_active" v-bind:class="'no'==template_active?'wffn-show':''">
		<div class="wffn_tabs_templates_part wf_funnel_tabs_templates_part">
			<div class="wffn_temp_anchor wf_funnel_temp_anchor" v-for="(design_name,type) in design_types" v-if="_.size(designs[type])>0" v-on:click="setTemplateType(type)" v-bind:data-select="(current_template_type==type)?'selected':''">
				<input type="radio" name="wffn_tabs">
				<label> <span>{{design_name}}</span></label>
			</div>
		</div>

		<section id="wffn_content1" class="wffn_tab-content wf_funnel_tab-content" style="display: block" v-for="(templates,type) in designs" v-if="(current_template_type==type) && (_.size(templates)>0)">
			<div class="wffn_filter_container" v-if="(`undefined`!==typeof filters) && _.size(filters)>0 && `wp_editor`!==type">
				<div v-for="(name,i) in filters" :data-filter-type="i" v-bind:class="'wffn_filter_container_inner'+(currentStepsFilter==i?' wffn_selected_filter':'')">
					<div v-on:click="currentStepsFilter = i" class="wffn_template_filters">{{name}}</div>
				</div>
			</div>
			<div class="wffn_pick_template">
				<div v-for="(template,slug) in templates" v-on:data-slug="slug" class="wffn_temp_card wf_funnel_temp_card" v-if="((`undefined`=== typeof currentStepsFilter) ||(`undefined`!==typeof currentStepsFilter) && (currentStepsFilter === 'all' || checkInArray(template.group, currentStepsFilter) != ''))">
					<div class="wffn_template_sec wffn_build_from_scratch" v-if="template.build_from_scratch">
						<div class="wffn_template_sec_design">
							<div class="wffn_temp_overlay">
								<div class="wffn_temp_middle_align">
									<div class="wffn_add_tmp_se">
										<a href="javascript:void(0)" class="wffn_steps_btn_add" v-on:click="triggerImport(template,slug,type,$event)"><span>+</span></a>
									</div>
									<div class="wffn_clear_30"></div>
									<div class="wffn_clear_10"></div>
									<div class="wffn_p wffn_import_template" v-on:click="triggerImport(template,slug,type,$event)">
										<span class="dashicons dashicons-update"></span><span class="wffn_import_text"><?php esc_html_e( 'Build from scratch', 'funnel-builder' ); ?></span><span class="wffn_importing_text"> <?php esc_html_e( 'Importing...', 'funnel-builder' ) ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="wffn_template_sec" v-else>
						<div class="wffn_template_sec_ribbon" v-bind:class="`yes`===template.pro?`wffn-pro`:``" v-if="`yes`===template.pro">{{wffn.i18n.ribbons.pro}}</div>
						<div v-bind:class="(template.is_pro?'wffn_template_sec_design_pro':'wffn_template_sec_design')">   <!-- USE THIS CLASS FOR PRO   and Use This Template btn will be Get Pro -->
							<img v-bind:src="template.thumbnail" class="wffn_img_temp">
							<div class="wffn_tmp_pro_tab"><?php esc_html_e( 'PRO', 'funnel-builder' ) ?></div>
							<div class="wffn_temp_overlay">
								<div class="wffn_temp_middle_align">
									<div class="wffn_pro_template" v-if="`yes`===template.pro&&false===template.license_exist">
										<a class="wffn_btn_white wffn_display_block">{{template.name}}</a>
										<a v-bind:href="template.preview_url" target="_blank" class="wffn_steps_btn wffn_steps_btn_success"><?php esc_html_e( 'Preview', 'funnel-builder' ) ?></a>
										<a target="_blank" href="<?php echo esc_url( 'https://buildwoofunnels.com/exclusive-offer/' ) ?>" class="wffn_steps_btn wf_funnel_btn_danger"><?php esc_html_e( 'Get PRO', 'funnel-builder' ); ?></a>
									</div>
									<div class="wffn_pro_template" v-else>
										<a class="wffn_btn_white wffn_display_block">{{template.name}}</a>
										<a v-bind:href="template.preview_url" target="_blank" class="wffn_steps_btn wffn_steps_btn_success"><?php esc_html_e( 'Preview', 'funnel-builder' ) ?></a>
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
<!------  WITH TEMPLATES  ------->
