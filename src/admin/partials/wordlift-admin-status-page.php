<?php
/**
 * Pages: Admin Status Report page.
 *
 * @since   3.9.8
 * @package Wordlift/admin
 */

$local_uris = $this->get_entity_uris();

$remote_uris = $this->get_linked_data_uris();

$not_found_uris = array_diff( $local_uris, $remote_uris );

if ( 0 < count( $not_found_uris ) ) {

	$first_id = key( $not_found_uris );
	// Re-push the entity to the Linked Data Cloud.
	wl_linked_data_push_to_redlink( $first_id );

}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Status Report', 'wordlift' ); ?></h1>

	<p><?php echo esc_html( sprintf( __( '%d not found URIs; %d local entity URIs; %d remote URIs (including posts and authors).', 'wordlift' ), count( $not_found_uris ), count( $local_uris ), count( $remote_uris ) ) ); ?></p>

	<table class="wp-list-table widefat fixed striped posts">
		<thead>
		<th scope="col"><?php esc_html_e( 'URL', 'wordlift' ); ?></th>
		</thead>
		<tbody>
		<?php foreach ( $not_found_uris as $id => $uri ) { ?>
			<tr>
				<td><?php echo esc_html( $uri ); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

</div>
