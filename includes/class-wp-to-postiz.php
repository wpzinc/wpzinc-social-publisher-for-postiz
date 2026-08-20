<?php
/**
 * WP to Postiz class.
 *
 * @package WP_To_Postiz
 * @author WP Zinc
 */

/**
 * Main WP to Postiz class, used to load the Plugin.
 *
 * @package   WP_To_Postiz
 * @author    WP Zinc
 * @version   1.0.0
 */
class WP_To_Postiz {

	/**
	 * Holds the class object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object|null
	 */
	public static $instance;

	/**
	 * Plugin
	 *
	 * @since   3.0.0
	 *
	 * @var     object
	 */
	public $plugin;

	/**
	 * Dashboard
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	public $dashboard;

	/**
	 * Classes
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	public $classes;

	/**
	 * Constructor. Acts as a bootstrap to load the rest of the plugin
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Plugin Details.
		$this->plugin              = new stdClass();
		$this->plugin->name        = 'wpzinc-social-publisher-for-postiz';
		$this->plugin->filter_name = 'wpzinc_social_publisher_for_postiz';
		$this->plugin->displayName = 'WP to Postiz';

		$this->plugin->settingsName      = 'wpzinc-social-publisher-for-postiz-pro'; // Settings key - used in both Free + Pro, and for oAuth.
		$this->plugin->account           = 'Postiz';
		$this->plugin->version           = WPZINC_SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_VERSION;
		$this->plugin->buildDate         = WPZINC_SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_BUILD_DATE;
		$this->plugin->folder            = WPZINC_SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_PATH;
		$this->plugin->url               = WPZINC_SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_URL;
		$this->plugin->documentation_url = 'https://www.wpzinc.com/documentation/wordpress-to-postiz-pro';
		$this->plugin->support_url       = 'https://www.wpzinc.com/support';
		$this->plugin->upgrade_url       = 'https://www.wpzinc.com/plugins/wordpress-to-postiz-pro';

		// Logo.
		$this->plugin->logo                        = WPZINC_SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_URL . 'lib/social/assets/images/icons/postiz-dark.svg';
		$this->plugin->header_background_color     = '#ffffff';
		$this->plugin->header_primary_text_color   = '#3d3d3d';
		$this->plugin->header_secondary_text_color = '#6e6e6e';

		// Review.
		$this->plugin->review_name   = 'wpzinc-social-publisher-for-postiz';
		$this->plugin->review_notice = sprintf(
			'Thanks for using %s to schedule your social media statuses on %s!',
			$this->plugin->displayName,
			$this->plugin->account
		);

		// ConvertKit Form UID.
		$this->plugin->convertkit_form_uid = 'adb5765302';

		// Default Settings.
		$this->plugin->default_schedule = 'immediate';

		// Defer loading of Plugin Classes.
		add_action( 'init', array( $this, 'initialize' ), 1 );
		add_action( 'init', array( $this, 'upgrade' ), 2 );

		// Admin Menus.
		add_action( $this->plugin->filter_name . '_admin_admin_menu', array( $this, 'admin_menus' ) );

	}

	/**
	 * Register menus and submenus.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $minimum_capability     Minimum required capability.
	 */
	public function admin_menus( $minimum_capability ) {

		// Menus.
		add_menu_page( $this->plugin->displayName, $this->plugin->displayName, $minimum_capability, $this->plugin->name . '-settings', array( $this->get_class( 'admin' ), 'settings_screen' ), $this->plugin->url . 'lib/social/assets/images/icons/' . strtolower( $this->plugin->account ) . '-light.svg' );

		// Register Submenu Pages.
		$settings_page = add_submenu_page( $this->plugin->name . '-settings', __( 'Settings', 'wpzinc-social-publisher-for-postiz' ), __( 'Settings', 'wpzinc-social-publisher-for-postiz' ), $minimum_capability, $this->plugin->name . '-settings', array( $this->get_class( 'admin' ), 'settings_screen' ) );

		// Logs.
		if ( $this->get_class( 'log' )->is_enabled() ) {
			$log_page = add_submenu_page( $this->plugin->name . '-settings', __( 'Logs', 'wpzinc-social-publisher-for-postiz' ), __( 'Logs', 'wpzinc-social-publisher-for-postiz' ), $minimum_capability, $this->plugin->name . '-log', array( $this->get_class( 'admin' ), 'log_screen' ) );
			add_action( "load-$log_page", array( $this->get_class( 'log' ), 'add_screen_options' ) );
		}

		$upgrade_page = add_submenu_page( $this->plugin->name . '-settings', __( 'Upgrade', 'wpzinc-social-publisher-for-postiz' ), __( 'Upgrade', 'wpzinc-social-publisher-for-postiz' ), $minimum_capability, $this->plugin->name . '-upgrade', array( $this->get_class( 'admin' ), 'upgrade_screen' ) );

	}

