<?php
/**
 * Defines functions that are called by WordPress' Cron.
 *
 * @package Postiz_Auto_Poster
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
function postiz_auto_poster_log_cleanup_cron() {

	// Initialise Plugin.
	$postiz_auto_poster = postiz_auto_poster::get_instance();
	$postiz_auto_poster->initialize();

	// Call CRON Log Cleanup function.
	$postiz_auto_poster->get_class( 'cron' )->log_cleanup();

	// Shutdown.
	unset( $postiz_auto_poster );

}
add_action( 'postiz_auto_poster_log_cleanup_cron', 'postiz_auto_poster_log_cleanup_cron' );

/**
 * Define the WP Cron function to perform the Media Library cleanup
 * of Text to Image generations
 *
 * @since   1.0.0
 */
function postiz_auto_poster_media_cleanup_cron() {

	// Initialise Plugin.
	$postiz_auto_poster = postiz_auto_poster::get_instance();
	$postiz_auto_poster->initialize();

	// Call Media Cleanup function.
	$postiz_auto_poster->get_class( 'media_library' )->cleanup();

	// Shutdown.
	unset( $postiz_auto_poster );

}
add_action( 'postiz_auto_poster_media_cleanup_cron', 'postiz_auto_poster_media_cleanup_cron' );
