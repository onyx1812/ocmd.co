<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use \Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;

/**
 * Class Elementor_WFTY_Customer_Details_Widget
 */
class Elementor_WFTY_Customer_Details_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wfty-customer-detail';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Customer Details', 'funnel-builder' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wfty-icon-offer_title';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wffn_woo_thankyou' ];
	}


	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$defaults = WFFN_Core()->thank_you_pages->default_shortcode_settings();

		$this->start_controls_section( 'section_button', [
			'label' => __( 'Customer Details', 'funnel-builder' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'customer_details_heading', [
			'label'   => __( 'Heading', 'funnel-builder' ),
			'type'    => Controls_Manager::TEXT,
			'classes' => 'wfty-elementor-heading-text',
			'default' => isset( $defaults['customer_details_heading'] ) ? $defaults['customer_details_heading'] : __( 'Customer Details', 'funnel-builder' )
		] );

		$this->add_control( 'layout_label', [
			'label' => __( 'Layout', 'funnel-builder' ),
			'type'  => Controls_Manager::HEADING,
		] );

		$this->add_responsive_control( 'customer_layout', [
			'label'          => __( 'Structure', 'funnel-builder' ),
			'type'           => Controls_Manager::SELECT,
			'default'        => '50',
			'tablet_default' => '50',
			'mobile_default' => '50',
			'options'        => [
				'50'  => __( 'Two Columns', 'funnel-builder' ),
				'100' => __( 'Full Width', 'funnel-builder' ),
			],
			'selectors'      => [
				'{{WRAPPER}} .elementor-customer-details-wrapper .wfty_customer_info .wfty_2_col_left, {{WRAPPER}} .elementor-customer-details-wrapper .wfty_customer_info .wfty_2_col_right' => 'width: {{value}}%; float: left;padding-right: 15px;',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_customer_heading', [
			'label' => __( 'Heading', 'funnel-builder' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'section_heading', [
			'label' => __( 'Heading', 'funnel-builder' ),
			'type'  => Controls_Manager::HEADING,
		] );

		$this->add_control( 'section_heading_color', [
			'label'     => __( 'Color', 'funnel-builder' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#000000',
			'scheme'    => [
				'type'  => Color::get_type(),
				'value' => Color::COLOR_2,
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-customer-details-wrapper .wfty-customer-info-heading.wfty_title' => 'color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'typography_section_heading',
			'label'          => 'Typography',
			'scheme'         => Typography::TYPOGRAPHY_1,
			'selector'       => '{{WRAPPER}} .elementor-customer-details-wrapper .wfty-customer-info-heading.wfty_title',
			'fields_options' => [
				// first mimic the click on Typography edit icon
				'typography'  => [ 'default' => 'yes' ],
				// then redifine the Elementor defaults
				'font_family' => [ 'default' => 'Open Sans' ],
				'font_size'   => [ 'default' => [ 'size' => 24 ] ],
				'font_weight' => [ 'default' => 600 ],
				'line_height' => [ 'default' => [ 'size' => 1.5, 'unit' => 'em' ] ],
			],
		] );

		$this->add_responsive_control( 'heading_align', [
			'label'     => __( 'Alignment', 'funnel-builder' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'left',
			'options'   => [
				'left'   => [
					'title' => __( 'Left', 'funnel-builder' ),
					'icon'  => 'fa fa-align-left',
				],
				'center' => [
					'title' => __( 'Center', 'funnel-builder' ),
					'icon'  => 'fa fa-align-center',
				],
				'right'  => [
					'title' => __( 'Right', 'funnel-builder' ),
					'icon'  => 'fa fa-align-right',
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-customer-details-wrapper .wfty_title' => 'text-align: {{VALUE}}',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_customer_detail', [
			'label' => __( 'Details', 'funnel-builder' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'heading_label', [
			'label'     => __( 'Heading', 'funnel-builder' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before'
		] );

		$this->add_control( 'heading_color', [
			'label'     => __( 'Color', 'funnel-builder' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#000000',
			'scheme'    => [
				'type'  => Color::get_type(),
				'value' => Color::COLOR_2,
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-customer-details-wrapper .wfty_customer_info .wfty_text_bold strong' => 'color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'typography_heading',
			'label'          => 'Typography',
			'scheme'         => Typography::TYPOGRAPHY_1,
			'selector'       => '{{WRAPPER}} .elementor-customer-details-wrapper .wfty_customer_info .wfty_text_bold strong',
			'fields_options' => [
				// first mimic the click on Typography edit icon
				'typography'  => [ 'default' => 'yes' ],
				// then redifine the Elementor defaults
				'font_family' => [ 'default' => 'Open Sans' ],
				'font_size'   => [ 'default' => [ 'size' => 20 ] ],
				'font_weight' => [ 'default' => 600 ],
				'line_height' => [ 'default' => [ 'size' => 1.5, 'unit' => 'em' ] ],
			],
		] );

		$this->add_control( 'detail_label', [
			'label'     => __( 'Details', 'funnel-builder' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'text_color', [
			'label'     => __( 'Color', 'funnel-builder' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#565656',
			'scheme'    => [
				'type'  => Color::get_type(),
				'value' => Color::COLOR_2,
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table .wfty_wrap .wfty_box.wfty_customer_details_2_col table tr th, {{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table .wfty_wrap .wfty_box.wfty_customer_details_2_col table tr td, {{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table, {{WRAPPER}} .elementor-customer-details-wrapper .wfty_view, {{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table *' => 'color: {{VALUE}}',
				'{{WRAPPER}} .elementor-customer-details-wrapper .wfty_wrap table.my_account_customers td:first-child'                                                                                                                                                                                                                                                                                                                                                                                                            => 'text-align: left; padding-left: 10px;',
				'{{WRAPPER}} .elementor-customer-details-wrapper .wfty_wrap table.my_account_customers .subscription-next-payment'                                                                                                                                                                                                                                                                                                                                                                                                => 'text-align: left; padding: 0 10px;',
                '{{WRAPPER}} .elementor-customer-details-wrapper .wfty_2_col_left:first-child div'                                                                                                                                                                                                                                                                                                                                                                                                                                => 'word-wrap: break-word;',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'typography',
			'label'          => 'Typography',
			'scheme'         => Typography::TYPOGRAPHY_1,
			'selector'       => '{{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table .wfty_wrap .wfty_box.wfty_customer_details_2_col table tr th, {{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table .wfty_wrap .wfty_box.wfty_customer_details_2_col table tr td, {{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table, {{WRAPPER}} .elementor-customer-details-wrapper .wfty_view, {{WRAPPER}} .elementor-customer-details-wrapper .wffn_customer_details_table *',
			'fields_options' => [
				// first mimic the click on Typography edit icon
				'typography'  => [ 'default' => 'yes' ],
				// then redifine the Elementor defaults
				'font_family' => [ 'default' => 'Open Sans' ],
				'font_size'   => [ 'default' => [ 'size' => 16 ] ],
				'font_weight' => [ 'default' => 400 ],
				'line_height' => [ 'default' => [ 'size' => 1.5, 'unit' => 'em' ] ],
			],
		] );

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$heading_text = $settings['customer_details_heading'];
		$this->add_render_attribute( 'wrapper', 'class', 'elementor-customer-details-wrapper' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php
			echo do_shortcode( '[wfty_customer_details customer_details_heading="' . $heading_text . '"]' );
			?>
		</div>
		<?php
	}

}