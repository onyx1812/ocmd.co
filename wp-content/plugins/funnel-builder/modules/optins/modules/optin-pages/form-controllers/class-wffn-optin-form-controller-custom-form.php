<?php

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * This class will control all custom form actions for optin submission
 * Class WFFN_Optin_Form_Controller_Custom_Form
 */
class WFFN_Optin_Form_Controller_Custom_Form extends WFFN_Optin_Form_Controller {

	private static $ins = null;
	public $slug = 'form';

	/**
	 * WFFN_Optin_Form_Controller_Custom_Form constructor.
	 */
	public function __construct() {
		add_shortcode( 'wfop_' . $this->slug, [ $this, 'add_optin_form_shortcode' ] );

		/**
		 * Redirect to next step if wffn_next_link is added in redirection url parameters.
		 */

		/**
		 * handle custom from post actions.
		 */
		add_action( 'wp_ajax_wffn_submit_custom_optin_form', array( $this, 'handle_submission' ) );
		add_action( 'wp_ajax_nopriv_wffn_submit_custom_optin_form', array( $this, 'handle_submission' ) );

		add_action( 'wp_ajax_wffn_frontend_render_form', array( $this, 'frontend_render_form' ) );
		add_action( 'wp_ajax_nopriv_wffn_frontend_render_form', array( $this, 'frontend_render_form' ) );
		add_action( 'wp_footer', array( $this, 'add_optin_form_footer' ) );

		parent::__construct();
	}

	/**
	 * @return WFFN_Optin_Form_Controller_Custom_Form|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	/**
	 * @return string
	 */
	public function get_form_group() {
		return 'custom_form';
	}

	/**
	 * Return title of this form builder controller
	 */
	public function get_title() {
		return __( 'Custom Form', 'funnel-builder' );
	}

	/**
	 * @return string|void
	 */
	public function get_form_shortcode() {
		return '[wffn_optin_' . $this->slug . ']';
	}


