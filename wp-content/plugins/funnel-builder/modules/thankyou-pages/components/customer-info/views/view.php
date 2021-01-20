<?php
defined( 'ABSPATH' ) || exit;

$billing_email = $this->order->get_billing_email(); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$billing_phone = $this->order->get_billing_phone(); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

$customer_layout = WFFN_Core()->thank_you_pages->get_optionsShortCode( 'layout_settings', $this->order->get_id() ); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$customer_layout = ( '2c' === $customer_layout ) ? '' : 'wfty_full_width'; //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$customer_details_heading = isset( $this->data['customer_details_heading'] ) ? $this->data['customer_details_heading'] : __( 'Customer Details', 'funnel-builder' ); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
?>
<div class="wfty_box wfty_customer_info">
	<div class="wfty-customer-info-heading wfty_title"><?php echo esc_html( $customer_details_heading ); ?></div>
	<div class="wfty_content wfty_clearfix wfty_text <?php echo esc_attr($customer_layout) ?>">
		<?php
		echo '<div class="wfty_2_col_left">';
		if ( ! empty( $billing_email ) ) {
			echo '<div class="wfty_text_bold"><strong>' . esc_attr( 'Email', 'funnel-builder' ) . '</strong></div>';
			echo '<div class="wfty_view">' . esc_html( $billing_email ) . '</div>';
		}
		echo '</div>';
		echo '<div class="wfty_2_col_right">';
		if ( ! empty( $billing_phone ) ) {
			echo '<div class="wfty_text_bold"><strong>' . esc_attr( 'Phone', 'funnel-builder' ) . '</strong></div>';
			echo '<div class="wfty_view">' . esc_html( $billing_phone ) . '</div>';
		}
		echo '</div>';
		echo '<div class="wfty_clear_15"></div>';
		$billing_address     = $this->order->get_formatted_billing_address(); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		$billing_address_raw = $this->order->get_address(); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

		/** check if only billing */
		if ( ! empty( $billing_address ) ) {
			?>
			<div class="wfty_2_col_left">
				<div class="wfty_text">
					<div class="wfty_text_bold"><strong><?php esc_attr_e( 'Billing address', 'woocommerce' ); ?></strong></div>
					<div class="wfty_view">
						<?php
						echo wp_kses_post( $billing_address );
						?>
					</div>
				</div>
			</div>
			<?php
		}

		/** show shipping address */
		$shipping_address     = $this->order->get_formatted_shipping_address(); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		$shipping_address_raw = $this->order->get_address( 'shipping' ); //phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

		$shipping_option = get_option( 'woocommerce_ship_to_countries' );

		if ( 'disabled' !== $shipping_option && ! empty( $shipping_address ) ) {
			$extra_class = ( empty( $billing_address ) ) ? 'wfty_2_col_left' : 'wfty_2_col_right';
			?>
			<div class="<?php echo esc_attr( $extra_class ); ?>">
				<div class="wfty_text">
					<div class="wfty_text_bold"><strong><?php esc_attr_e( 'Shipping address', 'woocommerce' ); ?></strong></div>
					<div class="wfty_view">
						<?php
						echo wp_kses_post( $shipping_address );
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<div class="wfty_clear"></div>
	</div>
</div>
