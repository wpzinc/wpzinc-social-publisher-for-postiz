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
	private $api_endpoint = 'https://api.postiz.com/public/v1';

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
	 * @since   3.8.4
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
	 * @since   3.8.1
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
	 * Returns the user's information.
	 *
	 * @since   1.0.0
	 *
	 * @param   int $transient_expiration_time  Transient Expiration Time, in seconds (default: 12 hours).
	 * @return  WP_Error|array
	 */
	public function user( $transient_expiration_time = 43200 ) {

		// Get user.
		$user = $this->get( 'user' );

		// Check for errors.
		if ( is_wp_error( $user ) ) {
			return $user;
		}

		// Store user in transient.
		set_transient( 'social_post_flow_api_user', $user['data'], $transient_expiration_time );

		// Return user.
		return $user['data'];

	}

	/**
	 * Returns a list of Social Media Profiles.
	 *
	 * @since   1.0.0
	 *
	 * @param   bool $force                      Force API call (false = use WordPress transient).
	 * @param   int  $transient_expiration_time  Transient Expiration Time, in seconds (default: 12 hours).
	 * @return  WP_Error|array
	 */
	public function profiles( $force = false, $transient_expiration_time = 43200 ) {

		// Setup profiles array.
		$profiles = array();

		// Check if our WordPress transient already has this data.
		// This reduces the number of times we query the API.
		$profiles = get_transient( 'social_post_flow_api_profiles' );
		if ( $force || false === $profiles ) {
			// Setup profiles array.
			$profiles = array();

			// Get profiles.
			$results = $this->get( 'profiles' );

			// Check for errors.
			if ( is_wp_error( $results ) ) {
				return $results;
			}

			// Build array of profiles, with the profile ID as the key.
			foreach ( $results['data'] as $profile ) {
				$profiles[ $profile['id'] ] = $profile;
			}

			// Store profiles in transient.
			set_transient( 'social_post_flow_api_profiles', $profiles, $transient_expiration_time );
		}

		// Return results.
		return $profiles;

	}

	/**
	 * Creates a Post on Social Post Flow.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $params     Params.
	 * @return  WP_Error|array
	 */
	public function create_post( $params ) {

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
							'Authorization' => 'Bearer ' . $this->access_token,
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
							'Authorization' => 'Bearer ' . $this->access_token,
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

		// If the body contains a message, an error occured.
		if ( isset( $body['message'] ) ) {
			// OAuth and non-authenticated requests will just return a `message` key.
			// Authenticated requests will return a `message` key and an `errors` array.
			if ( isset( $body['errors'] ) ) {
				$error_message = array();
				foreach ( $body['errors'] as $error_key => $errors ) {
					$error_message = array_merge( $error_message, $errors );
				}
			} else {
				$error_message = array(
					$body['message'],
				);
			}

			return new WP_Error(
				$http_code,
				implode( "\n", $error_message )
			);
		}

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
		$timeout = apply_filters( 'social_post_flow_api_get_timeout', $timeout );

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
		$enable_ssl_verification = apply_filters( 'social_post_flow_api_enable_ssl_verification', $enable_ssl_verification );

		return $enable_ssl_verification;

	}

}