	/**
	 * @return bool|false|string
	 */
	public function add_optin_form_shortcode() {
		$optin_page_id = WFOPP_Core()->optin_pages->get_optin_id();

		if ( $optin_page_id > 0 ) {

			$optinPageId = $optin_page_id;
			if ( $optinPageId > 0 && intval( $optinPageId ) === intval( $optin_page_id ) ) {
				$optinFields = WFOPP_Core()->optin_pages->form_builder->get_form_fields( $optin_page_id );

				if ( count( $optinFields ) > 0 ) {
					ob_start();
					echo $this->add_recaptcha_script(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$customizations = WFOPP_Core()->optin_pages->form_builder->get_form_customization_option( 'all', $optinPageId );
					$font_array     = [];
					if ( 'default' !== $customizations['input_font_family'] && 'inherit' !== $customizations['input_font_family'] ) {
						$font_array[] = $customizations['input_font_family'];
					}
					if ( 'default' !== $customizations['button_font_family'] && 'inherit' !== $customizations['button_font_family'] ) {
						$font_array[] = $customizations['button_font_family'];
					}
					if ( ! empty( $font_array ) ) {
						$font_array      = array_unique( $font_array );
						$font_string     = implode( '|', $font_array );
						$google_font_url = "//fonts.googleapis.com/css?family=" . $font_string;
						wp_enqueue_style( 'wfop-google-fonts', esc_url( $google_font_url ), array(), WFFN_VERSION, 'all' );
					}

					$class = '';

					if ( $customizations['show_input_label'] === 'no' ) {
						$class = "wfop_hide_label";
					}

					$get_embed_mode = WFOPP_Core()->optin_pages->get_embed_mode();

					/**
					 * Render popover for the preview mode
					 */
					if ( WFOPP_Core()->optin_pages->form_builder->is_preview ) {
						$this->frontend_render_form( $optinPageId, $get_embed_mode, $class );
					} else {
						//for inline mode, preview OR front
						$this->frontend_render_form( $optinPageId, 'inline', $class );
					}

					return ob_get_clean();
				}
			}
		}

		return false;
	}

	public function add_optin_form_footer() {
        if ( false === WFFN_Common::wffn_is_funnel_pro_active() ) {
            return;
        }
		$optinPageId = WFOPP_Core()->optin_pages->get_optin_id();

		if ( $optinPageId > 0 && WFOPP_Core()->optin_pages->is_wfop_page() ) {
			$get_embed_mode  = 'popover';
			$optinFields     = WFOPP_Core()->optin_pages->form_builder->get_form_fields( $optinPageId );
			$selected_design = WFOPP_Core()->optin_pages->get_page_design( $optinPageId );
			$selected_type   = isset( $selected_design['selected_type'] ) ? $selected_design['selected_type'] : '';
			if ( 'wp_editor' !== $selected_type ) {
				return;
			}

			if ( count( $optinFields ) > 0 ) {
				echo $this->add_recaptcha_script(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$customizations = WFOPP_Core()->optin_pages->form_builder->get_form_customization_option( 'all', $optinPageId );
				$font_array     = [];
				if ( 'default' !== $customizations['input_font_family'] && 'inherit' !== $customizations['input_font_family'] ) {
					$font_array[] = $customizations['input_font_family'];
				}
				if ( 'default' !== $customizations['button_font_family'] && 'inherit' !== $customizations['button_font_family'] ) {
					$font_array[] = $customizations['button_font_family'];
				}
				if ( ! empty( $font_array ) ) {
					$font_array      = array_unique( $font_array );
					$font_string     = implode( '|', $font_array );
					$google_font_url = "//fonts.googleapis.com/css?family=" . $font_string;
					wp_enqueue_style( 'wfop-google-fonts', esc_url( $google_font_url ), array(), WFFN_VERSION, 'all' );
				}

				$class = '';

				if ( $customizations['show_input_label'] === 'no' ) {
					$class = "wfop_hide_label";
				}

				$modal_effect = isset( $customizations['popup_open_animation'] ) ? $customizations['popup_open_animation'] : 'slide-down';
				/**
				 * Render popover front end popup HTML Here
				 */ ?>
                <div class="bwf_pp_overlay bwf_pp_effect_<?php echo esc_attr($modal_effect) ?>">
                    <div class="bwf_pp_wrap">
                        <a class="bwf_pp_close" href="javascript:void(0)">&times;</a>
                        <div class="bwf_pp_cont">
							<?php $this->frontend_render_form( $optinPageId, $get_embed_mode, $class ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

		} else {
			return;
		}
	}

	public function frontend_render_form( $optin_id = 0, $form_mode = 'inline', $class = '' ) {
		$status  = false;
		$optinid = intval( filter_input( INPUT_POST, 'optin_id', FILTER_SANITIZE_NUMBER_INT ) );
		if ( $optinid > 0 ) {
			$optin_id  = $optinid;
			$class     = filter_input( INPUT_POST, 'op_class', FILTER_SANITIZE_STRING );
			$form_mode = filter_input( INPUT_POST, 'op_mode', FILTER_SANITIZE_STRING );
			$status    = true;
		}

		if ( $optin_id <= 0 ) {
			return;
		}

		$optin_settings = WFOPP_Core()->optin_pages->get_optin_form_integration_option( $optin_id );
		$optin_layout   = WFOPP_Core()->optin_pages->form_builder->get_optin_layout( $optin_id );
		$customizations = WFOPP_Core()->optin_pages->form_builder->get_form_customization_option( 'all', $optin_id );
		$this->_output_form( $class, $optin_layout, $optin_id, $optin_settings, $form_mode, $customizations );

		if ( $status ) {
			exit;
		}
	}

	public function _output_form( $class, $optin_layout, $optinPageId, $optin_settings, $form_mode, $customizations ) {
		$submit_btn_text     = isset( $customizations['button_text'] ) ? $customizations['button_text'] : '';
		$subtitle            = isset( $customizations['subtitle'] ) ? $customizations['subtitle'] : '';
		$submitting_btn_text = isset( $customizations['button_submitting_text'] ) ? $customizations['button_submitting_text'] : 'Submitting...';
		$submit_btn_size     = isset( $customizations['button_size'] ) ? $customizations['button_size'] : 'med';
		$field_size          = isset( $customizations['field_size'] ) ? $customizations['field_size'] : 'small';

		$button_args   = array(
			'title'           => $submit_btn_text,
			'submitting_text' => $submitting_btn_text,
			'data-size'       => $submit_btn_size,
			'subtitle'        => $subtitle
		);
		$animate_class = ( isset( $customizations['popup_bar_animation'] ) && 'yes' === $customizations['popup_bar_animation'] ) ? ' bwf_pp_animate' : '';

		if ( 'popover' === $form_mode ) {
			if ( 'enable' === $customizations['popup_bar_pp'] ) { ?>
                <div class="pp-bar-text-wrapper">
                    <span class="pp-bar-text above"><?php echo esc_html( $customizations['popup_bar_text'] ); ?></span>
                </div>
                <div class="bwf_pp_bar_wrap">
                    <div class="bwf_pp_bar<?php echo esc_attr( $animate_class ); ?>" role="progressbar" aria-valuenow="<?php echo esc_attr( $customizations['popup_bar_width'] ); ?>" aria-valuemin="0" aria-valuemax="100">
                        <span class="pp-bar-text inside"><?php echo esc_html( $customizations['popup_bar_text'] ); ?></span>
                    </div>
                </div>
			<?php }
			?>
            <div class="bwf_pp_opt_head"><?php echo esc_html( $customizations['popup_heading'] ); ?></div>
            <div class="bwf_pp_opt_sub_head"><?php echo esc_html( $customizations['popup_sub_heading'] ); ?></div>
			<?php
		}
		?>
        <div class="bwf_clear"></div>
        <div class="wffn-optin-form bwfac_forms_outer <?php echo esc_attr( $class ); ?>" data-field-size="<?php echo esc_attr($field_size) ?>">
            <form class="wffn-custom-optin-from" method="post">
				<?php
				foreach ( $optin_layout as $optin_step => $fields ) { ?>
                    <div class="wfop_section <?php echo esc_attr( $optin_step ); ?>">
						<?php
						foreach ( $fields as $fieldData ) {
							$field_object = WFOPP_Core()->form_fields->get_integration_object( $fieldData['type'] );
							if ( $field_object instanceof WFFN_Optin_Form_Field ) {
								$field_object->load_scripts();
								$field_object->load_style();
								$field_object->get_field_output( $fieldData );
							}
						} ?>
                    </div>
					<?php
				}
				$is_preview = wffn_string_to_bool( filter_input( INPUT_GET, 'preview', FILTER_SANITIZE_STRING ) ); ?>
                <div class="bwfac_form_sec submit_button">
                    <input type="hidden" value="<?php echo esc_attr( is_admin() ) ?>" name="optin_is_admin">
                    <input type="hidden" value="<?php echo esc_attr( wp_doing_ajax() ) ?>" name="optin_is_ajax">
                    <input type="hidden" value="<?php echo esc_attr( $is_preview ) ?>" name="optin_is_preview">
                    <input type="hidden" value="<?php echo esc_attr( $optinPageId ) ?>" name="optin_page_id">
                    <input type="hidden" value="<?php echo esc_attr( $optin_settings['formBuilder'] ) ?>" name="formBuilder">
					<?php $this->wffn_get_button_html( $button_args ); ?>
                </div>
            </form>
			<?php $this->show_integration_form(); ?>

        </div>
		<?php
		if ( 'popover' === $form_mode ) { ?>
            <div class="bwf_pp_footer"><?php echo esc_html( $customizations['popup_footer_text'] ); ?></div>
			<?php
		}
		$customizer_settings = WFOPP_Core()->optin_pages->form_builder->get_form_customization_option( 'all', WFOPP_Core()->optin_pages->get_optin_id() );
		$customizations      = wp_parse_args( $customizations, $customizer_settings );
		include WFOPP_Core()->optin_pages->get_module_path() . '/internal-css.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
	}

	public function show_integration_form() {
		if ( WFOPP_Core()->optin_pages->form_builder->is_preview ) {
			return;
		}
		$optin_page_id = WFOPP_Core()->optin_pages->get_optin_id();
		if ( 'true' !== WFOPP_Core()->optin_pages->get_optin_form_integration_option( $optin_page_id, 'optin_form_enable' ) ) {
			return;
		}

		$this->render_integration_form( $optin_page_id );

	}

	public function add_recaptcha_field() {
		$html       = '';
		$db_options = WFOPP_Core()->optin_pages->get_option();

		if ( WFOPP_Core()->optin_pages->form_builder->is_preview ) {
			return $html;
		}

		if ( ! isset( $db_options['op_recaptcha'] ) && $db_options['op_recaptcha'] !== 'true' ) {
			return $html;
		}

		if ( isset( $db_options['op_recaptcha_site'] ) && $db_options['op_recaptcha_site'] !== '' ) {
			$html .= '<!-- Google reCAPTCHA widget -->';
			$html .= '<div class="g-recaptcha" data-sitekey="' . $db_options['op_recaptcha_site'] . '" data-badge="bottomright" data-size="invisible" data-callback="wffn_captchaResponse"></div>';
			$html .= '<input type="hidden" id="wffn-captcha-response" name="wffn-captcha-response" />';

		}

		return $html;
	}

	public function add_recaptcha_script() {
		$db_options = WFOPP_Core()->optin_pages->get_option();
		if ( ! WFOPP_Core()->optin_pages->form_builder->is_preview && $db_options['op_recaptcha'] && $db_options['op_recaptcha'] === 'true' ) {

			?>
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback" async defer></script> <?php //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
            <script>
                var onloadCallback = function () {
                    grecaptcha.execute();
                };

                function wffn_captchaResponse(response) {
                    document.getElementById('wffn-captcha-response').value = response;
                }
            </script>

			<?php
		}
	}

	/**
	 * Get Redirecting to next step if next link is in URL
	 */
	public function get_redirect_to_next_step( $posted_data ) {
		$current_step = WFFN_Core()->data->get_current_step();

		$step_id         = isset( $current_step['id'] ) ? $current_step['id'] : 0;
		$url_from_filter = '';
		if ( $step_id > 0 ) {
			$url_from_filter = WFFN_Core()->data->get_next_url( $step_id );

			$data = get_post_meta( $step_id, 'wffn_step_custom_settings', true );

			if ( isset( $data['custom_redirect_page'] ) && $data['custom_redirect'] === 'true' ) {
				if ( is_array( $data['custom_redirect_page'] ) && count( $data['custom_redirect_page'] ) > 0 ) {
					$url_from_filter = get_permalink( $data['custom_redirect_page']['id'] );
				}
			}
		}

		if ( ! empty( $url_from_filter ) ) {
			$url_from_filter = add_query_arg( array( 'opid' => $posted_data['opid'] ), $url_from_filter );
		}

		$url_from_filter = apply_filters( 'wffn_optin_redirect', $url_from_filter, $current_step );

		return empty( $url_from_filter ) ? site_url() : $url_from_filter;

	}

	/**
	 *
	 */
	public function handle_submission() {
		$optin_page_id = filter_input( INPUT_POST, 'optin_page_id', FILTER_SANITIZE_NUMBER_INT );
		$posted_data   = $this->get_posted_data( $optin_page_id );
		$response      = $this->wffn_recaptcha_response( $posted_data );
		$result        = [];
		if ( $response['success'] ) {

			WFFN_Core()->logger->log( "Custom form posted data: " . print_r( $posted_data, true ) );  //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

			$field_settings         = [];
			$optin_actions_settings = WFOPP_Core()->optin_actions->get_optin_action_settings( $optin_page_id );
			$result['posted_data']  = $posted_data;
			try {
				$result['posted_data'] = $this->handle_actions( $posted_data, $field_settings, $optin_actions_settings );

			} catch ( Exception $e ) {
				WFFN_Core()->logger->log( "Exception occured during form submission" . print_r( $e, true ) );  //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

			}
			WFFN_Core()->logger->log( "Actions ran successfully" . print_r( $result['posted_data'], true ) );  //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

			do_action( 'wffn_optin_form_submit', $optin_page_id, $result['posted_data'] );

			if ( 'true' === WFOPP_Core()->optin_pages->get_optin_form_integration_option( $optin_page_id, 'optin_form_enable' ) ) {

				$result['mapped'] = [];
				$get_mapping      = WFOPP_Core()->optin_pages->get_optin_form_integration_option( $optin_page_id, 'fields' );
				foreach ( $get_mapping as $key => $map ) {
					if ( ! empty( $map ) ) {

						$get_inputName = $key;
						if ( ! isset( $posted_data[ $get_inputName ] ) ) {
							$get_inputName = str_replace( WFFN_Optin_Pages::FIELD_PREFIX, '', $key );
						}
						if ( isset( $posted_data[ $get_inputName ] ) ) {
							$result['mapped'][ $map ] = $posted_data[ $get_inputName ];
						}

					}

				}
			}
			WFFN_Core()->logger->log( "111Actions ran successfully" );  //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

			$result['next_url'] = $this->get_redirect_to_next_step( $result['posted_data'] );
			WFFN_Core()->logger->log( "returning : " . print_r( $result, true ) );  //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

		}

		wp_send_json( $result );
	}

	public function get_posted_data( $optin_page_id ) {
		$raw_posted_data = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
		$posted          = [];
		$get_fields      = WFOPP_Core()->optin_pages->form_builder->get_form_fields( $optin_page_id );
		foreach ( $get_fields as $field ) {
			$get_field_object = WFOPP_Core()->form_fields->get_integration_object( $field['type'] );
			if ( isset( $get_field_object->is_custom_field ) && $get_field_object->is_custom_field ) {
				$posted[ $field['label'] ] = $get_field_object->get_sanitized_value( $raw_posted_data, $field );
			} else {
				$posted[ $get_field_object::get_slug() ] = $get_field_object->get_sanitized_value( $raw_posted_data, $field );
			}
		}
		if ( isset( $raw_posted_data['wffn-captcha-response'] ) && ! empty( $raw_posted_data['wffn-captcha-response'] ) ) {
			$posted['wffn-captcha-response'] = wffn_clean( $raw_posted_data['wffn-captcha-response'] );
		}
		$posted['optin_page_id'] = $optin_page_id;

		return $posted;
	}

	public function render_integration_form( $id ) {
		$get_integration_options = WFOPP_Core()->optin_pages->get_optin_form_integration_option( $id ); ?>
        <form action="<?php echo esc_url( $get_integration_options['formFields']['form']['action'] ); ?>" method="<?php echo esc_attr( $get_integration_options['formFields']['form']['method'] ); ?>" class="wfop_integration_form" style="display: none !important;">
			<?php
			unset( $get_integration_options['formFields']['form'] );
			unset( $get_integration_options['formFields']['submit'] );
			foreach ( $get_integration_options['formFields'] as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $inp ) {
						switch ( $key ) {
							case 'hidden':
								echo '<input type="hidden" name="' . esc_attr( $inp['name'] ) . '" value="' . esc_attr( $inp['value'] ) . '">';
								break;
							case 'select':
							case 'radio_checkbox':
							case 'textarea':
								echo '';
								break;
							default:
								echo '<input type="' . esc_attr( $inp['type'] ) . '" name="' . esc_attr( $inp['name'] ) . '" value="' . esc_attr( $inp['value'] ) . '">';
						}
					}
				}

			} ?>

        </form>
		<?php
	}

	/**
	 * @param $button_args
	 */
	public function wffn_get_button_html( $button_args ) {
		$args = wp_parse_args( $button_args, $this->get_default_button_args() ); ?>
        <div class="<?php echo esc_attr( $args['wrapper_class'] ) ?>" id="<?php echo esc_attr( $args['wrapper_id'] ) ?>">
			<?php if ( 'button' === $args['type'] ){ ?>
            <button class="<?php echo esc_attr( $args['button_class'] ) ?>" data-subitting-text="<?php echo esc_attr( $args['submitting_text'] ) ?>" type="submit" id="<?php echo esc_attr( $args['button_id'] ) ?>" data-size="<?php echo esc_attr( $args['data-size'] ) ?>">
				<?php } else{ ?>
                <a href="<?php echo esc_url( $args['link'] ) ?>">
					<?php } ?>
                    <span class="<?php echo esc_attr( $args['text_wrapper'] ) ?>">
						<?php if ( $args['show_icon'] ) {
							$this->maybe_get_icon_html( $args );
						} ?>
						<span class="<?php echo esc_attr( $args['title_class'] ) ?>"><?php echo esc_attr( $args['title'] ); ?></span>
					</span>
					<?php if ( ! empty( $args['subtitle'] ) ) { ?>
                        <span class="<?php echo esc_attr( $args['subtitle_class'] ) ?>"><?php echo esc_attr( $args['subtitle'] ); ?></span>
					<?php } ?>
					<?php if ( 'button' === $args['type'] ){ ?>
            </button>
		<?php } else { ?>
            </a>
		<?php } ?>
        </div>
		<?php
	}

	public function get_default_button_args() {
		return apply_filters( 'wffn_general_button_default_args', array(
			'title'                => '',
			'subtitle'             => '',
			'type'                 => 'button',
			'link'                 => '#',
			'submitting_text'      => '',
			'wrapper_class'        => 'bwf-custom-button',
			'wrapper_id'           => 'bwf-custom-button-wrap',
			'text_wrapper'         => 'bwf-text-wrapper',
			'button_class'         => 'wfop_submit_btn',
			'button_id'            => 'wffn_custom_optin_submit',
			'data-size'            => 'normal',
			'show_icon'            => false,
			'icon_container_class' => 'bwf_icon',
			'icon_class'           => '',
			'icon_position'        => '',
			'title_class'          => 'bwf_heading',
			'subtitle_class'       => 'bwf_subheading',
		) );
	}

	/**
	 * @param $args
	 */
	public function maybe_get_icon_html( $args ) { ?>
        <span class="<?php echo esc_attr( $args['icon_container_class'] ) . ' ' . esc_attr( $args['icon_position'] ); ?>">
			<?php if ( class_exists( '\Elementor\Icons_Manager' ) ) {
				\Elementor\Icons_Manager::render_icon( $args['icon_class'], [ 'aria-hidden' => 'true' ] );
			} else { ?>
                <i aria-hidden="true" class="far fa-bell"></i>
			<?php } ?>
		</span>

		<?php
	}
}

if ( class_exists( 'WFOPP_Core' ) ) {
	WFOPP_Core()->form_controllers->register( WFFN_Optin_Form_Controller_Custom_Form::get_instance() );
}
