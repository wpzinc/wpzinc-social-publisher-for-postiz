<?php
/**
 * Postiz API class
 *
 * @package WPZinc_Social_Publisher_Pro
 * @author  WP Zinc
 */

/**
 * Provides functions for sending statuses and querying Postiz's API.
 *
 * @package WPZinc_Social_Publisher_Pro
 * @author  WP Zinc
 * @version 1.0.0
 */
class WPZinc_Social_Publisher_Pro_Postiz_API {

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

		add_action( 'wpzinc_social_publisher_for_postiz_output_auth', array( $this, 'output_oauth' ) );

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
					<?php esc_html_e( 'Connect a Postiz Account', 'wpzinc-social-publisher-for-postiz' ); ?>
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
				'state'         => rawurlencode(
					add_query_arg(
						array(
							'wpzinc_social_publisher_pro_nonce' => wp_create_nonce( 'wpzinc_social_publisher_pro_nonce' ),
							'page' => $this->base->plugin->name . '-settings',
						),
						admin_url( 'admin.php' )
					)
				),
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
			// Skip some unsupported services.
			if ( in_array( $channel['identifier'], array( 'youtube', 'kick', 'twitch', 'reddit', 'lemmy', 'discord', 'slack', 'warpcast', 'nostr', 'dribbble', 'medium', 'devto', 'hashnode', 'wordpress', 'listmonk', 'whop', 'skool', 'kick', 'moltbook' ), true ) ) {
				continue;
			}

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
				return __( 'Twitter', 'wpzinc-social-publisher-for-postiz' );

			case 'linkedin':
				return __( 'LinkedIn Profile', 'wpzinc-social-publisher-for-postiz' );

			case 'linkedin-page':
				return __( 'LinkedIn Page', 'wpzinc-social-publisher-for-postiz' );

			case 'facebook':
				return __( 'Facebook Page', 'wpzinc-social-publisher-for-postiz' );

			case 'instagram':
			case 'instagram-standalone':
				return __( 'Instagram', 'wpzinc-social-publisher-for-postiz' );

			case 'threads':
				return __( 'Threads', 'wpzinc-social-publisher-for-postiz' );

			case 'bluesky':
				return __( 'Bluesky', 'wpzinc-social-publisher-for-postiz' );

			case 'mastodon':
				return __( 'Mastodon', 'wpzinc-social-publisher-for-postiz' );

			case 'vk':
				return __( 'VK', 'wpzinc-social-publisher-for-postiz' );

			case 'tiktok':
				return __( 'TikTok', 'wpzinc-social-publisher-for-postiz' );

			case 'telegram':
				return __( 'Telegram', 'wpzinc-social-publisher-for-postiz' );

			case 'pinterest':
				return __( 'Pinterest', 'wpzinc-social-publisher-for-postiz' );

			case 'gmb':
				return __( 'Google My Business', 'wpzinc-social-publisher-for-postiz' );

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

		$is_draft = array_key_exists( 'is_draft', $params ) && $params['is_draft'] ? true : false;

		// Build arguments.
		$args = array(
			'type'      => $is_draft ? 'draft' : 'now',
			'date'      => gmdate( 'Y-m-d\TH:i:s\Z', strtotime( '+1 day' ) ), // Setting to now as a draft results in no errors but no post is created in the Postiz UI.
			'shortLink' => false,
			'tags'      => array(),
			'posts'     => array(
				array(
					'integration' => array(
						'id' => $params['profile_ids'][0],
					),
					'value'       => array(
						array(
							'content' => $params['text'],
							'image'   => array(),
						),

						// First comment would go here.
					),
					'settings'    => array(
						'__type' => $service,
					),
				),
			),
		);

		// Scheduling.
		if ( ! $is_draft ) {
			switch ( $params['schedule_type'] ) {
				case 'immediate':
					$args['type'] = 'now';
					$args['date'] = gmdate( 'Y-m-d\TH:i:s\Z', strtotime( 'now' ) );
					break;
				case 'schedule':
					$args['type'] = 'schedule';
					$args['date'] = gmdate( 'Y-m-d\TH:i:s\Z', strtotime( $params['scheduled_at'] ) );
					break;
			}
		}