	/**
	 * Initializes required classes
	 *
	 * @since   3.4.9
	 */
	public function initialize() {

		// Define translation strings.
		$this->plugin->review_notice = sprintf(
			/* translators: Plugin Name */
			__( 'Thanks for using %s to schedule your social media statuses on Postiz!', 'wpzinc-social-publisher-for-postiz' ),
			$this->plugin->displayName
		);

		// Upgrade Reasons.
		$this->plugin->upgrade_reasons = array(
			array(
				__( 'Multiple Postiz Account Support', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Pro supports connecting multiple Postiz accounts to a single WordPress site', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Multiple, Customisable Status Messages', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Each Post Type and Social Network can have multiple, unique status message and settings', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Conditionally send Status Messages', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Only send status(es) to Postiz based on Post Author(s), Taxonomy Term(s) and/or Custom Field Values', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'More Scheduling Options', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Each status update can be posted immediately, at a custom time, or at a specific date and time', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Dynamic Status Tags', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Dynamically build status updates with data from the Post Author and Custom Fields', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Separate Statuses per Social Network', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Define different statuses for each Post Type and Social Network', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Per-Post Settings', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Override Settings on Individual Posts: Each Post can have its own Postiz settings', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Repost Old Posts', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Automatically Revive Old Posts that haven\'t been updated in a while, choosing the number of days, weeks or years to re-share content on social media.', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Bulk Publish Old Posts', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Manually re-share evergreen WordPress content and revive old posts with the Bulk Publish option', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'The Events Calendar, Event Manager and Modern Events Calendar Integration', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Schedule Posts to Postiz based on your Event\'s Start or End date, and display Event-specific details in your status updates', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'SEO Integration', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Display SEO-specific information in your status updates from All-In-One SEO Pack, Rank Math, SEOPress and Yoast SEO', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'WooCommerce Integration', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Display Product-specific information in your status updates', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Autoblogging and Frontend Post Submission Integration', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Pro supports autoblogging and frontend post submission Plugins, including User Submitted Posts, WP Property Feed, WPeMatico and WP Job Manager', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Shortcode Support', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Use shortcodes in status updates', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'Full Image Control', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Choose to display one or more images in your status updates, from the Post\'s Featured Image, the Media Gallery, the Post Content or an Advanced Custom Fields Image or Gallery.', 'wpzinc-social-publisher-for-postiz' ),
			),
			array(
				__( 'WP-Cron and WP-CLI Compatible', 'wpzinc-social-publisher-for-postiz' ),
				__( 'Optionally enable WP-Cron to send status updates via Cron, speeding up UI performance and/or choose to use WP-CLI for reposting old posts', 'wpzinc-social-publisher-for-postiz' ),
			),
		);

		// Shared admin module (autoloaded from lib/shared).
		$this->dashboard = new \WPZinc\Shared\Admin_UI( $this->plugin );

		// Initialize Plugin classes.
		$this->classes = new stdClass();

		// Initialize required classes.
		$this->classes->admin         = new \WPZinc\Social\Admin( self::$instance );
		$this->classes->ajax          = new \WPZinc\Social\Ajax( self::$instance );
		$this->classes->api           = new \WPZinc\Social\Postiz_API( self::$instance );
		$this->classes->common        = new \WPZinc\Social\Common( self::$instance );
		$this->classes->cron          = new \WPZinc\Social\Cron( self::$instance );
		$this->classes->date          = new \WPZinc\Social\Date( self::$instance );
		$this->classes->image         = new \WPZinc\Social\Image( self::$instance );
		$this->classes->install       = new \WPZinc\Social\Install( self::$instance );
		$this->classes->log           = new \WPZinc\Social\Log( self::$instance );
		$this->classes->media_library = new \WPZinc\Social\Media_Library( self::$instance );
		$this->classes->notices       = new \WPZinc\Social\Notices( self::$instance );
		$this->classes->post          = new \WPZinc\Social\Post( self::$instance );
		$this->classes->publish       = new \WPZinc\Social\Publish( self::$instance );
		$this->classes->screen        = new \WPZinc\Social\Screen( self::$instance );
		$this->classes->settings      = new \WPZinc\Social\Settings( self::$instance );
		$this->classes->validation    = new \WPZinc\Social\Validation( self::$instance );

	}

	/**
	 * Runs the upgrade routine once the plugin has loaded
	 *
	 * @since   1.0.0
	 */
	public function upgrade() {

		// Run upgrade routine.
		$this->get_class( 'install' )->upgrade();

	}

	/**
	 * Returns the given class
	 *
	 * @since   1.0.0
	 *
	 * @param   string $name   Class Name.
	 */
	public function get_class( $name ) {

		// If the class hasn't been loaded, throw a WordPress die screen
		// to avoid a PHP fatal error.
		if ( ! isset( $this->classes->{ $name } ) ) {
			// Define the error.
			$error = new WP_Error(
				'social_publisher_for_postiz_get_class',
				sprintf(
					/* translators: %1$s: Plugin Name, %2$s: PHP class name */
					__( '%1$s: Error: Could not load Plugin class %2$s', 'wpzinc-social-publisher-for-postiz' ),
					$this->plugin->displayName,
					$name
				)
			);

			// Depending on the request, return or display an error.
			// Admin UI.
			if ( is_admin() ) {
				wp_die(
					esc_html( $error->get_error_message() ),
					sprintf(
						/* translators: Plugin Name */
						esc_html__( '%s: Error', 'wpzinc-social-publisher-for-postiz' ),
						esc_html( $this->plugin->displayName )
					),
					array(
						'back_link' => true,
					)
				);
			}

			// Cron / CLI.
			return $error;
		}

		// Return the class object.
		return $this->classes->{ $name };

	}

	/**
	 * Helper method to determine whether this Plugin supports a specific feature.
	 *
	 * Typically used by the lib/ classes.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $feature    Feature.
	 * @return  bool                Feature Supported
	 */
	public function supports( $feature ) {

		// Define supported featured.
		$supported_features = array(
			'webp',
		);

		return in_array( $feature, $supported_features, true );

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since   1.0.0
	 *
	 * @return  object Class.
	 */
	public static function get_instance() {

		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}
