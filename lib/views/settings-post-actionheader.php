<?php
/**
 * Outputs status settings when editing a Post.
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!-- Action Header -->
<div class="postbox">
	<header>
		<h3>
			<?php
			echo esc_html(
				sprintf(
				/* translators: %1$s: Social Media Service (Facebook, Twitter etc.), %2$s: Social Media Profile Name */
					__( '%1$s: %2$s: Settings', 'postiz-auto-poster' ),
					$profile['formatted_service'],
					$profile['formatted_username']
				)
			);
			?>
		</h3>

		<?php
		// If this Profile's timezone doesn't match WordPress' timezone, show a warning.
		if ( isset( $profile['timezone'] ) ) {
			$timezones_match = $this->base->get_class( 'validation' )->timezones_match(
				$profile['timezone'],
				$profile['formatted_username'],
				$this->base->get_class( 'api' )->get_timezone_settings_url( $profile['id'] )
			);
			if ( is_wp_error( $timezones_match ) ) {
				?>
				<div class="notice-inline notice-warning">
					<p>
						<?php
						echo wp_kses(
							$timezones_match->get_error_message(),
							array(
								'a'  => array(
									'href'   => array(),
									'target' => array(),
								),
								'br' => array(),
							)
						);
						?>
					</p>
				</div>
				<?php
			}
		}
		?>
	</header>

	<!-- Account Enabled -->
	<div class="wpzinc-option">        
		<div class="left">
			<label for="<?php echo esc_attr( $profile_id ); ?>_enabled"><?php esc_html_e( 'Account Enabled', 'postiz-auto-poster' ); ?></label>
		</div>
		<div class="right">
			<input type="checkbox" id="<?php echo esc_attr( $profile_id ); ?>_enabled" class="enable" name="<?php echo esc_attr( $this->base->plugin->name ); ?>[<?php echo esc_attr( $profile_id ); ?>][enabled]" id="<?php echo esc_attr( $profile_id ); ?>_enabled" value="1"<?php checked( $this->get_setting( $post_type, '[' . $profile_id . '][enabled]', 0 ), 1, true ); ?> data-tab="profile-<?php echo esc_attr( $profile_id ); ?>" />
			<p class="description"><?php esc_html_e( 'Enabling this social media account means that Posts will be sent to this social media account.', 'postiz-auto-poster' ); ?></p>
		</div>
	</div>
</div>
