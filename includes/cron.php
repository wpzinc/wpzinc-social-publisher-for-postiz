<?php
/**
 * Defines functions that are called by WordPress' Cron.
 *
 * @package WPZinc_Social_Publisher_For_Postiz
 * @author WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define the WP Cron function to perform the log cleanup
 *
 * @since   1.0.0
 */
function wp_to_postiz_log_cleanup_cron() {

	// Initialise Plugin.
	$wp_to_postiz = wp_to_postiz::get_instance();
	$wp_to_postiz->initialize();

	// Call CRON Log Cleanup function.
	$wp_to_postiz->get_class( 'cron' )->log_cleanup();

	// Shutdown.
	unset( $wp_to_postiz );

}
add_action( 'wp_to_postiz_log_cleanup_cron', 'wp_to_postiz_log_cleanup_cron' );

/**
 * Define the WP Cron function to perform the Media Library cleanup
 * of Text to Image generations
 *
 * @since   1.0.0
 */
function wp_to_postiz_media_cleanup_cron() {

	// Initialise Plugin.
	$wp_to_postiz = wp_to_postiz::get_instance();
	$wp_to_postiz->initialize();

	// Call Media Cleanup function.
	$wp_to_postiz->get_class( 'media_library' )->cleanup();

	// Shutdown.
	unset( $wp_to_postiz );

}
add_action( 'wp_to_postiz_media_cleanup_cron', 'wp_to_postiz_media_cleanup_cron' );
