<?php

class Wordlift_Reference_Rebuild_Service extends Wordlift_Rebuild_Service {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;


	/**
	 * Wordlift_Reference_Rebuild_Service constructor.
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	public function __construct( $entity_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Reference_Rebuild_Service' );

		$this->entity_service = $entity_service;
	}

	public function rebuild() {
		set_time_limit( 21600 ); // 6 hours

		// Send textual output.
		header( 'Content-type: text/plain; charset=utf-8' );

		// We start at 0 by default and get to max.
		$offset = isset( $_GET['offset'] ) ? (int) $_GET['offset'] : 0;
		$limit  = isset( $_GET['limit'] ) ? (int) $_GET['limit'] : 1;
		$max    = $offset + $limit;

		$this->log->debug( 'Processing references...' );

		// Go through the list of published entities and posts and call the (legacy)
		// `wl_linked_data_save_post` function for each one. We're using the `process`
		// function which is provided by the parent `Wordlift_Listable` abstract class
		// and will cycle through all the posts w/ a very small memory footprint
		// in order to avoid memory errors.

		$count          = 0;
		$log            = $this->log;
		$entity_service = $this->entity_service;

		$this->process(
			function ( $post_id ) use ( &$count, $log, $entity_service ) {
				$count ++;

				if ( $entity_service->is_entity( $post_id ) ) {
					$log->trace( "Post $post_id is an entity, skipping..." );

					return;
				}

				$log->trace( "Going to save post $count, ID $post_id..." );
				do_action( 'wl_legacy_linked_data__push', $post_id );
			},
			array(),
			$offset,
			$max
		);

		// Redirect to the next chunk.
		if ( $count == $limit ) {
			$log->trace( 'Redirecting to post #' . ( $offset + 1 ) . '...' );
			$url = admin_url( 'admin-ajax.php?action=wl_rebuild_references&offset=' . ( $offset + $limit ) . '&limit=' . $limit );
			$this->redirect( $url );
		}

		$this->log->info( "Rebuild complete" );
		echo( "Rebuild complete" );

		// If we're being called as AJAX, die here.
		if ( DOING_AJAX ) {
			wp_die();
		}
	}

	/**
	 * @inheritdoc
	 */
	function find( $offset = 0, $limit = 10, $args = array() ) {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare(
			"
			SELECT DISTINCT subject_id AS id
			FROM {$wpdb->prefix}wl_relation_instances
			LIMIT %d OFFSET %d
			",
			$limit,
			$offset
		) );

	}

}
