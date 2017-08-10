<?php
/**
 * Pages: Admin batch analysis page.
 *
 * @since   3.14.0
 * @package Wordlift/admin
 */
?>

<div class="wrap">
	<h2><?php esc_html_e( 'WordLift Batch Analyze Monitor', 'wordlift' ) ?></h2>

	<?php
	$queue        = $this->batch_analysis_service->waiting_for_analysis();
	$submit_count = count( $queue );
	?>
	<h3><?php esc_html_e( 'Posts in queue', 'wordlift' ) ?><?php echo " ($submit_count)"; ?></h3>
	<?php
	if ( empty( $queue ) ) {
		esc_html_e( 'Nothing is currently in queue', 'wordlift' );
	} else {
		echo '<ul>';
		foreach ( $queue as $pid ) {
			$cancel_link = admin_url( "admin-ajax.php?action=wl_batch_analysis_cancel&post=$pid" );
			?>
			<li><a href="<?php echo get_edit_post_link( $pid ) ?>"><?php
					echo get_the_title( $pid ) ?></a> [<a
					href="<?php echo esc_attr( $cancel_link ); ?> "><?php esc_html_e( 'Cancel', 'wordlift' ); ?></a>]
			</li>
			<?php
		}
		echo '</ul>';
	}

	$queue         = $this->batch_analysis_service->waiting_for_response();
	$request_count = count( $queue );
	?>
	<h3><?php esc_html_e( 'Posts being processed', 'wordlift' ) ?><?php echo " ($request_count)"; ?></h3>
	<?php
	if ( empty( $queue ) ) {
		esc_html_e( 'Nothing is currently being processed', 'wordlift' );
	} else {
		echo '<ul>';
		foreach ( $queue as $pid ) {
			$cancel_link = admin_url( "admin-ajax.php?action=wl_batch_analysis_cancel&post=$pid" );
			?>
			<li><a href="<?php echo get_edit_post_link( $pid ) ?>"><?php
					echo get_the_title( $pid ) ?></a> [<a
					href="<?php echo esc_attr( $cancel_link ); ?> "><?php esc_html_e( 'Cancel', 'wordlift' ); ?></a>]
			</li>
			<?php
		}
		echo '</ul>';
	}

	$queue         = $this->batch_analysis_service->get_warnings();
	$warning_count = count( $queue );
	?>

	<h3><?php esc_html_e( 'Posts with warnings', 'wordlift' ) ?><?php echo " ($warning_count)"; ?></h3>
	<?php
	if ( empty( $queue ) ) {
		esc_html_e( 'No warnings :-)', 'wordlift' );
	} else {
		echo '<ul>';
		foreach ( $queue as $pid ) {
			$cancel_link = admin_url( "admin-ajax.php?action=wl_batch_analysis_clear_warning&post=$pid" );
			?>
			<li><a href="<?php echo get_edit_post_link( $pid ) ?>"><?php
					echo get_the_title( $pid ) ?></a> [<a
					href="<?php echo esc_attr( $cancel_link ); ?> "><?php esc_html_e( 'Clear warning', 'wordlift' ); ?></a>]
			</li>
			<?php
		}
		echo '</ul>';
	}
	?>
</div>
