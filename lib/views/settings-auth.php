<?php
/**
 * Outputs the Settings screen when the Plugin is authenticated with
 * the third party API service.
 *
 * @package WPZinc_Social_Publisher_Pro
 * @author  WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="postbox wpzinc-vertical-tabbed-ui">
	<!-- Second level tabs -->
	<ul class="wpzinc-nav-tabs wpzinc-js-tabs" data-panels-container="#settings-container" data-panel=".panel" data-active="wpzinc-nav-tab-vertical-active">
		<li class="wpzinc-nav-tab lock">
			<a href="#authentication" class="wpzinc-nav-tab-vertical-active" data-documentation="<?php echo esc_attr( $this->base->plugin->documentation_url ); ?>/authentication-settings/">
				<?php esc_html_e( 'Authentication', 'wpzinc-social-publisher-for-postiz' ); ?>
			</a>
		</li>
		<li class="wpzinc-nav-tab default">
			<a href="#general-settings" data-documentation="<?php echo esc_attr( $this->base->plugin->documentation_url ); ?>/general-settings/">
				<?php esc_html_e( 'General Settings', 'wpzinc-social-publisher-for-postiz' ); ?>
			</a>
		</li>
		<li class="wpzinc-nav-tab file-text">
			<a href="#log-settings" data-documentation="<?php echo esc_attr( $this->base->plugin->documentation_url ); ?>/log-settings/">
				<?php esc_html_e( 'Log Settings', 'wpzinc-social-publisher-for-postiz' ); ?>
			</a>
		</li>
	</ul>

	<!-- Content -->
	<div id="settings-container" class="wpzinc-nav-tabs-content">
		<!-- Authentication -->
		<div id="authentication" class="panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'Connected Accounts', 'wpzinc-social-publisher-for-postiz' ); ?></h3>

					<p class="description">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %1$s: Plugin Name, %2$s: Social Media Service Name  */
								__( 'A list of %1$s accounts/organizations that are connected to %2$s.', 'wpzinc-social-publisher-for-postiz' ),
								$this->base->plugin->account,
								$this->base->plugin->displayName
							)
						);
						?>
					</p>
				</header>

				<?php
				// List connected accounts.
				foreach ( $this->base->get_class( 'settings' )->get_accounts() as $account_id => $account ) {
					$reconnect_url        = $this->base->get_class( 'api' )->get_oauth_url( $account_id );
					$refresh_profiles_url = add_query_arg(
						array(
							'page'  => $this->base->plugin->name . '-settings',
							$this->base->plugin->name . '-refresh-profiles' => $account_id,
							'nonce' => wp_create_nonce( $this->base->plugin->name . '-refresh-profiles' ),
						),
						admin_url( 'admin.php' )
					);
					$disconnect_url       = add_query_arg(
						array(
							'page'  => $this->base->plugin->name . '-settings',
							$this->base->plugin->name . '-disconnect' => $account_id,
							'nonce' => wp_create_nonce( $this->base->plugin->name . '-disconnect' ),
						),
						admin_url( 'admin.php' )
					);
					?>
					<div class="wpzinc-option">
						<div class="left">
							<strong><?php echo esc_html( $account['name'] ); ?></strong>
						</div>
						<div class="right">
							<a href="<?php echo esc_url( $refresh_profiles_url ); ?>" class="button button-secondary">
								<?php esc_html_e( 'Refresh Profiles', 'wpzinc-social-publisher-for-postiz' ); ?>
							</a>
							<a href="<?php echo esc_url( $reconnect_url ); ?>" class="button button-secondary">
								<?php esc_html_e( 'Reconnect', 'wpzinc-social-publisher-for-postiz' ); ?>
							</a>
							<a href="<?php echo esc_url( $disconnect_url ); ?>" class="button wpzinc-button-red">
								<?php esc_html_e( 'Disconnect', 'wpzinc-social-publisher-for-postiz' ); ?>
							</a>
						</div>
					</div>
					<?php
				}
				?>
			</div>   
		</div>

		<!-- General Settings -->
		<div id="general-settings" class="panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'General Settings', 'wpzinc-social-publisher-for-postiz' ); ?></h3>
					<p class="description">
						<?php esc_html_e( 'Provides options for logging, Post default level settings and whether to use WordPress Cron when publishing or updating Posts.', 'wpzinc-social-publisher-for-postiz' ); ?>
					</p>
				</header>

				<div class="wpzinc-option">
					<div class="left">
						<label for="test_mode"><?php esc_html_e( 'Enable Test Mode', 'wpzinc-social-publisher-for-postiz' ); ?></label>
					</div>
					<div class="right">
						<input type="checkbox" name="test_mode" id="test_mode" value="1" <?php checked( $this->get_setting( '', 'test_mode' ), 1 ); ?> />

						<p class="description">
							<?php
							echo esc_html(
								sprintf(
								/* translators: Social Media Service Name  */
									__( 'If enabled, status(es) are not sent to %s, but will appear in the Log, if logging is enabled. This is useful to test status text, conditions etc.', 'wpzinc-social-publisher-for-postiz' ),
									$this->base->plugin->account
								)
							);
							?>
						</p>
					</div>
				</div>

				<div class="wpzinc-option">
					<div class="left">
						<label for="force_trailing_forwardslash"><?php esc_html_e( 'Force Trailing Forwardslash?', 'wpzinc-social-publisher-for-postiz' ); ?></label>
					</div>
					<div class="right">
						<input type="checkbox" name="force_trailing_forwardslash" id="force_trailing_forwardslash" value="1" <?php checked( $this->get_setting( '', 'force_trailing_forwardslash' ), 1 ); ?> />

						<p class="description">
							<?php
							esc_html_e( 'If enabled, any URLs in statuses will always end with a forwardslash. This might be required if the wrong image is shared with a status.', 'wpzinc-social-publisher-for-postiz' );
							?>
							<br />
							<?php
							printf(
								'%1$s <a href="options-permalink.php">%2$s</a> %3$s',
								esc_html__( 'It\'s better to ensure your', 'wpzinc-social-publisher-for-postiz' ),
								esc_html__( 'Permalink', 'wpzinc-social-publisher-for-postiz' ),
								esc_html__( 'settings end with a forwardslash, but this option is a useful fallback if changing Permalink structure isn\'t possible.', 'wpzinc-social-publisher-for-postiz' )
							);
							?>
						</p>
					</div>
				</div>

				<div class="wpzinc-option">
					<div class="left">
						<label for="proxy"><?php esc_html_e( 'Use Proxy?', 'wpzinc-social-publisher-for-postiz' ); ?></label>
					</div>
					<div class="right">
						<input type="checkbox" name="proxy" id="proxy" value="1" <?php checked( $this->get_setting( '', 'proxy' ), 1 ); ?> />

						<p class="description">
							<?php
							echo esc_html(
								sprintf(
								/* translators: %1$s: Social Media Service Name , %2$s: Social Media Service Name  */
									__( 'If enabled, statuses sent to %1$s are performed through our proxy. This is useful if your ISP or host\'s country prevents access to %1$s.', 'wpzinc-social-publisher-for-postiz' ),
									$this->base->plugin->account,
									$this->base->plugin->account
								)
							);
							?>
							<br />
							<?php esc_html_e( 'You may still need to use a VPN for initial Authentication when setting up the Plugin for the first time.', 'wpzinc-social-publisher-for-postiz' ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Log Settings -->
		<div id="log-settings" class="panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'Log Settings', 'wpzinc-social-publisher-for-postiz' ); ?></h3>
					<p class="description">
						<?php esc_html_e( 'Provides options to enable logging, display logs on Posts and how long to keep logs for.', 'wpzinc-social-publisher-for-postiz' ); ?>
					</p>
				</header>

				<div class="wpzinc-option">
					<div class="left">
						<label for="log_enabled"><?php esc_html_e( 'Enable Logging?', 'wpzinc-social-publisher-for-postiz' ); ?></label>
					</div>
					<div class="right">
						<input type="checkbox" name="log[enabled]" id="log_enabled" value="1" <?php checked( $this->get_setting( 'log', '[enabled]' ), 1 ); ?> data-conditional="enable_logging" />
						<p class="description">
							<?php
							if ( $this->get_setting( 'log', '[enabled]' ) ) {
								printf(
									'%1$s <a href="%2$s">%3$s</a> %4$s',
									esc_html__( 'If enabled, the', 'wpzinc-social-publisher-for-postiz' ),
									esc_html( admin_url( 'admin.php?page=' . $this->base->plugin->name . '-log' ) ),
									esc_html__( 'Plugin Logs', 'wpzinc-social-publisher-for-postiz' ),
									esc_html(
										sprintf(
											/* translators: Social Media Service Name  */
											__( 'will detail status(es) sent to %s, including any errors or reasons why no status(es) were sent.', 'wpzinc-social-publisher-for-postiz' ),
											$this->base->plugin->account
										)
									)
								);
							} else {
								// Don't link "Plugin Log" text, as Logs are disabled so it won't show anything.
								echo esc_html(
									sprintf(
									/* translators: %1$s: Social Media Service Name  */
										__( 'If enabled, the Plugin Logs will detail status(es) sent to %1$s, including any errors or reasons why no status(es) were sent.', 'wpzinc-social-publisher-for-postiz' ),
										$this->base->plugin->account
									)
								);
							}
							?>
						</p>
					</div>
				</div>

				<div id="enable_logging">
					<div class="wpzinc-option">
						<div class="left">
							<label for="log_display_on_posts"><?php esc_html_e( 'Display on Posts?', 'wpzinc-social-publisher-for-postiz' ); ?></label>
						</div>
						<div class="right">
							<input type="checkbox" name="log[display_on_posts]" id="log_display_on_posts" value="1" <?php checked( $this->get_setting( 'log', '[display_on_posts]' ), 1 ); ?> />
			   
							<p class="description">
								<?php
								if ( $this->get_setting( 'log', '[enabled]' ) ) {
									printf(
										'%1$s <a href="%2$s">%3$s</a> %4$s',
										esc_html__( 'If enabled, a Log will be displayed when editing a Post.  Logs are always available through the', 'wpzinc-social-publisher-for-postiz' ),
										esc_html( admin_url( 'admin.php?page=' . $this->base->plugin->name . '-log' ) ),
										esc_html__( 'Plugin Logs', 'wpzinc-social-publisher-for-postiz' ),
										esc_html__( 'screen', 'wpzinc-social-publisher-for-postiz' )
									);
								} else {
									// Don't link "Plugin Log" text, as Logs are disabled so it won't show anything.
									esc_html_e( 'If enabled, a Log will be displayed when editing a Post.  Logs are always available through the Plugin Logs screen.', 'wpzinc-social-publisher-for-postiz' );
								}
								?>
							</p>
						</div>
					</div>

					<div class="wpzinc-option">
						<div class="left">
							<label for="log_level"><?php esc_html_e( 'Log Level', 'wpzinc-social-publisher-for-postiz' ); ?></label>
						</div>
						<div class="right">
							<?php
							$log_levels_settings = $this->get_setting(
								'log',
								'log_level',
								array(
									'success',
									'test',
									'pending',
									'warning',
									'error',
								)
							);

							foreach ( $log_levels as $log_level => $label ) {
								?>
								<label for="log_level_<?php echo esc_attr( $log_level ); ?>">
									<input  type="checkbox" 
											name="log[log_level][]" 
											id="log_level_<?php echo esc_attr( $log_level ); ?>"
											value="<?php echo esc_attr( $log_level ); ?>"
											<?php echo ( in_array( $log_level, $log_levels_settings, true ) || $log_level === 'error' ? ' checked' : '' ); ?>
											<?php echo ( ( $log_level === 'error' ) ? ' disabled' : '' ); ?>
											/>

									<?php echo esc_html( $label ); ?>
								</label>
								<br />
								<?php
							}
							?>

							<p class="description">
								<?php esc_html_e( 'Defines which log results to save to the Log database. Errors will always be logged.', 'wpzinc-social-publisher-for-postiz' ); ?>
							</p>
						</div>
					</div>

					<div class="wpzinc-option">
						<div class="left">
							<label for="log_preserve_days"><?php esc_html_e( 'Preserve Logs', 'wpzinc-social-publisher-for-postiz' ); ?></strong>
						</div>
						<div class="right">
							<input type="number" name="log[preserve_days]" id="log_preserve_days" value="<?php echo esc_attr( $this->get_setting( 'log', '[preserve_days]' ) ); ?>" min="0" max="9999" step="1" />
							<?php esc_html_e( 'days', 'wpzinc-social-publisher-for-postiz' ); ?>
					   
							<p class="description">
								<?php
								esc_html_e( 'The number of days to preserve logs for.  Zero means logs are kept indefinitely.', 'wpzinc-social-publisher-for-postiz' );
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
