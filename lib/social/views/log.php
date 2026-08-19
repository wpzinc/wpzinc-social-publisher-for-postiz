<?php
/**
 * Outputs the Logs WP_List_Table.
 *
 * @package WPZinc\Social
 * @author  WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<header>
	<h1>
		<?php echo esc_html( $this->base->plugin->displayName ); ?>

		<span>
			<?php esc_html_e( 'Logs', 'wpzinc-social-publisher-for-postiz' ); ?>
		</span>
	</h1>
</header>

<hr class="wp-header-end" />

<div class="wrap">
	<?php
	// Search Subtitle.
	if ( $table->is_search() ) {
		?>
		<span class="subtitle left"><?php esc_html_e( 'Search results for', 'wpzinc-social-publisher-for-postiz' ); ?> &#8220;<?php echo esc_html( $table->get_search() ); ?>&#8221;</span>
		<?php
	}
	?>

	<form action="admin.php?page=<?php echo esc_attr( $this->base->plugin->name ); ?>-log" method="post" id="posts-filter">
		<?php
		// Output Search Box.
		$table->search_box( __( 'Search', 'wpzinc-social-publisher-for-postiz' ), 'wpzinc-social-log' );

		// Output Table.
		$table->display();
		?>
	</form>
</div><!-- /.wrap -->
