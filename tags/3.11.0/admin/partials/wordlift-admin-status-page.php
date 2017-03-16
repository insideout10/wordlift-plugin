<?php
/**
 * Pages: Admin Status Report page.
 *
 * @since   3.9.8
 * @package Wordlift/admin
 */

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Status Report', 'wordlift' ); ?></h1>

	<p><?php echo sprintf( esc_html__( '%d branch(es) and %d revision(s) deleted.', 'wordlift' ), $branches, $revisions ); ?></p>

</div>