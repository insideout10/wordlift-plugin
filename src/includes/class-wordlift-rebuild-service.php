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

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * A {@link Wordlift_Sparql_Service} instance.
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service A {@link Wordlift_Sparql_Service} instance.
	 */
	private $sparql_service;

	/**
	 * @var \Wordlift_Uri_Service
	 */
	private $uri_service;

	/**
	 * Create an instance of Wordlift_Rebuild_Service.
	 *
	 * @since 3.6.0
	 *
	 * @param \Wordlift_Sparql_Service $sparql_service A {@link Wordlift_Sparql_Service} instance used to query the remote dataset.
	 * @param \Wordlift_Uri_Service $uri_service
	 */
	public function __construct( $sparql_service, $uri_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Rebuild_Service' );

		$this->sparql_service = $sparql_service;
		$this->uri_service    = $uri_service;
	}

	/**
	 * Rebuild the Linked Data remote dataset by clearing it out and repopulating
	 * it with local data.
	 *
	 * @since 3.6.0
	 */
	public function rebuild() {

		// Clear out all generated URIs, since the dataset URI might have changed
		// in the process.
		$this->uri_service->delete_all();

		// Delete all the triples in the remote dataset.
		$this->sparql_service->queue( 'DELETE { ?s ?p ?o } WHERE { ?s ?p ?o };' );

		// Go through the list of published entities and posts and call the (legacy)
		// `wl_linked_data_save_post` function for each one. We're using the `process`
		// function which is provided by the parent `Wordlift_Listable` abstract class
		// and will cycle through all the posts w/ a very small memory footprint
		// in order to avoid memory errors.
		$this->process( function ( $post ) {
			wl_linked_data_save_post( $post->ID );
		}, array(
			'post_status' => 'publish',
			'post_type'   => array( 'entity', 'post' )
		) );

	}

	/**
	 * List the items starting at the specified offset and up to the specified limit.
	 *
	 * @since 3.6.0
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
