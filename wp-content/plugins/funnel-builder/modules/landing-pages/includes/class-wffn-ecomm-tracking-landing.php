<?php

/**
 * This class take care of ecommerce tracking setup
 * It renders necessary javascript code to fire events as well as creates dynamic data for the tracking
 * @author woofunnels.
 */
class WFFN_Ecomm_Tracking_Landing {
	private static $ins = null;
	private $admin_general_settings;

	public function __construct() {
		add_action( 'wp_head', array( $this, 'render' ), 90 );
		$this->admin_general_settings = BWF_Admin_General_Settings::get_instance();

	}

	public static function get_instance() {
		if ( self::$ins === null ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function render() {
		if ( $this->should_render() ) {
			$this->render_fb();
			$this->render_ga();
		}
	}

	/**
	 * render script to load facebook pixel core js
	 */

	public function render_fb() {
		if ( false !== $this->is_fb_pixel() ) { ?>
			<!-- Facebook Analytics Script Added By WooFunnels -->
			<script>
				!function (f, b, e, v, n, t, s) {
					if (f.fbq) return;
					n = f.fbq = function () {
						n.callMethod ?
							n.callMethod.apply(n, arguments) : n.queue.push(arguments)
					};
					if (!f._fbq) f._fbq = n;
					n.push = n;
					n.loaded = !0;
					n.version = '2.0';
					n.queue = [];
					t = b.createElement(e);
					t.async = !0;
					t.src = v;
					s = b.getElementsByTagName(e)[0];
					s.parentNode.insertBefore(t, s)
				}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
				<?php

				$get_all_fb_pixel = $this->is_fb_pixel();
				$get_each_pixel_id = explode( ',', $get_all_fb_pixel );
				if ( is_array( $get_each_pixel_id ) && count( $get_each_pixel_id ) > 0 ) {
				foreach ( $get_each_pixel_id as $pixel_id ) {
				?>
				fbq('init', '<?php echo esc_js( trim( $pixel_id ) ); ?>');
				<?php
				}
				?>
				<?php esc_js( $this->render_fb_view() ); ?>
				<?php
				}
				?>
			</script>
			<?php
		}
	}

	public function is_fb_pixel() {
		$get_pixel_key = apply_filters( 'bwf_fb_pixel_ids', $this->admin_general_settings->get_option( 'fb_pixel_key' ) );

		return empty( $get_pixel_key ) ? false : $get_pixel_key;
	}

	/**
	 * maybe render script to fire fb pixel view event
	 */
	public function render_fb_view() {
		if ( $this->do_track_fb_view() && WFFN_Core()->landing_pages->is_wflp_page() ) {
			?>
			fbq('track', 'PageView');
			<?php
		}
	}

	/**
	 * maybe render script to fire fb pixel view event
	 */
	public function do_track_fb_view() {
		$fb_tracking = $this->admin_general_settings->get_option( 'is_fb_page_view_lp' );

		if ( is_array( $fb_tracking ) && count( $fb_tracking ) > 0 && 'yes' === $fb_tracking[0] ) {
			return true;
		}

		return false;

	}

	/**
	 * render google analytics core script to load framework
	 */
	public function render_ga() {
		$get_tracking_code = $this->ga_code();
		if ( false === $get_tracking_code ) {
			return;
		}

		$get_tracking_code = explode( ",", $get_tracking_code );
		if ( ( $this->do_track_ga_view() ) && false !== $get_tracking_code ) {
			?>
			<!-- Google Analytics Script Added By WooFunnels -->
			<script>
				(function (i, s, o, g, r, a, m) {
					i['GoogleAnalyticsObject'] = r;
					i[r] = i[r] || function () {
						(i[r].q = i[r].q || []).push(arguments)
					}, i[r].l = 1 * new Date();
					a = s.createElement(o),
						m = s.getElementsByTagName(o)[0];
					a.async = 1;
					a.src = g;
					m.parentNode.insertBefore(a, m)
				})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
				<?php
				$count = false;
				foreach ( $get_tracking_code as $k => $ga_code ) {
					$tracker = ( true === $count ) ? ", 'tracker" . $k . "'" : "";
					echo "ga( 'create', '" . esc_js( trim( $ga_code ) ) . "', 'auto' " . $tracker . " );"; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$count = true;
				}
				?>
                ga('send', 'pageview');
			</script>

			<?php
		}
	}

	public function ga_code() {
		$get_ga_key = apply_filters( 'bwf_get_ga_lp_key', $this->admin_general_settings->get_option( 'ga_key' ) );

		return empty( $get_ga_key ) ? false : $get_ga_key;
	}

	public function do_track_ga_view() {
		$ga_tracking = $this->admin_general_settings->get_option( 'is_ga_page_view_lp' );

		if ( is_array( $ga_tracking ) && count( $ga_tracking ) > 0 && 'yes' === $ga_tracking[0] ) {
			return true;
		}

		return false;
	}

	public function should_render() {
		if ( WFFN_Core()->landing_pages->is_wflp_page() ) {
			return true;
		}

		return false;
	}

}