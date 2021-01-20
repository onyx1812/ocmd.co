<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff 

use Elementor\Plugin;

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Funnel Public facing functionality
 * Class WFFN_Public
 */
class WFFN_ThankYou_WC_Pages_Elementor {

	private static $ins = null;
	protected $template_type = [];
	protected $design_template_data = [];
	protected $templates = [];
	private $edit_id = 0;
	private $url = '';

	/**
	 * WFFN_Session constructor..
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->url = plugin_dir_url( __FILE__ );

		$this->process_url();
		$this->add_default_templates();

		add_filter( 'bwf_page_template', array( $this, 'get_page_template' ) );

		/**  Register widget category */
		add_action( 'elementor/elements/categories_registered', array( $this, 'wfty_elementor_category' ) );
		/** Register widgets */
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
		add_action( 'elementor/editor/init', array( $this, 'maybe_setup_wfty_fonts' ), 500 );

		/** show short-code */
		add_action( 'elementor/editor/init', array( $this, 'maybe_register_widget_message' ), 500 );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'maybe_print_shortcodes_helpbox' ) );
	}

	/**
	 * Include fonts
	 */
	public function maybe_setup_wfty_fonts() {
		add_action( 'wp_head', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Adding a new widget category 'Custom'
	 */
	public function wfty_elementor_category() {
        if ( !empty(WFFN_Core()->thank_you_pages->edit_id) && class_exists( '\Elementor\Plugin' ) ) {
            \Elementor\Plugin::instance()->elements_manager->add_category( 'wffn_woo_thankyou', array(
                'title' => __( 'WooFunnels', 'funnel-builder' ),
                'icon'  => 'fa fa-plug',
            ) );
        }
	}

	/**
	 * @throws Exception
	 */
	public function register_widgets() {
		// Include plugin files
		$this->includes();

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_WFTY_Order_Details_Widget() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_WFTY_Customer_Details_Widget() );
	}

	/**
	 * Include widget Files
	 */
	public function includes() {
		require_once( __DIR__ . '/widget/class-elementor-wfty-order-details-widget.php' );
		require_once( __DIR__ . '/widget/class-elementor-wfty-customer-details-widget.php' );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'wffn_woo_thankyou_page', plugin_dir_url( WFTY_PLUGIN_FILE ) . 'assets/css/wffn-woo-thankyou-el-widgets.css', array(), WFFN_VERSION . time(), 'all' );
	}

	private function process_url() {
		if ( isset( $_REQUEST['page'] ) && 'wf-ty' === $_REQUEST['page'] && isset( $_REQUEST['edit'] ) && $_REQUEST['edit'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->edit_id = absint( $_REQUEST['edit'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	public function add_default_templates() {

		$template = [
			'slug'        => 'elementor',
			'title'       => __( 'Elementor', 'funnel-builder' ),
			'button_text' => __( 'Edit', 'funnel-builder' ),
			'edit_url'    => add_query_arg( [
				'post'   => $this->edit_id,
				'action' => 'elementor',
			], admin_url( 'post.php' ) ),
		];
		WFFN_Core()->thank_you_pages->register_template_type( $template );
		$templates = WooFunnels_Dashboard::get_all_templates();
		$designs   = isset( $templates['wc_thankyou'] ) ? $templates['wc_thankyou'] : [];

		foreach ( $designs as $d_key => $templates ) {

			if ( is_array( $templates ) ) {
				foreach ( $templates as $temp_key => $temp_val ) {
					if ( isset( $temp_val['pro'] ) && 'yes' === $temp_val['pro'] ) {
						$temp_val['license_exist'] = WFFN_Core()->admin->get_license_status();
					}
					WFFN_Core()->thank_you_pages->register_template( $temp_key, $temp_val, $d_key );
				}
			}
		}

		return [];
	}

	/**
	 * @return WFFN_ThankYou_WC_Pages_Elementor|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}


	/**
	 * Get page template fiter callback for elementor preview mode
	 *
	 * @param string $template page template.
	 *
	 * @return string
	 */
	public function get_page_template( $template ) {

		if ( is_singular() && defined( 'ELEMENTOR_VERSION' ) && true === Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
			$document = Plugin::$instance->documents->get_doc_for_frontend( get_the_ID() );

			if ( $document ) {
				$template = $document->get_meta( '_wp_page_template' );
			}
		}

		return $template;
	}

	public function maybe_register_widget_message() {
		$id       = \Elementor\Plugin::$instance->editor->get_post_id();
		$get_post = get_post( $id );

		if ( WFFN_Core()->thank_you_pages->get_post_type_slug() === $get_post->post_type ) {
			add_action( 'wp_footer', [ $this, 'print_inline_script' ], 9999 );
		}


	}

	public function print_inline_script() {

		?>
		<script>

			(function ($) {
				"use strict";

				var wftySupportedMergeTagsWidgets =<?php echo wp_json_encode( $this->get_merge_tags_supported_widgets() ); ?>;

				elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
					if (wftySupportedMergeTagsWidgets.indexOf(model.get('widgetType')) === -1) {
						return;
					}
					var html = '\t\t\t<div class="wfty-el-customize-note">\n' +
						'\t\t\t\t\t\t\n' +
						'\t\t<div class="elementor-panel-alert elementor-panel-alert-info"><?php esc_html_e( 'You can also add personalization tags to this element using shortcodes. ', 'funnel-builder' ); ?><a style="text-decoration: underline;" onclick="wfty_show_tb(\'WooFunnels Shortcodes\', \'wfty_shortcode_help_box\');" href="javascript:void(0)"><?php esc_html_e( 'Click here to show the available shortcodes', 'funnel-builder' ); ?></a> </div>\n'
						'\t\t\t\t\t</div>\n' +
						'\t\t';
					$(".elementor-panel-navigation").eq(0).after(html);
				});

			})(jQuery);
		</script>
		<?php
	}

	public function get_merge_tags_supported_widgets() {
		return [ 'heading', 'text-editor', 'shortcode', 'wfty-customer-detail', 'wfty-order-detail', 'wfty-order-download' ];
	}

	public function maybe_print_shortcodes_helpbox() {
		include_once WFFN_Core()->thank_you_pages->get_module_path() . '/includes/help-shortcodes.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
	}


}

WFFN_ThankYou_WC_Pages_Elementor::get_instance();
