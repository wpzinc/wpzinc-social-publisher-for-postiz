<?php
/**
 * Social Publisher for Postiz general plugin functions.
 *
 * @package social_publisher_for_postiz
 * @author WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Saves the new access token, refresh token and its expiry against
 * all accounts that have the existing access token.
 *
 * @since   1.0.0
 *
 * @param   array  $result                  New Access Token, Refresh Token and Expiry timestamp.
 * @param   string $client_id               OAuth Client ID used for the Access and Refresh Tokens.
 * @param   string $existing_access_token   Existing Access Token.
 */
function social_publisher_for_postiz_update_credentials( $result, $client_id, $existing_access_token ) {

	// Get Plugin instance.
	$social_publisher_for_postiz = social_publisher_for_postiz::get_instance();

	// Get the account IDs based on the existing access token.
	$account_ids = $social_publisher_for_postiz->get_class( 'settings' )->get_account_ids_by_access_token( $existing_access_token );

	// Bail if no accounts are found.
	if ( count( $account_ids ) === 0 ) {
		return;
	}

	// Update the access and refresh tokens for each account.
	foreach ( $account_ids as $account_id ) {
		$social_publisher_for_postiz->get_class( 'settings' )->update_account_credentials(
			$result['access_token'],
			$result['refresh_token'],
			$result['token_expires'],
			$account_id
		);
	}

}

// Update Access Token when refreshed by the API class.
add_action( 'social_publisher_for_postiz_api_refresh_token', 'social_publisher_for_postiz_update_credentials', 10, 3 );
