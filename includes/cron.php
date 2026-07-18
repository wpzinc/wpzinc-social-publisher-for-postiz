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
function wpzinc_social_publisher_for_postiz_log_cleanup_cron() {

	// Initialise Plugin.
	$wpzinc_social_publisher_for_postiz = wpzinc_social_publisher_for_postiz::get_instance();
	$wpzinc_social_publisher_for_postiz->initialize();

	// Call CRON Log Cleanup function.
	$wpzinc_social_publisher_for_postiz->get_class( 'cron' )->log_cleanup();

	// Shutdown.
	unset( $wpzinc_social_publisher_for_postiz );

}
add_action( 'wpzinc_social_publisher_for_postiz_log_cleanup_cron', 'wpzinc_social_publisher_for_postiz_log_cleanup_cron' );

/**
 * Define the WP Cron function to perform the Media Library cleanup
 * of Text to Image generations
 *
 * @since   1.0.0
 */
function wpzinc_social_publisher_for_postiz_media_cleanup_cron() {

	// Initialise Plugin.
	$wpzinc_social_publisher_for_postiz = wpzinc_social_publisher_for_postiz::get_instance();
	$wpzinc_social_publisher_for_postiz->initialize();

	// Call Media Cleanup function.
	$wpzinc_social_publisher_for_postiz->get_class( 'media_library' )->cleanup();

	// Shutdown.
	unset( $wpzinc_social_publisher_for_postiz );

}
add_action( 'wpzinc_social_publisher_for_postiz_media_cleanup_cron', 'wpzinc_social_publisher_for_postiz_media_cleanup_cron' );
