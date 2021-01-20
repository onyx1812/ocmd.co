<?php
/**
 * Section head
 */
/** Registering Settings in top bar */
$section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );

if ( ! is_null( $section ) && 'new' === $section && class_exists( 'BWF_Admin_Breadcrumbs' ) ) {
	BWF_Admin_Breadcrumbs::register_node( [ 'text' => esc_html__( 'New Funnel', 'funnel-builder' ) ] );
}
BWF_Admin_Breadcrumbs::render_sticky_bar();
?>
<div class="wrap wffn-funnel-common">
