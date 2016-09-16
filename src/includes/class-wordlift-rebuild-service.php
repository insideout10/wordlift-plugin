<?php

/**
 * Define the Wordlift_Rebuild_Service class.
 */

/**
 * The Wordlift_Rebuild_Service allows to rebuild the Linked Data dataset from
 * scratch by clearing out data on the remote dataset, parsing all data in WordPress
 * and resending data to the remote dataset.
 *
 * @since 3.6.0
 */
class Wordlift_Rebuild_Service extends Wordlift_Listable {

	private $log;

	/**
	 * A {@link Wordlift_Sparql_Service} instance.
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service A {@link Wordlift_Sparql_Service} instance.
	 */
	private $sparql_service;

	/**
	 * The global WordPress database connection.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \wpdb $wpdb The global WordPress database connection.
	 */
	private $wpdb;

	/**
	 * Wordlift_Rebuild_Service constructor.
	 *
	 * @since 3.6.0
	 *
	 * @param \Wordlift_Sparql_Service $sparql_service
	 * @param wpdb $wpdb The global WordPress database connection.
	 */
	public function __construct( $sparql_service, $wpdb ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Rebuild_Service' );

		$this->sparql_service = $sparql_service;
		$this->wpdb           = $wpdb;

	}

	/**
	 * Rebuild the Linked Data remote dataset by clearing it out and repopulating
	 * it with local data.
	 *
	 * @since 3.6.0
	 */
	public function rebuild() {

		$this->sparql_service->queue( 'DELETE { ?s ?p ?o } WHERE { ?s ?p ?o };' );

		// Clear out all local entity URIs.
		$this->delete_entity_uris();

		// Parse entities.
		$this->process( function ( $post ) {
			wl_linked_data_save_post( $post->ID );
		}, array(
			'post_status' => 'publish',
			'post_type'   => array( 'entity', 'post' )
		) );

	}

	private function delete_entity_uris() {

		$count = $this->wpdb->delete( $this->wpdb->postmeta, array( 'meta_key' => 'entity_url' ) );

		$this->log->info( "$count entity URI(s) deleted." );

		$count = $this->wpdb->delete( $this->wpdb->usermeta, array( 'meta_key' => '_wl_uri' ) );

	}

	/**
	 * List the items starting at the specified offset and up to the specified limit.
	 *
	 * @param int $offset The start offset.
	 * @param int $limit The maximum number of items to return.
	 * @param array $args Additional arguments.
	 *
	 * @return array A array of items (or an empty array if no items are found).
	 */
	function find( $offset = 0, $limit = 10, $args = array() ) {

		return get_posts( wp_parse_args( $args, array(
			'offset'      => $offset,
			'numberposts' => $limit,
			'fields'      => 'all',
			'orderby'     => 'ID',
			'order'       => 'ASC',
			'post_status' => 'any',
			'post_type'   => 'post'
		) ) );
	}
}