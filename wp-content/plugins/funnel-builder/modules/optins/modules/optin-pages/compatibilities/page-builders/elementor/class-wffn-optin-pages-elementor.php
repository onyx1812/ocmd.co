<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff

use Elementor\Plugin;

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_Optin_Pages_Elementor
 */
class WFFN_Optin_Pages_Elementor {

	private static $ins = null;
	protected $template_type = [];
	protected $design_template_data = [];
	protected $templates = [];
	private $edit_id = 0;
	private $url = '';

	/**
	 * WFFN_Optin_Pages_Elementor constructor.
	 */
	public function __construct() {
		$this->url = plugin_dir_url( WFOPP_PLUGIN_FILE );
		$this->process_url();
		$this->add_default_templates();

		add_filter( 'bwf_page_template', array( $this, 'get_page_template' ) );

		/**  Register widget category */
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_wffn_elementor_category' ) );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
	}

	private function process_url() {
		if ( isset( $_REQUEST['page'] ) && 'wf-op' === $_REQUEST['page'] && isset( $_REQUEST['edit'] ) && $_REQUEST['edit'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->edit_id = absint( $_REQUEST['edit'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		if ( isset( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && isset( $_REQUEST['post'] ) && $_REQUEST['post'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->edit_id = absint( $_REQUEST['post'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	public function add_default_templates() {
		$templates = WooFunnels_Dashboard::get_all_templates();
		$designs   = isset( $templates['optin'] ) ? $templates['optin'] : [];
		$template  = [
			'slug'        => 'elementor',
			'title'       => __( 'Elementor', 'funnel-builder' ),
			'button_text' => __( 'Edit', 'funnel-builder' ),
			'edit_url'    => add_query_arg( [
				'post'   => $this->edit_id,
				'action' => 'elementor',
			], admin_url( 'post.php' ) ),
		];
		WFOPP_Core()->optin_pages->register_template_type( $template );

		foreach ( $designs as $d_key => $templates ) {

			if ( is_array( $templates ) ) {
				foreach ( $templates as $temp_key => $temp_val ) {
					if ( isset( $temp_val['pro'] ) && 'yes' === $temp_val['pro'] ) {
						$temp_val['license_exist'] = WFFN_Core()->admin->get_license_status();
					}
					WFOPP_Core()->optin_pages->register_template( $temp_key, $temp_val, $d_key );
				}
			}
		}

		return [];
	}

	/**
	 * @return WFFN_Optin_Pages_Elementor|null
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

	public function get_module_path() {
		return plugin_dir_path( WFFN_PLUGIN_FILE ) . '/modules/optin-pages/templates/template-default-boxed.php';
	}

	/**
	 * Adding a new widget category 'Flex Funnels'
	 */
	public function add_wffn_elementor_category() {
		$design = WFOPP_Core()->optin_pages->get_page_design( WFOPP_Core()->optin_pages->edit_id );
		if ( 'elementor' === $design['selected_type'] && class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::instance()->elements_manager->add_category( 'wffn-flex', array(
				'title' => __( 'WooFunnels', 'funnel-builder' ),
				'icon'  => 'fa fa-plug',
			) );
		}
	}

	public function register_widgets() {
		$optinPageId = $this->edit_id;

		if ( $optinPageId < 1 && function_exists( 'WFOPP_Core' ) ) {
			$optinPageId = WFOPP_Core()->optin_pages->get_optin_id();
		}
		if ( $optinPageId < 1 && function_exists( 'get_the_ID' ) ) {
			$optinPageId = get_the_ID();
		}

        if ( WFOPP_Core()->optin_pages->get_post_type_slug() === get_post_type( $optinPageId ) ) {

            require_once( __DIR__ . '/widgets/class-elementor-wffn-optin-form-widget.php' );
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_WFFN_Optin_Form_Widget() );
            do_action( 'wffn_optin_elementor_lite_loaded' );
        }
	}

	public function register_scripts() {
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
			$post_id = get_the_ID();
			if ( WFOPP_Core()->optin_pages->get_post_type_slug() === get_post_type( $post_id ) ) {
				wp_enqueue_script( 'phone_flag_intl', WFOPP_Core()->optin_pages->url . 'assets/phone/js/intltelinput.min.js', array(), WFFN_VERSION_DEV );
				wp_enqueue_style( 'flag_style', WFOPP_Core()->optin_pages->url . 'assets/phone/css/phone-flag.css', array(), WFFN_VERSION_DEV );

			}

		}
	}


}

WFFN_Optin_Pages_Elementor::get_instance();
