<?php

class WFFN_Template_Importer {

    private static $instance = null;
    private static $importer = [];

    public function __construct() {
        require __DIR__ . '/remote/class-wffn-remote-template-importer.php';

        if ( class_exists( 'WFFN_Remote_Template_Importer' ) ) {
            WFFN_Core()->remote_importer = WFFN_Remote_Template_Importer::get_instance();
        }
        add_action( 'wffn_step_duplicated', array( $this, 'maybe_clear_cache' ) );
    }

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function register( $builder, $importer ) {
        if ( ! isset( self::$importer[ $builder ] ) && $importer instanceof WFFN_Import_Export ) {
            self::$importer[ $builder ] = $importer;
        }
    }

    /**
     * @param $module_id
     * @param $builder
     * @param $slug
     * @param $step
     *
     * @return array
     */
    public function import_remote( $module_id, $builder, $slug, $step ) {
        $result = [ 'success' => false, 'error' => __( 'No Error', 'funnel-builder' ) ];

        $template_file_path = $builder . '/' . $step . '/' . $slug;
        if ( ! file_exists( WFFN_TEMPLATE_UPLOAD_DIR . $template_file_path . '.json' ) ) {
            //Pull Template from cloud
            $content = WFFN_Core()->remote_importer->get_remote_template( $step, $slug, $builder );
        } else {
            $content = file_get_contents( WFFN_TEMPLATE_UPLOAD_DIR . $template_file_path . '.json' );
            unlink( WFFN_TEMPLATE_UPLOAD_DIR . $template_file_path . '.json' ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
        }
        if ( empty( $content ) || ( is_array( $content ) && isset( $content['error'] ) ) ) {
            $result['error'] = $content['error'];

            return $result;
        }
        $content           = apply_filters( 'wffn_imported_template_content', $content, $module_id );
        $status            = $this->import( $module_id, $builder, $slug, $content );
        $result['success'] = $status;

        do_action( 'wffn_import_completed', $module_id, $step, $builder, $slug );

        return $result;
    }

    /**
     * @param $module_id
     * @param $builder
     * @param $slug
     *
     * @return bool
     */
    public function import( $module_id, $builder, $slug, $content = '' ) {

        if ( isset( self::$importer[ $builder ] ) && self::$importer[ $builder ] instanceof WFFN_Import_Export && ! empty( $content ) ) {

            //Remove all Page Builders meta before importing new ones
            delete_post_meta( $module_id, '_elementor_edit_mode' );
            delete_post_meta( $module_id, '_et_pb_use_builder' );
            delete_post_meta( $module_id, '_fl_builder_enabled' );

            $importer = self::$importer[ $builder ];
            WooFunnels_Dashboard::$classes['BWF_Logger']->log( "Importing the " . $module_id, 'wffn_template_import' );
            WooFunnels_Dashboard::$classes['BWF_Logger']->log( "Content length the " . strlen( $content ), 'wffn_template_import' );
            $status = $importer->import( $module_id, $content );
            delete_post_meta( $module_id, '_tobe_import_template_type' );
            delete_post_meta( $module_id, '_tobe_import_template' );

            return $status;
        } else {
            WooFunnels_Dashboard::$classes['BWF_Logger']->log( "failed importing for " . $module_id . "-- builder" . $builder, 'wffn_template_import' );
        }

        return false;
    }

    /**
     * @param $module_id
     * @param $builder
     * @param $slug
     *
     * @return array||null
     */
    public function export( $module_id, $builder, $slug ) {
        if ( isset( self::$importer[ $builder ] ) && self::$importer[ $builder ] instanceof WFFN_Import_Export ) {
            $importer    = self::$importer[ $builder ];
            $export_data = $importer->export( $module_id, $builder, $slug );

            return $export_data;
        }

        return null;
    }

    public function maybe_clear_cache() {
        if ( class_exists( '\Elementor\Plugin' ) ) {
            Elementor\Plugin::$instance->files_manager->clear_cache();
        }
    }

}

WFFN_Core::register( 'importer', 'WFFN_Template_Importer' );
