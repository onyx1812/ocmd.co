<?php
$product_field   = WFACP_Common::get_product_field();
$advanced_field  = WFACP_Common::get_advanced_fields();
$customizer_data = [
	'wfacp_form' => [
		'wfacp_form_section_embed_forms_2_disable_steps_bar'        => 'true',
		'wfacp_form_section_embed_forms_2_step_form_max_width'      => '640',
		'wfacp_form_section_embed_forms_2_form_border_type'         => 'none',
		'wfacp_form_form_fields_1_embed_forms_2_billing_address_1'  => 'wfacp-col-left-half',
		'wfacp_form_form_fields_1_embed_forms_2_billing_city'       => 'wfacp-col-left-half',
		'wfacp_form_form_fields_1_embed_forms_2_billing_postcode'   => 'wfacp-col-left-third',
		'wfacp_form_form_fields_1_embed_forms_2_billing_country'    => 'wfacp-col-left-third',
		'wfacp_form_form_fields_1_embed_forms_2_billing_state'      => 'wfacp-col-left-third',
		'wfacp_form_form_fields_1_embed_forms_2_shipping_address_1' => 'wfacp-col-left-half',
		'wfacp_form_form_fields_1_embed_forms_2_shipping_city'      => 'wfacp-col-left-half',
		'wfacp_form_form_fields_1_embed_forms_2_shipping_postcode'  => 'wfacp-col-left-third',
		'wfacp_form_form_fields_1_embed_forms_2_shipping_country'   => 'wfacp-col-left-third',
		'wfacp_form_form_fields_1_embed_forms_2_shipping_state'     => 'wfacp-col-left-third',
		'wfacp_form_form_fields_1_embed_forms_2_billing_first_name' => 'wfacp-col-left-half',
		'wfacp_form_form_fields_1_embed_forms_2_billing_last_name'  => 'wfacp-col-left-half',
		'wfacp_form_section_embed_forms_2_sec_heading_color'        => '#333',
		'wfacp_form_section_embed_forms_2_heading_fs'               => 20
	]
];


$steps = [
	'single_step' => [
		'name'          => __( 'Step 1', 'woofunnels-aero-checkout' ),
		'slug'          => 'single_step',
		'friendly_name' => __( 'Single Step Checkout', 'woofunnels-aero-checkout' ),
		'active'        => 'yes',
	],
	'two_step'    => [
		'name'          => __( 'Step 2', 'woofunnels-aero-checkout' ),
		'slug'          => 'two_step',
		'friendly_name' => __( 'Two Step Checkout', 'woofunnels-aero-checkout' ),
		'active'        => 'no',
	],
	'third_step'  => [
		'name'          => __( 'Step 3', 'woofunnels-aero-checkout' ),
		'slug'          => 'third_step',
		'friendly_name' => __( 'Three Step Checkout', 'woofunnels-aero-checkout' ),
		'active'        => 'no',
	],
];

$pageLayout = [
	'steps'                       => WFACP_Common::get_default_steps_fields(),
	'fieldsets'                   => [
		'single_step' => [
			0 => [
				'name'        => __( 'Customer Information', 'woofunnels-aero-checkout' ),
				'class'       => '',
				'sub_heading' => '',
				'fields'      => [
					[
						'label'        => __( 'Email', 'woocommerce' ),
						'required'     => 'true',
						'type'         => 'email',
						'class'        => [ 0 => 'form-row-wide', ],
						'validate'     => [ 0 => 'email', ],
						'autocomplete' => 'email username',
						'priority'     => '110',
						'id'           => 'billing_email',
						'field_type'   => 'billing',
						'placeholder'  => __( 'john.doe@example.com ', 'woofunnels-aero-checkout' ),
					],
					[
						'label'        => __( 'First name', 'woocommerce' ),
						'required'     => 'true',
						'class'        => [ 0 => 'form-row-first', ],
						'autocomplete' => 'given-name',
						'priority'     => '10',
						'type'         => 'text',
						'id'           => 'billing_first_name',
						'field_type'   => 'billing',
						'placeholder'  => __( 'John', 'woofunnels-aero-checkout' ),
					],
					[
						'label'        => __( 'Last name', 'woocommerce' ),
						'required'     => 'true',
						'class'        => [ 0 => 'form-row-last', ],
						'autocomplete' => 'family-name',
						'priority'     => '20',
						'type'         => 'text',
						'id'           => 'billing_last_name',
						'field_type'   => 'billing',
						'placeholder'  => __( 'Doe', 'woofunnels-aero-checkout' ),
					],
					WFACP_Common::get_single_address_fields(),
					WFACP_Common::get_single_address_fields( 'shipping' ),
					[
						'label'        => __( 'Phone', 'woocommerce' ),
						'type'         => 'tel',
						'class'        => [ 'form-row-wide' ],
						'id'           => 'billing_phone',
						'field_type'   => 'billing',
						'validate'     => [ 'phone' ],
						'placeholder'  => '999-999-9999',
						'autocomplete' => 'tel',
						'priority'     => 100,
					],
				],
			],
			[
				'name'        => __( 'Shipping Method', 'woofunnels-aero-checkout' ),
				'class'       => '',
				'sub_heading' => '',
				'html_fields' => [ 'shipping_calculator' => true ],
				'fields'      => [
					isset( $advanced_field['shipping_calculator'] ) ? $advanced_field['shipping_calculator'] : []
				],
			],
			[
				'name'        => __( 'Order Summary', 'woofunnels-aero-checkout' ),
				'class'       => 'wfacp_order_summary_box',
				'sub_heading' => '',
				'html_fields' => [
					'order_summary' => 'true',
				],
				'fields'      => [
					$advanced_field['order_summary'],
				],
			],


		],
	],
	'product_settings'            => [
		'coupons'                        => '',
		'enable_coupon'                  => 'false',
		'disable_coupon'                 => 'false',
		'hide_quantity_switcher'         => 'false',
		'enable_delete_item'             => 'false',
		'hide_product_image'             => 'false',
		'is_hide_additional_information' => 'true',
		'autocomplete_google_key'        => '',
		'preferred_countries_enable'     => 'false',
		'preferred_countries'            => '',
		'product_switcher_template'      => 'default',
	],
	'have_billing_address'        => 'true',
	'have_shipping_address'       => 'true',
	'have_shipping_method'        => 'true',
	'have_billing_address_index'  => '6',
	'have_shipping_address_index' => '7',
	'current_step'                => 'single_step',
];

return [
	'default_customizer_value' => $customizer_data,
	'page_layout'              => $pageLayout,
];
