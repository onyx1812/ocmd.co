<?php

/**
 * Class WFFN_Autonami
 * Class controls rendering and display of Autonami plugin use case
 */
class WFFN_Autonami {
	private static $ins = null;

	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 90 );
	}

	public function register_admin_menu() {
		if ( class_exists( 'BWFAN_Core' ) ) {
			return;
		}

		global $submenu;

		$menu = false;

		if( count($submenu['woofunnels']) > 0 ){
			foreach( $submenu['woofunnels'] as $menus ){
				if( in_array('wfacp-autonami-automations', $menus, true )){
					$menu = true;
					break;
				}
			}
		}

		if( $menu === true ){
			return;
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		/** Autonami plugin doesn't exists */

		add_submenu_page( 'woofunnels', 'Automations', 'Automations', 'manage_options', 'wffn-autonami-automations', [
			$this,
			'admin_page',
		] );

		if ( isset( $_GET['page'] ) && 'wffn-autonami-automations' === $_GET['page'] ) {//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_enqueue_style( 'bwf-wc-style', plugin_dir_url( __FILE__ ) . 'wffn-landing.css', array(), '1.0' );
		}
	}

	public function admin_page() {
		if ( ! isset( $_GET['page'] ) || 'wffn-autonami-automations' !== $_GET['page'] ) {//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		/** Removing admin notices */
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

		$this->output();

		return;
	}

	protected function output() {
		ob_start();
		?>
        <div id="wffn-autonami-automations" class="wrap">
            <div class="bwf-wc-clear bwf-wc-clear-40"></div>
            <div class="bwf-wc-section bwf-wc-center">
                <div class="bwf-wc-h1"><?php esc_html_e('AeroCheckout', 'funnel-builder'); ?> <i class='dashicons dashicons-heart'></i> <?php esc_html_e('Autonami', 'funnel-builder'); ?></div>
                <div class="bwf-wc-p"><?php esc_html_e('Now capture and recover your abandoned carts with Autonami - the free WordPress automation engine! Built by the same folks behind Aero Checkout. You\'ll love all the free features.', 'funnel-builder'); ?>
                </div>
                <div class="bwf-wc-clear bwf-wc-clear-20"></div>
				<?php $this->output_button(); ?>
            </div>
            <div class="bwf-wc-clear-60"></div>
            <div class="bwf-wc-section bwf-wc-sect-middle">
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <img class="bwf-wc-img-m" src="<?php echo esc_url_raw(plugin_dir_url( __FILE__ ) . 'Live-Email-Capturing.jpg'); ?>" alt="Autonami analytics">
                </div>
                <div class="bwf-wc-cont-gap"></div>
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <div class="bwf-wc-h3"><?php esc_html_e('Live Email Capturing', 'funnel-builder'); ?></div>
                    <div class="bwf-wc-p"><?php esc_html_e('The moment a prospect enters their email at the checkout, it gets captured. Works for both - guest and logged-in users.', 'funnel-builder'); ?></div>
                </div>
            </div>
            <div class="bwf-wc-clear-40"></div>
            <div class="bwf-wc-section bwf-wc-sect-middle">
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <div class="bwf-wc-h3"><?php esc_html_e('Build Powerful Automations', 'funnel-builder'); ?></div>
                    <div class="bwf-wc-p"><?php esc_html_e('Create cart recovery sequences with personalized coupon codes and timed delays for maximum impact.', 'funnel-builder'); ?></div>
                </div>
                <div class="bwf-wc-cont-gap"></div>
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <img class="bwf-wc-img-m" src="<?php echo esc_url_raw(plugin_dir_url( __FILE__ ) . 'Build-Powerful-Automations.jpg'); ?>" alt="Autonami analytics">
                </div>
            </div>
            <div class="bwf-wc-clear-40"></div>
            <div class="bwf-wc-section bwf-wc-sect-middle">
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <img class="bwf-wc-img-m" src="<?php echo esc_url_raw(plugin_dir_url( __FILE__ ) . 'Analytics.jpg'); ?>" alt="Autonami analytics">
                </div>
                <div class="bwf-wc-cont-gap"></div>
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <div class="bwf-wc-h3"><?php esc_html_e('View Detailed Analytics', 'funnel-builder'); ?></div>
                    <div class="bwf-wc-p"><?php esc_html_e('Track your abandoned carts, recovered and lost carts to know what\'s working and tweak what\'s not.', 'funnel-builder'); ?></div>
                </div>
            </div>
            <div class="bwf-wc-clear-40"></div>
            <div class="bwf-wc-section bwf-wc-sect-middle">
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <div class="bwf-wc-h3"><?php esc_html_e('Post-Purchase Follow-Ups', 'funnel-builder'); ?></div>
                    <div class="bwf-wc-p"><?php esc_html_e('Don\'t cut the chord post the sale. Reach out with helpful content, cross-sell offers, review requests & more.', 'funnel-builder'); ?></div>
                </div>
                <div class="bwf-wc-cont-gap"></div>
                <div class="bwf-wc-w bwf-wc-cont-half">
                    <img class="bwf-wc-img-m" src="<?php echo esc_url_raw(plugin_dir_url( __FILE__ ) . 'post-purchase.jpg'); ?>" alt="Autonami analytics">
                </div>
            </div>
            <div class="bwf-wc-clear-60"></div>
            <div class="bwf-wc-section bwf-wc-center bwf-wc-section-bg-w">
                <div class="bwf-wc-h1"><?php esc_html_e('Ready to recover the lost revenue?', 'funnel-builder'); ?></div>
                <div class="bwf-wc-p"><?php esc_html_e('Download the free version of Autonami. Import pre-built recipes with a single click. Recover lost revenue on autopilot!', 'funnel-builder'); ?></div>
                <div class="bwf-wc-clear-20"></div>
				<?php $this->output_button(); ?>
            </div>
        </div>
		<?php
		echo ob_get_clean(); //phpcs:ignore
	}

	protected function output_button() {
		$plugin_path = 'wp-marketing-automations/wp-marketing-automations.php';
		if ( $this->autonami_install_check() ) {
			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_path . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin_path );
		} else {
			$activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wp-marketing-automations' ), 'install-plugin_wp-marketing-automations' );
		}
		echo '<a href="' . esc_url_raw( $activation_url ) . '" class="bwf-wc-btn">Activate Autonami</a>';
	}

	protected function autonami_install_check() {

		$path    = 'wp-marketing-automations/wp-marketing-automations.php';
		$plugins = get_plugins();

		return isset( $plugins[ $path ] );
	}
}

WFFN_Autonami::get_instance();