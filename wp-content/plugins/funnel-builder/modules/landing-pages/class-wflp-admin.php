<?php //phpcs:ignore WordPress.WP.TimezoneChange.DeprecatedSniff
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * Class WFLP_Admin
 */
class WFLP_Admin {

    private static $ins = null;

    public function __construct() {

        add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 92 );
        add_action( 'admin_head', array( $this, 'hide_from_menu' ) );
        add_filter( 'woofunnels_global_settings', function ( $menu ) {
            array_push( $menu, array(
                'title'    => __( 'Sales Pages', 'funnel-builder' ),
                'slug'     => 'lp-settings',
                'link'     => admin_url( 'admin.php?page=wf-lp&section=lp-settings' ),
                'priority' => 20,
            ) );

            return $menu;
        } );
        add_action( 'edit_form_after_title', [ $this, 'add_back_button' ] );
        add_filter( 'et_builder_enabled_builder_post_type_options', [ $this, 'wffn_add_lp_type_to_divi' ], 999 );
        add_action( 'admin_footer', array( $this, 'maybe_add_js_for_permalink_settings' ) );

        add_filter( 'bwf_general_settings_fields', array( $this, 'add_permalink_settings' ), 10 );
        add_filter( 'bwf_general_settings_default_config', function ( $fields ) {
            $fields['landing_page_base'] = 'sp';

            return $fields;
        } );

