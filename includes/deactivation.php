<?php
/**
 * Defines the deactivation function, which is run when the Plugin is deactivated.
 *
 * @package WPZinc_Social_Publisher_For_Postiz
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
function wpzinc_social_publisher_for_postiz_deactivate( $network_wide ) {

	// Initialise Plugin.
	$wpzinc_social_publisher_for_postiz = wpzinc_social_publisher_for_postiz::get_instance();
	$wpzinc_social_publisher_for_postiz->initialize();

	// Check if we are on a multisite install, activating network wide, or a single install.
	if ( ! is_multisite() || ! $network_wide ) {
		// Single Site deactivation.
		$wpzinc_social_publisher_for_postiz->get_class( 'install' )->uninstall();
	} else {
		// Multisite network wide deactivation.
		$sites = get_sites(
			array(
				'number' => 0,
			)
		);
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			$wpzinc_social_publisher_for_postiz->get_class( 'install' )->uninstall();
			restore_current_blog();
		}
	}

}
