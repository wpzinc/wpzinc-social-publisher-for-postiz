<?php
/**
 * Defines functions that are called by WordPress' Cron.
 *
 * @package social_publisher_for_postiz
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
function social_publisher_for_postiz_log_cleanup_cron() {

	// Initialise Plugin.
	$social_publisher_for_postiz = social_publisher_for_postiz::get_instance();
	$social_publisher_for_postiz->initialize();

	// Call CRON Log Cleanup function.
	$social_publisher_for_postiz->get_class( 'cron' )->log_cleanup();

	// Shutdown.
	unset( $social_publisher_for_postiz );

}
add_action( 'social_publisher_for_postiz_log_cleanup_cron', 'social_publisher_for_postiz_log_cleanup_cron' );

/**
 * Define the WP Cron function to perform the Media Library cleanup
 * of Text to Image generations
 *
 * @since   1.0.0
 */
function social_publisher_for_postiz_media_cleanup_cron() {

	// Initialise Plugin.
	$social_publisher_for_postiz = social_publisher_for_postiz::get_instance();
	$social_publisher_for_postiz->initialize();

	// Call Media Cleanup function.
	$social_publisher_for_postiz->get_class( 'media_library' )->cleanup();

	// Shutdown.
	unset( $social_publisher_for_postiz );

}
add_action( 'social_publisher_for_postiz_media_cleanup_cron', 'social_publisher_for_postiz_media_cleanup_cron' );
