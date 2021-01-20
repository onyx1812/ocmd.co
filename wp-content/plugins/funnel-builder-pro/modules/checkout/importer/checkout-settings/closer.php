<?php
$product_field  = WFACP_Common::get_product_field();
$advanced_field = WFACP_Common::get_advanced_fields();
$settings       = [
	'show_on_next_step' => [
		'single_step' => [
			'billing_email'       => 'false',
			'billing_first_name'  => 'false',
			'billing_last_name'   => 'false',
			'address'             => 'false',
			'shipping-address'    => 'false',
			'billing_phone'       => 'false',
			'shipping_calculator' => 'false',
		],
	],
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
	'steps'                       => $steps,
	'fieldsets'                   => [
		'single_step' => [
			[
				'name'        => __( '', 'woofunnels-aero-checkout' ),
				'class'       => '',
				'sub_heading' => '',
				'fields'      => [
					[
						'label'        => __( 'Email', 'woocommerce' ),
						'required'     => 'true',
						'type'         => 'email',
						'class'        => [ 'form-row-wide', ],
						'validate'     => [ 'email', ],
						'autocomplete' => 'email username',
						'priority'     => '110',
						'id'           => 'billing_email',
						'field_type'   => 'billing',
						'placeholder'  => __( 'john.doe@example.com ', 'woofunnels-aero-checkout' ),
					],
					[
						'label'        => __( 'First name', 'woocommerce' ),
						'required'     => 'true',
						'class'        => [ 'form-row-first', ],
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
						'class'        => [ 'form-row-last', ],
						'autocomplete' => 'family-name',
						'priority'     => '20',
						'type'         => 'text',
						'id'           => 'billing_last_name',
						'field_type'   => 'billing',
						'placeholder'  => __( 'Doe', 'woofunnels-aero-checkout' ),
					],
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
				'name'        => __( 'Shipping Address', 'woofunnels-aero-checkout' ),
				'class'       => '',
				'sub_heading' => '',
				'fields'      => [
					WFACP_Common::get_single_address_fields( 'shipping' ),
					WFACP_Common::get_single_address_fields(),
				],
			],
			[
				'name'        => __( 'Order Summary', 'woofunnels-aero-checkout' ),
				'class'       => 'wfacp_order_summary_box',
				'sub_heading' => '',
				'html_fields' => [
					'order_coupon'  => 'true',
					'order_summary' => 'true',
				],
				'fields'      => [
					isset( $advanced_field['shipping_calculator'] ) ? $advanced_field['shipping_calculator'] : [],
					$advanced_field['order_coupon'],
					$advanced_field['order_total'] = [
						'type'        => 'wfacp_html',
						'field_type'  => 'advanced',
						'class'       => [ 'wfacp_order_total' ],
						'default'     => false,
						'id'          => 'order_total',
						'html_fields' => [
							'order_total' => true
						],
						'label'       => __( 'Order Total', 'woofunnels-aero-checkout' ),
					],
				],
			],
		],
	],
	'product_settings'            => [
		'coupons'                             => '',
		'enable_coupon'                       => 'false',
		'disable_coupon'                      => 'false',
		'hide_quantity_switcher'              => 'true',
		'enable_delete_item'                  => 'false',
		'hide_product_image'                  => 'false',
		'is_hide_additional_information'      => 'true',
		'additional_information_title'        => WFACP_Common::get_default_additional_information_title(),
		'hide_quick_view'                     => 'true',
		'hide_you_save'                       => 'true',
		'hide_best_value'                     => 'false',
		'best_value_product'                  => '',
		'best_value_text'                     => 'Best Value',
		'best_value_position'                 => 'above',
		'enable_custom_name_in_order_summary' => 'false',
		'autocomplete_enable'                 => 'false',
		'autocomplete_google_key'             => '',
		'preferred_countries_enable'          => 'false',
		'preferred_countries'                 => '',
		'product_switcher_template'           => 'default',
	],
	'have_coupon_field'           => 'true',
	'have_billing_address'        => 'true',
	'have_shipping_address'       => 'true',
	'have_billing_address_index'  => '6',
	'have_shipping_address_index' => '5',
	'enabled_product_switching'   => 'no',
	'have_shipping_method'        => 'true',
	'current_step'                => 'single_step',
];

return [ 'page_layout' => $pageLayout, 'page_settings' => $settings ];