        add_filter( 'bwf_enable_ecommerce_integration_landing', '__return_true' );
       	add_action('admin_init', array( $this , 'maybe_show_wizard'));

    }

    /**
     * @return WFLP_Admin|null
     */
    public static function get_instance() {
        if ( null === self::$ins ) {
            self::$ins = new self;
        }

        return self::$ins;
    }

    public function register_admin_menu() {

        add_submenu_page( 'woofunnels', __( 'Sales Page', 'funnel-builder' ), __( 'Sales Page', 'funnel-builder' ), 'manage_options', 'wf-lp', array(
            $this,
            'builder_view',
        ) );
    }

    public function builder_view() {

        $section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );

        if ( 'settings' === $section ) {
            include_once WFFN_PLUGIN_DIR . '/admin/views/landing-pages/settings.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

            return;
        }

        if ( 'lp-settings' === $section ) {
            include_once WFFN_PLUGIN_DIR . '/admin/views/landing-pages/lp-settings.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

            return;
        }

        include_once WFFN_PLUGIN_DIR . '/admin/views/landing-pages/without-template.php'; //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
    }

    /**
     * @param $funnel
     */
    public function get_tabs_html( $lp_id ) {
        $tabs = $this->get_tabs_links( $lp_id );
        ?>
		<div class="bwf_menu_list_primary">
			<ul>
                <?php
                foreach ( $tabs as $tab ) {
                    $is_active = $this->is_tab_active_class( $tab['section'] );
                    $tab_link  = $this->get_tab_link( $tab );
                    ?>
					<li class="<?php echo esc_attr( $is_active ); ?>">
						<a href="<?php echo empty( $tab_link ) ? 'javascript:void(0);' : esc_url( $tab_link ); ?>">
                            <?php
                            echo $this->get_tabs_icons( $tab['section'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            echo esc_html( $tab['title'] );
                            ?>
						</a>
					</li>
                    <?php
                }
                ?>
			</ul>
		</div>
        <?php
    }

    public function get_tabs_links( $lp_id ) {
        $tabs = array(
            array(
                'section' => 'design',
                'title'   => __( 'Design', 'funnel-builder' ),
                'link'    => add_query_arg( array(
                    'page'    => 'wf-lp',
                    'section' => 'design',
                    'edit'    => $lp_id,
                ), admin_url( 'admin.php' ) ),
            ),
            array(
                'section' => 'settings',
                'title'   => __( 'Settings', 'funnel-builder' ),
                'link'    => add_query_arg( array(
                    'page'    => 'wf-lp',
                    'section' => 'settings',
                    'edit'    => $lp_id,
                ), admin_url( 'admin.php' ) ),
            ),
        );

        return apply_filters( 'wffn_lp_tabs', $tabs );
    }

    public function is_tab_active_class( $section ) {

        if ( isset( $_GET['section'] ) && $section === $_GET['section'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return 'active';
        }
        if ( empty( $section ) && ! isset( $_GET['section'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return 'active';
        }

        return '';
    }

    public function get_tab_link( $tab ) {
        return BWF_Admin_Breadcrumbs::maybe_add_refs( $tab['link'] );
    }

    public function get_tabs_icons( $section ) {
        //Funnels
        $icon = '<span class="dashicons dashicons-art"></span>';
        if ( 'analytics' === $section ) {
            $icon = '<span class="dashicons dashicons-chart-bar"></span>';
        }
        if ( 'settings' === $section ) {
            $icon = '<span class="dashicons dashicons-admin-generic"></span>';
        }

        return $icon;
    }

    public function hide_from_menu() {
        global $submenu, $woofunnels_menu_slug;
        foreach ( $submenu as $key => $men ) {
            if ( $woofunnels_menu_slug !== $key ) {
                continue;
            }
            foreach ( $men as $k => $d ) {
                if ( 'admin.php?page=wf-lp' === $d[2] ) {

                    unset( $submenu[ $key ][ $k ] );
                }
            }
        }
        global $parent_file, $plugin_page, $submenu_file; //phpcs:ignore
        if ( 'wf-lp' === $plugin_page ) :
            $parent_file  = $woofunnels_menu_slug;//phpcs:ignore
            $submenu_file = 'admin.php?page=bwf_funnels'; //phpcs:ignore
        endif;
    }


    public function render_primary_nav() {
    }

    public function get_selected_nav_class() {
        if ( 'bwf_funnels' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) {
            return 'nav-tab-active';
        }

        return '';
    }

    public function get_selected_nav_class_global() {
        if ( 'wf-lp' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) && 'lp-settings' === filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING ) ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return 'nav-tab-active';
        }

        return '';
    }

    /**
     * Adding back to landing page editor
     */
    public function add_back_button() {
        global $post;
        $lp_type = WFFN_Core()->landing_pages->get_post_type_slug();
        $lp_id   = ( $lp_type === $post->post_type ) ? $post->ID : 0;
        if ( $lp_id > 0 ) {
            $funnel_id = get_post_meta( $lp_id, '_bwf_in_funnel', true );
            if ( ! empty( $funnel_id ) && abs( $funnel_id ) > 0 ) {
                BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel_id );
            }
            $edit_link = BWF_Admin_Breadcrumbs::maybe_add_refs( add_query_arg( [
                'page'    => 'wf-lp',
                'edit'    => $lp_id,
                'section' => 'design',
            ], admin_url( 'admin.php' ) ) );

            if ( use_block_editor_for_post_type( $lp_type ) ) {
                add_action( 'admin_footer', array( $this, 'render_back_to_funnel_script_for_block_editor' ) );
            } else { ?>
				<div id="wf_funnel-switch-mode">
					<a id="wf_funnel-back-button" class="button button-default button-large" href="<?php echo esc_url( $edit_link ); ?>">
                        <?php esc_html_e( '&#8592; Back to Sales Page', 'funnel-builder' ); ?>
					</a>
				</div>
                <?php
            }
        } ?>
        <?php
    }

    public function render_back_to_funnel_script_for_block_editor() {
        global $post;
        $lp_type = WFFN_Core()->landing_pages->get_post_type_slug();
        $lp_id   = ( $lp_type === $post->post_type ) ? $post->ID : 0;
        if ( $lp_id > 0 ) {
            $funnel_id = get_post_meta( $lp_id, '_bwf_in_funnel', true );
            if ( ! empty( $funnel_id ) && abs( $funnel_id ) > 0 ) {
                BWF_Admin_Breadcrumbs::register_ref( 'wffn_funnel_ref', $funnel_id );
            }
            $edit_link = BWF_Admin_Breadcrumbs::maybe_add_refs( add_query_arg( [
                'page'    => 'wf-lp',
                'edit'    => $lp_id,
                'section' => 'design',
            ], admin_url( 'admin.php' ) ) ) ?>
			<script id="wf_funnel-back-button-template" type="text/html">
				<div id="wf_funnel-switch-mode" style="margin-right: 15px;margin-left: -5px;">
					<a id="wf_funnel-back-button" class="button button-default button-large" href="<?php echo esc_url( $edit_link ); ?>">
                        <?php esc_html_e( '&#8592; Back to Sales Page', 'elementor' ); ?>
					</a>
				</div>
			</script>

			<script>
                window.addEventListener('load', function () {
                    (function ($) {
                        let back_button = $($('#wf_funnel-back-button-template').html());
                        if ($('#editor').find('.edit-post-header-toolbar').length > 0) {
                            $('#editor').find('.edit-post-header-toolbar').prepend(back_button);
                        }
                    })(jQuery);
                });
			</script>
        <?php }
    }

    /**
     * @param $options
     *
     * @return mixed
     */
    public function wffn_add_lp_type_to_divi( $options ) {
        $lp_type             = WFFN_Core()->landing_pages->get_post_type_slug();
        $options[ $lp_type ] = 'on';

        return $options;
    }

    public function maybe_add_js_for_permalink_settings() {
        ?>
		<script>
            if (typeof window.bwfBuilderCommons !== "undefined") {
                window.bwfBuilderCommons.addFilter('bwf_common_permalinks_fields', function (e) {
                    e.unshift(
                        {
                            type: "input",
                            inputType: "text",
                            label: "",
                            model: "landing_page_base",
                            inputName: 'landing_page_base',
                        });
                    return e;
                });
            }
		</script>
        <?php
    }


    public function add_permalink_settings( $fields ) {

        $fields['landing_page_base'] = array(
            'label' => __( 'Sales page', 'funnel-builder' ),
            'hint'  => '',

        );

        return $fields;

    }

    public function maybe_show_wizard() {
        if (  'wf-lp' !== filter_input(INPUT_GET,'page', FILTER_SANITIZE_STRING) ) {
            return;
        }
        if ( isset( $_GET['tab'] ) && strpos( wffn_clean($_GET['tab']), 'wizard' ) !== false ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }
        if ( defined( 'WFFN_PRO_PLUGIN_BASENAME' ) && false === WFFN_Common::wffn_is_funnel_pro_active() ) {
            wp_redirect( admin_url( 'admin.php?page=woofunnels&tab=wffn-wizard' ) );
            exit;
        }


    }

}
