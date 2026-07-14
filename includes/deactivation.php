<?php
/**
 * Defines the deactivation function, which is run when the Plugin is deactivated.
 *
 * @package Postiz_Auto_Poster
 * @author WP Zinc
 */

/**
 * Runs the uninstallation routines when the plugin is deactivated.
 *
 * @since   1.0.0
 *
 * @param   bool $network_wide   Is network wide deactivation.
 */
function postiz_auto_poster_deactivate( $network_wide ) {

	// Initialise Plugin.
	$postiz_auto_poster = postiz_auto_poster::get_instance();
	$postiz_auto_poster->initialize();

	// Check if we are on a multisite install, activating network wide, or a single install.
	if ( ! is_multisite() || ! $network_wide ) {
		// Single Site deactivation.
		$postiz_auto_poster->get_class( 'install' )->uninstall();
	} else {
		// Multisite network wide deactivation.
		$sites = get_sites(
			array(
				'number' => 0,
			)
		);
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			$postiz_auto_poster->get_class( 'install' )->uninstall();
			restore_current_blog();
		}
	}

}
