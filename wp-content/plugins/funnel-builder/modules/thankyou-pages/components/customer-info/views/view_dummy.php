<?php
defined( 'ABSPATH' ) || exit;
$order_id                 = ( $this->order instanceof WC_Order ) ? $this->order->get_id() : 0; //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$customer_layout          = WFFN_Core()->thank_you_pages->get_optionsShortCode( 'layout_settings', $order_id );
$customer_layout          = ( '2c' === $customer_layout ) ? '' : 'wfty_full_width';
$customer_details_heading = isset( $this->data['customer_details_heading'] ) ? $this->data['customer_details_heading'] : __( 'Customer Details', 'funnel-builder' ); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
?>
<div class="wfty_box wfty_customer_info">
	<div class="wfty-customer-info-heading wfty_title"><?php echo esc_html( $customer_details_heading ); ?></div>
	<div class="wfty_content wfty_clearfix wfty_text <?php echo esc_attr($customer_layout) ?>">
		<?php
		echo '<div class="wfty_2_col_left">';
		if ( ! empty( $dummy_data['email'] ) ) {
			echo '<div class="wfty_text_bold"><strong>' . esc_attr( 'Email', 'funnel-builder' ) . '</strong></div>';
			echo '<div class="wfty_view">' . esc_html( $dummy_data['email'] ) . '</div>'; //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		}
		echo '</div>';
		echo '<div class="wfty_2_col_right">';
		if ( ! empty( $dummy_data['phone'] ) ) {
			echo '<div class="wfty_text_bold"><strong>' . esc_attr( 'Phone', 'funnel-builder' ) . '</strong></div>';
			echo '<div class="wfty_view">' . esc_html( $dummy_data['phone'] ) . '</div>'; //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		}
		echo '</div>';
		echo '<div class="wfty_clear_15"></div>';

		/** check if only billing */
		if ( ! empty( $billing_address ) ) {
			?>
			<div class="wfty_2_col_left">
				<div class="wfty_text">
					<div class="wfty_text_bold"><strong><?php esc_attr_e( 'Billing address', 'woocommerce' ); ?></strong></div>
					<div class="wfty_view">
						<?php echo wp_kses_post( $billing_address ); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable ?>
					</div>
				</div>
			</div>
			<?php
		}

		/** show shipping address */
		$shipping_option = get_option( 'woocommerce_ship_to_countries' );
		if ( 'disabled' !== $shipping_option && !empty( $shipping_address ) ) {
			$extra_class = ( empty( !$shipping_address ) ) ? 'wfty_2_col_left' : 'wfty_2_col_right';
			?>
			<div class="<?php echo esc_attr( $extra_class ); ?>">
				<div class="wfty_text">
					<div class="wfty_text_bold"><strong><?php esc_attr_e( 'Shipping address', 'woocommerce' ); ?></strong></div>
					<div class="wfty_view">
						<?php echo wp_kses_post( $shipping_address ); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable ?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<div class="wfty_clear"></div>
	</div>
</div>
