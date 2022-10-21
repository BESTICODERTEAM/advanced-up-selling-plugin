<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register List Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_list_widget( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/up-selling-list-widget.php' );

	$widgets_manager->register( new \Elementor_List_Widget() );

}
add_action( 'elementor/widgets/register', 'register_list_widget' );