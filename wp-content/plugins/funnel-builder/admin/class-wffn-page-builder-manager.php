<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFFN_Page_Builder_Manager
 * Handles All the methods about page builder activities
 */
class WFFN_Page_Builder_Manager {

	private static $ins = null;
	private $funnel = null;
	private $installed_plugins = null;

	public function __construct() {

	}

	/**
	 * @return WFFN_Page_Builder_Manager|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function localize_page_builder_texts() {
		$get_all_opted_page_builders = WFFN_Core()->admin->get_all_active_page_builders();
		$pageBuildersTexts           = [];
		if ( empty( $get_all_opted_page_builders ) ) {
			return $pageBuildersTexts;
		}


		foreach ( $get_all_opted_page_builders as $builder ) {
			$page_builder  = $this->get_dependent_plugins_for_page_builder( $builder );
			$plugin_string = sprintf( __( 'We are unable to find %s installed/ activated on your setup. ', 'funnel-builder' ), esc_html( $page_builder['title'] ) );
			$button_text   = __( 'Activate the plugin', 'funnel-builder' );
			$no_install    = 'no';
			$title         = __( 'Sorry, we cannot import this template!', 'funnel-builder' );
			$install_fail  = sprintf( __( 'We are unable to install the %s.', 'funnel-builder' ), esc_html( $page_builder['title'] ) );
			$activate_fail = sprintf( __( 'We are unable to activate the %s.', 'funnel-builder' ), esc_html( $page_builder['title'] ) );

			/**
			 * If its a divi builder we need to handle few cases down there for best user experience
			 */
			if ( 'divi' === $builder ) {

				$theme_status  = $page_builder['theme-status'];
				$plugin_status = $page_builder['plugin-status'];
				$button_text   = 'Close';
				$no_install    = 'yes';

				/**
				 * If theme is not activated and plugin is installed but deactivated, we will try to activate plugin and import template
				 */
				if ( 'activated' !== $theme_status && 'activate' === $plugin_status ) {
					$plugin_string = sprintf( __( 'Please click the below button to install/ activate %s first and then import the template.', 'funnel-builder' ), esc_html( $page_builder['title'] ) );
					$button_text   = __( 'Activate the plugin', 'funnel-builder' );
					$no_install    = 'no';
				} elseif ( 'activated' === $theme_status ) { //If divi theme is activated, no need to find plugin or its status but import the template
					$title         = __( 'Are you sure you want to import this template?', 'funnel-builder' );
					$plugin_string = __( 'It will take around 5-10 seconds to import this template in your store.', 'funnel-builder' );
					$button_text   = __( 'Yes, Import this template!', 'funnel-builder' );
				}
			} else {
				$plugin_string .= sprintf( __( 'Please click on the below button to install/ activate %s ', 'funnel-builder' ), esc_html( $page_builder['title'] ) );

			}

