<?php
/**
 * Outputs the single status configuration form.  Its values are populated by statuses.js, based
 * on the status that has been selected for editing.
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div id="<?php echo esc_attr( $this->base->plugin->name ); ?>-status-form-container" class="hidden">
	<div id="<?php echo esc_attr( $this->base->plugin->name ); ?>-status-form" class="wp-to-social-pro-status-form">
		<div class="notice-inline notice-warning pinterest hidden full">
			<p>
				<?php
				esc_html_e( 'You need to create at least one Pinterest Board, and then refresh the screen to choose the board to post this status to.', 'social-publisher-for-postiz' );
				?>
				<a href="<?php echo esc_attr( $this->base->plugin->documentation_url ); ?>/status-settings/#buffer-pinterest" target="_blank">
					<?php echo esc_html_e( 'Click here for instructions on creating a Pinterest board.', 'social-publisher-for-postiz' ); ?>
				</a>
			</p>
		</div>

		<!-- Status Type and Text -->
		<div class="wpzinc-option status">
			<div class="full">
				<h3><?php esc_html_e( 'Status Type and Text', 'social-publisher-for-postiz' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'The type of status to create and its text.', 'social-publisher-for-postiz' ); ?>
				</p>

				<select name="<?php echo esc_attr( $this->base->plugin->name ); ?>_post_type" class="post_type" size="1">
					<?php
					foreach ( $this->base->get_class( 'common' )->get_status_post_type_options() as $status_post_type_key => $status_post_type_label ) {
						?>
						<option value="<?php echo esc_attr( $status_post_type_key ); ?>">
							<?php echo esc_attr( $status_post_type_label ); ?>
						</option>
						<?php
					}
					?>
				</select>

				<?php
				// Tags.
				$textarea = 'textarea.message';
				require 'settings-post-action-status-tags.php';
				?>

				<textarea name="<?php echo esc_attr( $this->base->plugin->name ); ?>_message" rows="3" class="widefat wpzinc-autosize-js message"></textarea>

				<?php
				// If we're editing a Post, Page or CPT, show the chararcter count.
				if ( isset( $post ) && ! empty( $post ) ) {
					?>
					<small class="characters">
						<span class="character-count"></span>
						<?php esc_html_e( 'characters', 'social-publisher-for-postiz' ); ?>
					</small>
					<?php
				}
				?>
			</div>
		</div>

		<!-- Schedule -->
		<div class="wpzinc-option status">
			<div class="full">
				<h3><?php esc_html_e( 'Schedule', 'social-publisher-for-postiz' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'When the status should be added to social media.', 'social-publisher-for-postiz' ); ?>
				</p>

				<select name="<?php echo esc_attr( $this->base->plugin->name ); ?>_schedule" size="1" class="schedule widefat">
					<?php
					foreach ( $this->base->get_class( 'common' )->get_schedule_options( $post_type, $is_post_screen ) as $schedule_option => $label ) {
						?>
						<option value="<?php echo esc_attr( $schedule_option ); ?>"><?php echo esc_attr( $label ); ?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>

		<!-- Link -->
		<div class="wpzinc-option link">
			<div class="full">
				<h3><?php esc_html_e( 'Link', 'social-publisher-for-postiz' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'The "primary" URL to use for the link preview / card. Additional links can be included in the status text above.', 'social-publisher-for-postiz' ); ?>
				</p>

				<input type="text" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_url" class="widefat url" />
			</div>
		</div>

		<!-- Pinterest -->
		<div class="wpzinc-option pinterest hidden">
			<div class="full">
				<h3><?php esc_html_e( 'Pinterest', 'social-publisher-for-postiz' ); ?></h3>
				<p class="description">
					<?php
					esc_html_e( 'Define the Pinterest Board for this status to be sent to.', 'social-publisher-for-postiz' );
					?>
				</p>

				<table class="widefat fixed striped">
					<tbody>
						<tr>
							<td width="20%">
								<label for="<?php echo esc_attr( $this->base->plugin->name ); ?>_pinterest_board">
									<?php esc_html_e( 'Board', 'social-publisher-for-postiz' ); ?>
								</label>
							</td>
							<td>
								<!-- Pinterest: Sub Profile -->
								<select name="<?php echo esc_attr( $this->base->plugin->name ); ?>_pinterest[board]" id="<?php echo esc_attr( $this->base->plugin->name ); ?>_pinterest_board" size="1" class="widefat"></select> 
								<input type="url" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_pinterest[board]" id="<?php echo esc_attr( $this->base->plugin->name ); ?>_pinterest_board" placeholder="<?php esc_attr_e( 'Pinterest Board URL', 'social-publisher-for-postiz' ); ?>" class="widefat" />
							</td>
						</tr>

						<?php
						if ( $this->base->supports( 'pinterest_title' ) ) {
							?>
							<tr>
								<td>
									<label for="pinterest_title">
										<?php esc_html_e( 'Pin Title', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<input type="text" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_pinterest[title]" id="pinterest_title" placeholder="<?php esc_attr_e( 'Pin Title', 'social-publisher-for-postiz' ); ?>" class="widefat" />
									<p class="description">
										<?php esc_html_e( 'An optional title. Text Tags are supported.', 'social-publisher-for-postiz' ); ?>
									</p>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>

		<?php
		if ( $this->base->supports( 'googlebusiness' ) ) {
			?>
			<!-- Google Business Profile -->
			<div class="wpzinc-option googlebusiness hidden">
				<div class="full">
					<h3><?php esc_html_e( 'Google Business Profile', 'social-publisher-for-postiz' ); ?></h3>
					<p class="description">
						<?php
						echo esc_html_e( 'Optional: Define the status type (What\'s New, Offer or Event) and additional structured fields / data.', 'social-publisher-for-postiz' );
						?>
					</p>

					<table class="widefat fixed striped">
						<tbody>
							<tr>
								<td width="20%">
									<label for="googlebusiness_post_type">
										<?php esc_html_e( 'Post Type', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<select name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[post_type]" id="googlebusiness_post_type" size="1" class="widefat">
										<option value="whats_new"><?php esc_attr_e( 'What\'s New', 'social-publisher-for-postiz' ); ?></option>
										<option value="offer"><?php esc_attr_e( 'Offer', 'social-publisher-for-postiz' ); ?></option>
										<option value="event"><?php esc_attr_e( 'Event', 'social-publisher-for-postiz' ); ?></option>
									</select>
								</td>
							</tr>
							<tr class="whats_new event">
								<td>
									<label for="googlebusiness_cta">
										<?php esc_html_e( 'Call to Action', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<select name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[cta]" id="googlebusiness_cta" size="1" class="widefat">
										<option value="book"><?php esc_attr_e( 'Book', 'social-publisher-for-postiz' ); ?></option>
										<option value="order"><?php esc_attr_e( 'Order', 'social-publisher-for-postiz' ); ?></option>
										<option value="shop"><?php esc_attr_e( 'Shop', 'social-publisher-for-postiz' ); ?></option>
										<option value="learn_more"><?php esc_attr_e( 'Learn More', 'social-publisher-for-postiz' ); ?></option>
										<option value="signup"><?php esc_attr_e( 'Sign Up', 'social-publisher-for-postiz' ); ?></option>
									</select>
								</td>
							</tr>
							<tr class="offer event">
								<td>
									<label for="googlebusiness_start_date_option">
										<?php esc_html_e( 'Start Date', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<select name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[start_date_option]" id="googlebusiness_start_date_option" size="1" class="widefat">
										<?php
										foreach ( $this->base->get_class( 'common' )->get_google_business_start_date_options( $post_type ) as $schedule_option => $label ) {
											?>
											<option value="<?php echo esc_attr( $schedule_option ); ?>"><?php echo esc_attr( $label ); ?></option>
											<?php
										}
										?>
									</select>

									<input type="text" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[start_date]" id="googlebusiness_start_date" placeholder="<?php esc_attr_e( 'Custom Meta Field Name', 'social-publisher-for-postiz' ); ?>" />
								</td>
							</tr>
							<tr class="offer event">
								<td>
									<label for="googlebusiness_end_date_option">
										<?php esc_html_e( 'End Date', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<select name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[end_date_option]" id="googlebusiness_end_date_option" size="1" class="widefat">
										<?php
										foreach ( $this->base->get_class( 'common' )->get_google_business_end_date_options( $post_type ) as $schedule_option => $label ) {
											?>
											<option value="<?php echo esc_attr( $schedule_option ); ?>"><?php echo esc_attr( $label ); ?></option>
											<?php
										}
										?>
									</select>

									<input type="text" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[end_date]" id="googlebusiness_end_date" placeholder="<?php esc_attr_e( 'Custom Meta Field Name', 'social-publisher-for-postiz' ); ?>" />
								</td>
							</tr>
							<tr class="offer event">
								<td>
									<label for="googlebusiness_title">
										<?php esc_html_e( 'Event / Offer Title', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<input type="text" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[title]" id="googlebusiness_title" class="widefat" />
								</td>
							</tr>
							<tr class="offer">
								<td>
									<label for="googlebusiness_code">
										<?php esc_html_e( 'Coupon Code', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<input type="text" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[code]" id="googlebusiness_code" class="widefat" />
								</td>
							</tr>
							<tr class="offer">
								<td>
									<label for="googlebusiness_terms">
										<?php esc_html_e( 'Terms and Conditions Text', 'social-publisher-for-postiz' ); ?>
									</label>
								</td>
								<td>
									<input type="text" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_googlebusiness[terms]" id="googlebusiness_terms" class="widefat" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php
		}
		?>

		<!-- Images -->
		<div class="wpzinc-option images">
			<div class="full">
				<h3><?php esc_html_e( 'Image', 'social-publisher-for-postiz' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'The type of image to use.', 'social-publisher-for-postiz' ); ?>
				</p>

				<table class="widefat fixed striped">
					<tbody>
						<tr>
							<td width="20%">
								<label for="<?php echo esc_attr( $this->base->plugin->name ); ?>_image">
									<?php esc_html_e( 'Image', 'social-publisher-for-postiz' ); ?>
								</label>
							</td>
							<td>
								<select id="<?php echo esc_attr( $this->base->plugin->name ); ?>_image" name="<?php echo esc_attr( $this->base->plugin->name ); ?>_image" size="1" class="image">
									<?php
									foreach ( $this->base->get_class( 'image' )->get_status_image_options( false, $post_type ) as $value => $image_option ) {
										?>
										<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_attr( $image_option['label'] ); ?></option>
										<?php
									}
									?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
