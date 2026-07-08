<?php
/**
 * Postiz Auto Poster general plugin functions.
 *
 * @package Postiz_Auto_Poster
 * @author WP Zinc
 */

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
function postiz_auto_poster_update_credentials( $result, $client_id, $existing_access_token ) {

	// Get Plugin instance.
	$postiz_auto_poster = Postiz_Auto_Poster::get_instance();

	// Get the account IDs based on the existing access token.
	$account_ids = $postiz_auto_poster->get_class( 'settings' )->get_account_ids_by_access_token( $existing_access_token );

	// Bail if no accounts are found.
	if ( count( $account_ids ) === 0 ) {
		return;
	}

	// Update the access and refresh tokens for each account.
	foreach ( $account_ids as $account_id ) {
		$postiz_auto_poster->get_class( 'settings' )->update_account_credentials(
			$result['access_token'],
			$result['refresh_token'],
			$result['token_expires'],
			$account_id
		);
	}

}

// Update Access Token when refreshed by the API class.
add_action( 'postiz_auto_poster_api_refresh_token', 'postiz_auto_poster_update_credentials', 10, 3 );
