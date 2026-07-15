<?php
/**
 * Defines the deactivation function, which is run when the Plugin is deactivated.
 *
 * @package social_publisher_for_postiz
 * @author WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Runs the uninstallation routines when the plugin is deactivated.
 *
 * @since   1.0.0
 *
 * @param   bool $network_wide   Is network wide deactivation.
 */
function social_publisher_for_postiz_deactivate( $network_wide ) {

	// Initialise Plugin.
	$social_publisher_for_postiz = social_publisher_for_postiz::get_instance();
	$social_publisher_for_postiz->initialize();

	// Check if we are on a multisite install, activating network wide, or a single install.
	if ( ! is_multisite() || ! $network_wide ) {
		// Single Site deactivation.
		$social_publisher_for_postiz->get_class( 'install' )->uninstall();
	} else {
		// Multisite network wide deactivation.
		$sites = get_sites(
			array(
				'number' => 0,
			)
		);
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			$social_publisher_for_postiz->get_class( 'install' )->uninstall();
			restore_current_blog();
		}
	}

}
