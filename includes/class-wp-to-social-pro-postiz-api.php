<?php
/**
 * Postiz API class
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 */

/**
 * Provides functions for sending statuses and querying Postiz's API.
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 * @version 1.0.0
 */
class WP_To_Social_Pro_Postiz_API {

	/**
	 * Holds the base class object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object.
	 */
	public $base;

	/**
	 * Holds the Postiz Application's Client ID
	 *
	 * @since   1.0.0
	 *
	 * @var     string.
	 */
	private $client_id = 'pca_qqjGRuJ7khiATvS7d59KqBWGd9wWnAnU';

	/**
	 * Holds the oAuth Authorize URL
	 *
	 * @since   1.0.0
	 *
	 * @var     string.
	 */
	private $oauth_authorize_url = 'https://platform.postiz.com/oauth/';

	/**
	 * Holds the oAuth Gateway endpoint, used to exchange a code for an access token
	 *
	 * @since   1.0.0
	 *
	 * @var     string.
	 */
	private $redirect_uri = 'https://www.wpzinc.com/?oauth=postiz';

	/**
	 * Holds the Proxy endpoint, which might be used to pass requests through
	 *
	 * @since   1.0.0
	 *
	 * @var     string.
	 */
	private $proxy_endpoint = 'https://proxy.wpzinc.net/';

	/**
	 * Holds the API endpoint
	 *
	 * @since   1.0.0
	 *
	 * @var     string.
	 */
	private $api_endpoint = 'https://api.postiz.com/public/v1/';

