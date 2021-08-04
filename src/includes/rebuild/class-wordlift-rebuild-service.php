<?php
/**
 * Services: Rebuild Service.
 *
 * The Wordlift_Rebuild_Service allows to rebuild the Linked Data dataset from
 * scratch by clearing out data on the remote dataset, parsing all data in WordPress
 * and resending data to the remote dataset.
 *
 * @since      3.6.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Rebuild_Service} class.
 *
 * @since      3.6.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
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
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service A {@link Wordlift_Sparql_Service} instance.
	 */
	private $sparql_service;

	/**
	 * The {@link Wordlift_Uri_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Uri_Service $uri_service The {@link Wordlift_Uri_Service} instance.
	 */
	private $uri_service;

	/**
	 * Create an instance of Wordlift_Rebuild_Service.
	 *
	 * @param \Wordlift_Sparql_Service $sparql_service A {@link Wordlift_Sparql_Service} instance used to query the remote dataset.
	 * @param \Wordlift_Uri_Service $uri_service
	 *
	 * @since 3.6.0
	 *
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
		$offset = (int) $_GET['offset'] ?: 0;
		$limit  = (int) $_GET['limit'] ?: 1;
		$max    = $offset + $limit;

		// Whether we should run queries asynchronously, this is handled in `wordlift_constants.php`.
		$asynchronous = isset( $_GET['wl-async'] ) && 'true' === $_GET['wl-async'];

		// If we're starting at offset 0, then delete existing URIs and data from
		// the remote dataset.
		if ( 0 === $offset ) {

			// Clear out all generated URIs, since the dataset URI might have changed
			// in the process.
			$this->uri_service->delete_all();

			// Delete all the triples in the remote dataset.
			$this->sparql_service->execute( 'DELETE { ?s ?p ?o } WHERE { ?s ?p ?o };' );

		}

		// Go through the list of published entities and posts and call the (legacy)
		// `wl_linked_data_save_post` function for each one. We're using the `process`
		// function which is provided by the parent `Wordlift_Listable` abstract class
		// and will cycle through all the posts w/ a very small memory footprint
		// in order to avoid memory errors.

		$count = 0;
		$log   = $this->log;
		$this->process( function ( $post ) use ( &$count, $log ) {
			$count ++;
			$log->trace( "Going to save post $count, ID $post->ID..." );
			if ( function_exists( 'wl_linked_data_save_post' ) ) {
				wl_linked_data_save_post( $post->ID );
			}
		}, array(
			'post_status' => 'publish',
		), $offset, $max );

		// Redirect to the next chunk.
		if ( $count == $limit ) {
			$log->trace( 'Redirecting to post #' . ( $offset + 1 ) . '...' );
			$this->redirect( admin_url( 'admin-ajax.php?action=wl_rebuild&offset=' . ( $offset + $limit ) . '&limit=' . $limit . '&wl-async=' . ( $asynchronous ? 'true' : 'false' ) ) );
		}

		// Flush the cache.
		Wordlift_File_Cache_Service::flush_all();

		// Rebuild also the references.
		$this->redirect( admin_url( 'admin-ajax.php?action=wl_rebuild_references' ) );
	}

	/**
	 * Redirect using a client-side meta to avoid browsers' redirect restrictions.
	 *
	 * @param string $url The URL to redirect to.
	 *
	 * @since 3.9.8
	 *
	 */
	public function redirect( $url ) {

		ob_clean();

		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		?>
        <html>
        <head>
            <meta http-equiv="refresh"
                  content="0; <?php echo esc_attr( $url ); ?>">
        </head>
        <body>
        Rebuilding, please wait...
        </body>
        </html>
		<?php

		exit;

	}

	/**
	 * List the items starting at the specified offset and up to the specified limit.
	 *
	 * @param int $offset The start offset.
	 * @param int $limit The maximum number of items to return.
	 * @param array $args Additional arguments.
	 *
	 * @return array A array of items (or an empty array if no items are found).
	 * @since 3.19.5 remove Polylang hooks.
	 * @since 3.6.0
	 *
	 */
	function find( $offset = 0, $limit = 10, $args = array() ) {

		$actual_args = wp_parse_args( $args, Wordlift_Entity_Service::add_criterias( array(
			'offset'        => $offset,
			'numberposts'   => $limit,
			'fields'        => 'all',
			'orderby'       => 'ID',
			'order'         => 'ASC',
			'post_status'   => 'any',
			'cache_results' => false,
		) ) );

		$this->log->trace( 'Using ' . var_export( $actual_args, true ) );

		return get_posts( $actual_args );
	}

}