		// Images.
		switch ( $params['post_type'] ) {
			case 'image':
			case 'story':
			case 'pin':
			case 'googlebusiness':
				// Bail if no images are defined.
				if ( ! array_key_exists( 'media_urls', $params ) ) {
					break;
				}

				// Build images array.
				$images = array();
				foreach ( $params['media_urls'] as $media ) {
					// Upload image.
					$upload = $this->post(
						'upload-from-url',
						array(
							'url' => $media['image'],
						)
					);

					// Bail if the upload failed.
					if ( is_wp_error( $upload ) ) {
						return $upload;
					}

					// Add ID and path to the array.
					$images[] = array(
						'id'   => $upload['id'],
						'path' => $upload['path'],
					);
				}

				// Add images to args.
				$args['posts'][0]['value'][0]['image'] = $images;
				break;
		}

		// Settings.
		switch ( $service ) {
			case 'twitter':
				$settings = array(
					'who_can_reply_post' => 'everyone',
				);
				break;

			case 'facebook':
				if ( $params['post_type'] === 'link' ) {
					$settings = array(
						'url' => $params['url'],
					);
				}
				break;

			case 'instagram':
				$settings = array(
					'post_type' => $params['post_type'], // image, story.
				);
				break;

			case 'tiktok':
				$settings = array(
					'privacy_level'          => 'PUBLIC_TO_EVERYONE',
					'duet'                   => false,
					'stitch'                 => false,
					'comment'                => true,
					'autoAddMusic'           => 'no',
					'brand_content_toggle'   => false,
					'brand_organic_toggle'   => true,
					'content_posting_method' => 'DIRECT_POST',
				);
				break;

			case 'pinterest':
				$settings = array(
					'board' => '',
					'title' => '',
					'link'  => $params['url'],
				);
				break;

		}

		// If service specific settings have been defined, add them to the args.
		if ( isset( $settings ) ) {
			$args['posts'][0]['settings'] = array_merge( $args['posts'][0]['settings'], $settings );
		}

		// Send update.
		$result = $this->post( 'posts', $args );

		// Bail if the result is an error.
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Get post.
		$post = $this->get_post( $result[0]['postId'] );

		// Return array of just the data we need to send to the Plugin.
		return array(
			'profile_id'        => $params['profile_ids'][0],
			'message'           => 'scheduled',
			'status_text'       => $params['text'],
			'status_created_at' => strtotime( 'now' ),
			'due_at'            => strtotime( $args['date'] ),
		);

	}

	/**
	 * Gets a post by ID.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $id   Post ID.
	 * @return  WP_Error|array
	 */
	public function get_post( $id ) {

		return $this->get( 'posts/' . $id );

	}

	/**
	 * Private function to perform a GET request
	 *
	 * @since  1.0.0
	 *
	 * @param  string $cmd        Command (required).
	 * @param  array  $params     Params (optional).
	 * @return WP_Error|array
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
	 * @return WP_Error|array
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
	 * @return  WP_Error|array
	 */
	private function request( $cmd, $method = 'get', $params = array() ) {

		// Check required parameters exist.
		if ( empty( $this->access_token ) ) {
			return new WP_Error( 'wpzinc_social_publisher_for_postiz_no_access_token', __( 'No access token was specified', 'wpzinc-social-publisher-for-postiz' ) );
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
							'Content-Type'  => 'application/json',
						),
						'body'      => wp_json_encode( $params ),
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

		// If an error is detected, return it.
		if ( array_key_exists( 'error', $body ) ) {
			// Messages can be a string or array.
			$messages = is_array( $body['message'] ) ? $body['message'] : array( $body['message'] );

			// Return WP_Error.
			return new WP_Error(
				'social_publisher_for_postiz_api_error',
				implode( '; ', $messages )
			);
		}

		// Return response body.
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
		$timeout = apply_filters( 'wpzinc_social_publisher_for_postiz_api_get_timeout', $timeout );

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
		$enable_ssl_verification = apply_filters( 'wpzinc_social_publisher_for_postiz_api_enable_ssl_verification', $enable_ssl_verification );

		return $enable_ssl_verification;

	}

}
