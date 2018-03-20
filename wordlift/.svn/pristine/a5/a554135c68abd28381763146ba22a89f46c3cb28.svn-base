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
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * A {@link Wordlift_Sparql_Service} instance.
	 * @since  3.6.0
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
	 * @param \Wordlift_Uri_Service    $uri_service
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

		ob_clean();

		// Give ourselves some time to process the data.
		set_time_limit( 21600 ); // 6 hours

		// Send textual output.
		header( 'Content-type: text/plain; charset=utf-8' );

		// We start at 0 by default and get to max.
		$offset      = $_GET['offset'] ?: 0;
		$limit       = $_GET['limit'] ?: 1;
		$entity_only = isset( $_GET['entity_only'] ) && '1' === $_GET['entity_only'];
		$max         = $offset + $limit;

		// If we're starting at offset 0, then delete existing URIs and data from
		// the remote dataset.
		if ( 0 === $offset ) {

			// Clear out all generated URIs, since the dataset URI might have changed
			// in the process.
			$this->uri_service->delete_all();

			// Delete all the triples in the remote dataset.
			$this->sparql_service->queue( 'DELETE { ?s ?p ?o } WHERE { ?s ?p ?o };' );

		}

		// Go through the list of published entities and posts and call the (legacy)
		// `wl_linked_data_save_post` function for each one. We're using the `process`
		// function which is provided by the parent `Wordlift_Listable` abstract class
		// and will cycle through all the posts w/ a very small memory footprint
		// in order to avoid memory errors.

		$count = 0;
		$this->process( function ( $post ) use ( &$count ) {
			$count ++;
			wl_linked_data_save_post( $post->ID );
		}, array(
			'post_status' => 'publish',
			'post_type'   => $entity_only ? 'entity' : array(
				'entity',
				'post',
			),
		), $offset, $max );

		// Redirect to the next chunk.
		if ( $count == $limit ) {
			$this->redirect( admin_url( 'admin-ajax.php?action=wl_rebuild&offset=' . ( $offset + $limit ) . '&limit=' . $limit . '&entity_only=' . ( $entity_only ? '1' : '0' ) ) );
		}

		echo( "done [ count :: $count ][ limit :: $limit ]" );

		// If we're being called as AJAX, die here.
		if ( DOING_AJAX ) {
			wp_die();
		}

	}

	/**
	 * Redirect using a client-side meta to avoid browsers' redirect restrictions.
	 *
	 * @since 3.9.8
	 *
	 * @param string $url The URL to redirect to.
	 */
	private function redirect( $url ) {

		ob_clean();

		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		?>
		<html>
		<head>
			<meta http-equiv="refresh"
			      content="1; <?php echo esc_attr( $url ); ?>">
		</head>
		</html>
		<?php

		exit;

	}

	/**
	 * List the items starting at the specified offset and up to the specified limit.
	 *
	 * @since 3.6.0
	 *
	 * @param int   $offset The start offset.
	 * @param int   $limit  The maximum number of items to return.
	 * @param array $args   Additional arguments.
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
			'post_type'   => 'post',
		) ) );
	}

}