			$pageBuildersTexts[ $builder ] = array(
				'text'          => $plugin_string,
				'ButtonText'    => $button_text,
				'noInstall'     => $no_install,
				'title'         => $title,
				'install_fail'  => $install_fail,
				'activate_fail' => $activate_fail,
				'close_btn'     => __( 'Close', 'funnel-builder' ),
				'server_error'  => sprintf( __( 'An unexpected error occurred. Something may wrong with WordPress.org or this server\'s configuration. If you continue to have problems, please try the %s.', 'funnel-builder' ), '<a target="_blank" href="https://buildwoofunnels.com/support/">Support Forums</a>' ),
			);
		}

		return $pageBuildersTexts;
	}

	public function get_dependent_plugins_for_page_builder( $page_builder_slug = '', $default = 'elementor' ) {
		$plugins = $this->get_plugins_groupby_page_builders();

		if ( array_key_exists( $page_builder_slug, $plugins ) ) {
			return $plugins[ $page_builder_slug ];
		}

		return $plugins[ $default ];
	}

	/**
	 * Get Plugins list by page builder.
	 *
	 * @return array Required Plugins list.
	 * @since 1.1.4
	 *
	 */
	public function get_plugins_groupby_page_builders() {

		$divi_status   = $this->get_plugin_status( 'divi-builder/divi-builder.php' );
		$theme_status  = 'not-installed';
		if ( $divi_status ) {
			if ( true === $this->is_divi_theme_installed() ) {
				if ( false === $this->is_divi_theme_enabled() ) {
					$theme_status = 'deactivated';
				} else {
					$theme_status = 'activated';
					$divi_status  = '';
				}
			}
		}



		$plugins = array(
			'elementor' => array(
				'title'   => 'Elementor',
				'plugins' => array(
					array(
						'slug'   => 'elementor', // For download from wordpress.org.
						'init'   => 'elementor/elementor.php',
						'status' => $this->get_plugin_status( 'elementor/elementor.php' ),
					),
				),
			),
			'divi'      => array(
				'title'         => 'Divi',
				'theme-status'  => $theme_status,
				'plugin-status' => $divi_status,
				'plugins'       => array(
					array(
						'slug'   => 'divi-builder', // For download from wordpress.org.
						'init'   => 'divi-builder/divi-builder.php',
						'status' => $divi_status,
					),
				),
			),
		);

		$plugins['beaver-builder'] = array(
			'title'   => 'Beaver Builder',
			'plugins' => array(),
		);

		// Check if Pro Exist.
		if ( file_exists( WP_PLUGIN_DIR . '/' . 'bb-plugin/fl-builder.php' ) && ! is_plugin_active( 'beaver-builder-lite-version/fl-builder.php' ) ) {
			$plugins['beaver-builder']['plugins'][] = array(
				'slug'   => 'bb-plugin',
				'init'   => 'bb-plugin/fl-builder.php',
				'status' => $this->get_plugin_status( 'bb-plugin/fl-builder.php' ),
			);
		} else {
			$plugins['beaver-builder']['plugins'][] = array(
				'slug'   => 'beaver-builder-lite-version', // For download from wordpress.org.
				'init'   => 'beaver-builder-lite-version/fl-builder.php',
				'status' => $this->get_plugin_status( 'beaver-builder-lite-version/fl-builder.php' ),
			);
		}
		$plugins['wp_editor']['plugins'][] = array(
			'slug'   => '',
			'status' => null,
		);

		return $plugins;
	}

	/**
	 * Get plugin status
	 *
	 * @param string $plugin_init_file Plguin init file.
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function get_plugin_status( $plugin_init_file ) {

		if ( null === $this->installed_plugins ) {
			$this->installed_plugins = get_plugins();
		}

		if ( ! isset( $this->installed_plugins[ $plugin_init_file ] ) ) {
			return 'install';
		} elseif ( ! is_plugin_active( $plugin_init_file ) ) {
			return 'activate';
		}

		return;
	}

	/**
	 * Check if Divi theme is install status.
	 *
	 * @return boolean
	 */
	public function is_divi_theme_installed() {
		foreach ( (array) wp_get_themes() as $theme ) {
			if ( 'Divi' === $theme->name || 'Divi' === $theme->parent_theme || 'Extra' === $theme->name || 'Extra' === $theme->parent_theme ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if divi theme enabled for post id.
	 *
	 * @param object $theme theme data.
	 *
	 * @return boolean
	 */
	public function is_divi_theme_enabled( $theme = false ) {

		if ( ! $theme ) {
			$theme = wp_get_theme();
		}

		if ( 'Divi' === $theme->name || 'Divi' === $theme->parent_theme || 'Extra' === $theme->name || 'Extra' === $theme->parent_theme ) {
			return true;
		}

		return false;
	}

	public function get_all_page_builders() {
		return array(
			array(
				'name'  => 'Elementor',
				'value' => 'elementor',
			),
			array(
				'name'  => 'Divi',
				'value' => 'divi',
			),
			/**array(
			 * 'name'  => 'Beaver Builder',
			 * 'value' => 'beaver-builder',
			 * ),
			 */
		);
	}

}


if ( class_exists( 'WFFN_Core' ) ) {
	WFFN_Core::register( 'page_builders', 'WFFN_Page_Builder_Manager' );
}