	/**
	 * Access Token
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	public $access_token = '';

	/**
	 * Refresh Token
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	public $refresh_token = '';

	/**
	 * Token Expiry Timestamp
	 *
	 * @since   1.0.0
	 *
	 * @var     int
	 */
	public $token_expires = false;

	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		add_action( 'postiz_auto_poster_output_auth', array( $this, 'output_oauth' ) );

	}

	/**
	 * Outputs an Authorize Plugin button on Settings > General when the Plugin needs to be authenticated with Postiz.
	 *
	 * @since   1.0.0
	 */
	public function output_oauth() {

		?>
		<div class="wpzinc-option">
			<div class="full">
				<a href="<?php echo esc_attr( $this->get_oauth_url() ); ?>" class="button button-primary">
					<?php esc_html_e( 'Connect a Postiz Account', 'postiz-auto-poster' ); ?>
				</a>
			</div>
		</div>
		<?php

	}

	/**
	 * Returns the oAuth URL used to begin the oAuth process
	 *
	 * @since   1.0.0
	 *
	 * @return  string  oAuth URL
	 */
	public function get_oauth_url() {

		// Return OAuth URL.
		return add_query_arg(
			array(
				'client_id'     => $this->client_id,
				'response_type' => 'code',
				'state'         => admin_url( 'admin.php?page=' . $this->base->plugin->name . '-settings' ),
			),
			$this->oauth_authorize_url . 'authorize'
		);

	}

	/**
	 * Returns the Postiz URL where the user can register for a Postiz account
	 *
	 * @since   1.0.0
	 *
	 * @return  string  URL
	 */
	public function get_registration_url() {

		return 'https://postiz.pro/wpzinc';

	}

	/**
	 * Returns the Postiz URL where the user can connect their social media accounts
	 * to Buffer
	 *
	 * @since   1.0.0
	 *
	 * @return  string  URL
	 */
	public function get_connect_profiles_url() {

		return 'https://platform.postiz.com/launches';

	}

	/**
	 * Returns the Postiz URL where the user can change the timezone for the
	 * given profile ID.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $profile_id     Profile ID.
	 * @return  string                  Timezone Settings URL
	 */
	public function get_timezone_settings_url( $profile_id ) {

		return 'https://publish.buffer.com/profile/' . $profile_id . '/tab/settings/postingSchedule';

	}

	/**
	 * Sets this class' access and refresh tokens
	 *
	 * @since   1.0.0
	 *
	 * @param   string   $access_token    Access Token.
	 * @param   string   $refresh_token   Refresh Token.
	 * @param   bool|int $token_expires   Token Expiry.
	 */
	public function set_tokens( $access_token = '', $refresh_token = '', $token_expires = false ) {

		$this->access_token  = $access_token;
		$this->refresh_token = $refresh_token;
		$this->token_expires = $token_expires;

	}

	/**
	 * Checks if an access token was set.  Called by any function which
	 * performs a call to the API
	 *
	 * @since   1.0.0
	 *
	 * @return  bool    Token Exists
	 */
	private function check_access_token_exists() {

		if ( empty( $this->access_token ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Checks if a refresh token was set.  Called by any function which
	 * performs a call to the API
	 *
	 * @since   1.0.0
	 *
	 * @return  bool    Token Exists
	 */
	private function check_refresh_token_exists() {

		if ( empty( $this->refresh_token ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Returns the account details.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function account() {

		// Postiz doesn't have an account() or user() API endpoint, so return sensible defaults.
		return array(
			'id'            => 'default',
			'name'          => 'Default',
			'email'         => 'noreply@postiz.io',
			'channel_limit' => 0,
			'plan'          => 'postiz',
		);

	}

	/**
	 * Returns a list of Social Media Profiles.
	 *
	 * @since   1.0.0
	 *
	 * @param   bool   $force        Force API call (false = use stored option).
	 * @param   string $account_id   Account ID.
	 * @return  WP_Error|array
	 */
	public function profiles( $force = false, $account_id = 'default' ) {

		// Setup profiles array.
		$profiles = array();

		// Return stored profiles if available and not forcing a refresh.
		$option_name = $this->base->plugin->name . '-profiles-' . $account_id;
		$profiles    = get_option( $option_name );
		if ( ! $force && is_array( $profiles ) ) {
			return $profiles;
		}

		// Get profiles.
		$results = $this->get( 'integrations' );

		// Check for errors.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Build profiles array from results.
		$profiles = array();
		foreach ( $results as $channel ) {
			$profiles[ $channel['id'] ] = array(
				'id'                 => $channel['id'],
				'formatted_service'  => $this->get_formatted_service( $channel['identifier'] ),
				'formatted_username' => $channel['name'],
				'service'            => $this->get_service( $channel['identifier'] ),
				'timezone'           => '',
				'can_be_subprofile'  => false,
			);
		}

		// Store profiles in a non-autoloaded option so they persist across
		// object cache eviction, and don't load on every WP request.
		update_option( $option_name, $profiles, false );

		return $profiles;

	}

	/**
	 * Depending on the social media profile type, return the formatted service name.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $type   Social Media Profile Type.
	 * @return  string          Formatted Social Media Profile Service Name
	 */
	private function get_formatted_service( $type ) {

		switch ( $type ) {

			case 'x':
				return __( 'Twitter', 'postiz-auto-poster' );

			case 'linkedin':
				return __( 'LinkedIn Profile', 'postiz-auto-poster' );

			case 'linkedin-page':
				return __( 'LinkedIn Page', 'postiz-auto-poster' );

			case 'facebook':
				return __( 'Facebook Page', 'postiz-auto-poster' );

			case 'instagram':
			case 'instagram-standalone':
				return __( 'Instagram', 'postiz-auto-poster' );

			case 'threads':
				return __( 'Threads', 'postiz-auto-poster' );

			case 'bluesky':
				return __( 'Bluesky', 'postiz-auto-poster' );

			case 'mastodon':
				return __( 'Mastodon', 'postiz-auto-poster' );

			case 'warpcast':
				return __( 'Warpcast', 'postiz-auto-poster' );

			case 'nostr':
				return __( 'Nostr', 'postiz-auto-poster' );

			case 'vk':
				return __( 'VK', 'postiz-auto-poster' );

			case 'youtube':
				return __( 'YouTube', 'postiz-auto-poster' );

			case 'tiktok':
				return __( 'TikTok', 'postiz-auto-poster' );

			case 'reddit':
				return __( 'Reddit', 'postiz-auto-poster' );

			case 'lemmy':
				return __( 'Lemmy', 'postiz-auto-poster' );

			case 'discord':
				return __( 'Discord', 'postiz-auto-poster' );

			case 'slack':
				return __( 'Slack', 'postiz-auto-poster' );

			case 'telegram':
				return __( 'Telegram', 'postiz-auto-poster' );

			case 'kick':
				return __( 'Kick', 'postiz-auto-poster' );

			case 'twitch':
				return __( 'Twitch', 'postiz-auto-poster' );

			case 'pinterest':
				return __( 'Pinterest', 'postiz-auto-poster' );

			case 'dribbble':
				return __( 'Dribbble', 'postiz-auto-poster' );

			case 'medium':
				return __( 'Medium', 'postiz-auto-poster' );

			case 'devto':
				return __( 'Dev.to', 'postiz-auto-poster' );

			case 'hashnode':
				return __( 'Hashnode', 'postiz-auto-poster' );

			case 'WordPress':
				return __( 'WordPress', 'postiz-auto-poster' );

			case 'gmb':
				return __( 'Google My Business', 'postiz-auto-poster' );

			case 'listmonk':
				return __( 'Listmonk', 'postiz-auto-poster' );

			case 'moltbook':
				return __( 'Moltbook', 'postiz-auto-poster' );

			case 'skool':
				return __( 'Skool', 'postiz-auto-poster' );

			case 'whop':
				return __( 'Whop', 'postiz-auto-poster' );

			default:
				return '';

		}

	}

	/**
	 * Depending on the social media profile type, return the service name.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $type   Social Media Profile Type.
	 * @return  string          Social Media Profile Service Name
	 */
	private function get_service( $type ) {

		switch ( $type ) {

			case 'linkedin-page':
				return 'linkedin';

			case 'instagram':
			case 'instagram-standalone':
				return 'instagram';

			default:
				return $type;

		}

	}

	/**
	 * Creates an update (status)
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $params     Params.
	 * @param   string $service    Service.
	 * @return  WP_Error|array
	 */
	public function updates_create( $params, $service = '' ) {

		// @TODO.
		var_dump( $params );
		die();

		return $this->post( 'posts', $params );

	}

	/**
	 * Private function to perform a GET request
	 *
	 * @since  1.0.0
	 *
	 * @param  string $cmd        Command (required).
	 * @param  array  $params     Params (optional).
	 * @return mixed               WP_Error | object
	 */
	private function get( $cmd, $params = array() ) {

		return $this->request( $cmd, 'get', $params );

	}

	/**
	 * Private function to perform a POST request
	 *
	 * @since  1.0.0
	 *
	 * @param  string $cmd        Command (required).
	 * @param  array  $params     Params (optional).
	 * @return mixed               WP_Error | object
	 */
	private function post( $cmd, $params = array() ) {

		return $this->request( $cmd, 'post', $params );

	}

	/**
	 * Main function which handles sending requests to the Social Post Flow API
	 *
	 * @since   1.0.0
	 *
	 * @param   string $cmd        Command.
	 * @param   string $method     Method (get|post).
	 * @param   array  $params     Parameters (optional).
	 * @return  mixed               WP_Error | object
	 */
	private function request( $cmd, $method = 'get', $params = array() ) {

		// Check required parameters exist.
		if ( empty( $this->access_token ) ) {
			return new WP_Error( 'postiz_auto_poster_no_access_token', __( 'No access token was specified', 'postiz-auto-poster' ) );
		}

		// Build endpoint URL.
		$url = $this->api_endpoint . $cmd;

		// Send request.
		switch ( $method ) {
			/**
			 * GET
			 */
			case 'get':
				$response = wp_remote_get(
					$url,
					array(
						'headers'   => array(
							'Authorization' => $this->access_token,
							'Accept'        => 'application/json',
						),
						'body'      => $params,
						'timeout'   => $this->get_timeout(),
						'sslverify' => $this->enable_ssl_verification(),
					)
				);
				break;

			/**
			 * POST
			 */
			case 'post':
				$response = wp_remote_post(
					$url,
					array(
						'headers'   => array(
							'Authorization' => $this->access_token,
							'Accept'        => 'application/json',
						),
						'body'      => $params,
						'timeout'   => $this->get_timeout(),
						'sslverify' => $this->enable_ssl_verification(),
					)
				);
				break;
		}

		// If an error occured, return it now.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Fetch HTTP code and body.
		$http_code = wp_remote_retrieve_response_code( $response );
		$response  = wp_remote_retrieve_body( $response );

		// Decode response.
		$body = json_decode( $response, true );

		// @TODO WP_Error returns on errors.

		return $body;

	}

	/**
	 * Returns the timeout for the Social Post Flow API.
	 *
	 * @since   1.0.0
	 *
	 * @return  int
	 */
	private function get_timeout() {

		// Define the timeout.
		$timeout = 20;

		/**
		 * Defines the number of seconds before timing out a request to the Social Post Flow API.
		 *
		 * @since   1.0.0
		 *
		 * @param   int     $timeout    Timeout, in seconds
		 */
		$timeout = apply_filters( 'postiz_auto_poster_api_get_timeout', $timeout );

		return $timeout;

	}

	/**
	 * Returns whether SSL verification is enabled for the Social Post Flow API.
	 *
	 * @since   1.0.0
	 *
	 * @return  bool
	 */
	private function enable_ssl_verification() {

		$enable_ssl_verification = true;

		/**
		 * Defines whether to enable SSL verification for the Social Post Flow API.
		 *
		 * @since   1.0.0
		 *
		 * @param   bool    $enable_ssl_verification    Enable SSL verification.
		 */
		$enable_ssl_verification = apply_filters( 'postiz_auto_poster_api_enable_ssl_verification', $enable_ssl_verification );

		return $enable_ssl_verification;

	}

}
