<?php
/**
 * WP Zinc Social Publisher for Postiz general plugin functions.
 *
 * @package WPZinc_Social_Publisher_For_Postiz
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
function wpzinc_social_publisher_for_postiz_update_credentials( $result, $client_id, $existing_access_token ) {

	// Get Plugin instance.
	$wpzinc_social_publisher_for_postiz = wpzinc_social_publisher_for_postiz::get_instance();

	// Get the account IDs based on the existing access token.
	$account_ids = $wpzinc_social_publisher_for_postiz->get_class( 'settings' )->get_account_ids_by_access_token( $existing_access_token );

	// Bail if no accounts are found.
	if ( count( $account_ids ) === 0 ) {
		return;
	}

	// Update the access and refresh tokens for each account.
	foreach ( $account_ids as $account_id ) {
		$wpzinc_social_publisher_for_postiz->get_class( 'settings' )->update_account_credentials(
			$result['access_token'],
			$result['refresh_token'],
			$result['token_expires'],
			$account_id
		);
	}

}

// Update Access Token when refreshed by the API class.
add_action( 'wpzinc_social_publisher_for_postiz_api_refresh_token', 'wpzinc_social_publisher_for_postiz_update_credentials', 10, 3 );
