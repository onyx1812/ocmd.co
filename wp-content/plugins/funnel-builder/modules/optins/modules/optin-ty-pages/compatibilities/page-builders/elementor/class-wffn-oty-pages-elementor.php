<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff

use Elementor\Plugin;

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_OTY_Pages_Elementor
 */
class WFFN_OTY_Pages_Elementor {

	private static $ins = null;
	protected $template_type = [];
	protected $design_template_data = [];
	protected $templates = [];
	private $edit_id = 0;
	private $url = '';

	/**
	 * WFFN_OTY_Pages_Elementor constructor.
	 */
	public function __construct() {
		$this->url = plugin_dir_url( __FILE__ );
		$this->process_url();
		$this->add_default_templates();

		add_filter( 'bwf_page_template', array( $this, 'get_page_template' ) );
	}

	private function process_url() {
		if ( isset( $_REQUEST['page'] ) && 'wf-oty' === $_REQUEST['page'] && isset( $_REQUEST['edit'] ) && $_REQUEST['edit'] > 0 ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->edit_id = absint( $_REQUEST['edit'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	public function add_default_templates() {
		$templates = WooFunnels_Dashboard::get_all_templates();
		$designs   = isset( $templates['optin_ty'] ) ? $templates['optin_ty'] : [];

		$template = [
			'slug'        => 'elementor',
			'title'       => __( 'Elementor', 'funnel-builder' ),
			'button_text' => __( 'Edit', 'funnel-builder' ),
			'edit_url'    => add_query_arg( [
				'post'   => $this->edit_id,
				'action' => 'elementor',
			], admin_url( 'post.php' ) ),
		];
		WFOPP_Core()->optin_ty_pages->register_template_type( $template );

		foreach ( $designs as $d_key => $templates ) {

			if ( is_array( $templates ) ) {
				foreach ( $templates as $temp_key => $temp_val ) {
					if ( isset( $temp_val['pro'] ) && 'yes' === $temp_val['pro'] ) {
						$temp_val['license_exist'] = WFFN_Core()->admin->get_license_status();
					}
					WFOPP_Core()->optin_ty_pages->register_template( $temp_key, $temp_val, $d_key );
				}
			}
		}

		return [];
	}

	/**
	 * @return WFFN_OTY_Pages_Elementor|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}


	/**
	 * Get page template filter callback for elementor preview mode
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
		return plugin_dir_path( WFFN_PLUGIN_FILE ) . '/modules/optin-ty-pages/templates/template-default-boxed.php';
	}

}

WFFN_OTY_Pages_Elementor::get_instance();
