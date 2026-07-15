<?php
/**
 * Social Publisher for Postiz class.
 *
 * @package social_publisher_for_postiz
 * @author WP Zinc
 */

/**
 * Main Social Publisher for Postiz class, used to load the Plugin.
 *
 * @package   social_publisher_for_postiz
 * @author    WP Zinc
 * @version   1.0.0
 */
class Social_Publisher_For_Postiz {

	/**
	 * Holds the class object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	public static $instance;

	/**
	 * Plugin
	 *
	 * @since   3.0.0
	 *
	 * @var     object
	 */
	public $plugin = '';

	/**
	 * Dashboard
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	public $dashboard = '';

	/**
	 * Classes
	 *
	 * @since   1.0.0
	 *
	 * @var     array
	 */
	public $classes = '';

	/**
	 * Constructor. Acts as a bootstrap to load the rest of the plugin
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Plugin Details.
		$this->plugin              = new stdClass();
		$this->plugin->name        = 'social-publisher-for-postiz';
		$this->plugin->filter_name = 'social_publisher_for_postiz';
		$this->plugin->displayName = 'Social Publisher for Postiz';

		$this->plugin->settingsName      = 'social-publisher-for-postiz-pro'; // Settings key - used in both Free + Pro, and for oAuth.
		$this->plugin->account           = 'Postiz';
		$this->plugin->version           = SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_VERSION;
		$this->plugin->buildDate         = SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_BUILD_DATE;
		$this->plugin->folder            = SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_PATH;
		$this->plugin->url               = SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_URL;
		$this->plugin->documentation_url = 'https://www.wpzinc.com/documentation/wordpress-buffer-pro/';
		$this->plugin->support_url       = 'https://www.wpzinc.com/support';

		// Logo.
		$this->plugin->logo                        = SOCIAL_PUBLISHER_FOR_POSTIZ_PLUGIN_URL . 'lib/assets/images/icons/postiz-dark.svg';
		$this->plugin->header_background_color     = '#ffffff';
		$this->plugin->header_primary_text_color   = '#3d3d3d';
		$this->plugin->header_secondary_text_color = '#6e6e6e';

		// Review.
		$this->plugin->review_name   = 'social-publisher-for-postiz';
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
	 * @since   3.9.7
	 *
	 * @param   string $minimum_capability     Minimum required capability.
	 */
	public function admin_menus( $minimum_capability ) {

		// Menus.
		add_menu_page( $this->plugin->displayName, $this->plugin->displayName, $minimum_capability, $this->plugin->name . '-settings', array( $this->get_class( 'admin' ), 'settings_screen' ), $this->plugin->url . 'lib/assets/images/icons/' . strtolower( $this->plugin->account ) . '-light.svg' );

		// Register Submenu Pages.
		$settings_page = add_submenu_page( $this->plugin->name . '-settings', __( 'Settings', 'social-publisher-for-postiz' ), __( 'Settings', 'social-publisher-for-postiz' ), $minimum_capability, $this->plugin->name . '-settings', array( $this->get_class( 'admin' ), 'settings_screen' ) );

		// Logs.
		if ( $this->get_class( 'log' )->is_enabled() ) {
			$log_page = add_submenu_page( $this->plugin->name . '-settings', __( 'Logs', 'social-publisher-for-postiz' ), __( 'Logs', 'social-publisher-for-postiz' ), $minimum_capability, $this->plugin->name . '-log', array( $this->get_class( 'admin' ), 'log_screen' ) );
			add_action( "load-$log_page", array( $this->get_class( 'log' ), 'add_screen_options' ) );
		}

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
			__( 'Thanks for using %s to schedule your social media statuses on Postiz!', 'social-publisher-for-postiz' ),
			$this->plugin->displayName
		);

		// Dashboard Submodule.
		if ( ! class_exists( 'WPZincDashboardWidget' ) ) {
			require_once $this->plugin->folder . '_modules/dashboard/class-wpzincdashboardwidget.php';
		}
		$this->dashboard = new WPZincDashboardWidget( $this->plugin );

		// Initialize Plugin classes.
		$this->classes = new stdClass();

		// Initialize required classes.
		$this->classes->admin         = new WP_To_Social_Pro_Admin( self::$instance );
		$this->classes->ajax          = new WP_To_Social_Pro_AJAX( self::$instance );
		$this->classes->api           = new WP_To_Social_Pro_Postiz_API( self::$instance );
		$this->classes->common        = new WP_To_Social_Pro_Common( self::$instance );
		$this->classes->cron          = new WP_To_Social_Pro_Cron( self::$instance );
		$this->classes->date          = new WP_To_Social_Pro_Date( self::$instance );
		$this->classes->image         = new WP_To_Social_Pro_Image( self::$instance );
		$this->classes->install       = new WP_To_Social_Pro_Install( self::$instance );
		$this->classes->log           = new WP_To_Social_Pro_Log( self::$instance );
		$this->classes->media_library = new WP_To_Social_Pro_Media_Library( self::$instance );
		$this->classes->notices       = new WP_To_Social_Pro_Notices( self::$instance );
		$this->classes->post          = new WP_To_Social_Pro_Post( self::$instance );
		$this->classes->publish       = new WP_To_Social_Pro_Publish( self::$instance );
		$this->classes->screen        = new WP_To_Social_Pro_Screen( self::$instance );
		$this->classes->settings      = new WP_To_Social_Pro_Settings( self::$instance );
		$this->classes->validation    = new WP_To_Social_Pro_Validation( self::$instance );

	}

	/**
	 * Runs the upgrade routine once the plugin has loaded
	 *
	 * @since   3.2.5
	 */
	public function upgrade() {

		// Run upgrade routine.
		$this->get_class( 'install' )->upgrade();

	}

	/**
	 * Returns the given class
	 *
	 * @since   3.4.9
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
					__( '%1$s: Error: Could not load Plugin class %2$s', 'social-publisher-for-postiz' ),
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
						esc_html__( '%s: Error', 'social-publisher-for-postiz' ),
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
	 * @since   3.5.5
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
	 * @since   3.1.4
	 *
	 * @return  object Class.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}
