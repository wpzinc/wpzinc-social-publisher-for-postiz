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

/**
 * Define the WP Cron function to refresh access tokens before they expire
 *
 * @since   6.2.0
 */
function wpzinc_social_publisher_for_postiz_refresh_token_cron() {

	// Initialise Plugin.
	$wpzinc_social_publisher_for_postiz = WPZinc_Social_Publisher_For_Postiz::get_instance();
	$wpzinc_social_publisher_for_postiz->initialize();

	// Refresh any access tokens that are due to expire.
	$wpzinc_social_publisher_for_postiz->get_class( 'cron' )->refresh_token();

	// Shutdown.
	unset( $wpzinc_social_publisher_for_postiz );

}
add_action( 'wpzinc_social_publisher_for_postiz_refresh_token_cron', 'wpzinc_social_publisher_for_postiz_refresh_token_cron' );
