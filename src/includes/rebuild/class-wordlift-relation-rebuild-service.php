<?php

class Wordlift_Relation_Rebuild_Service extends Wordlift_Listable {

	/**
	 * @var Wordlift_Content_Filter_Service
	 */
	private $content_filter_service;

	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.3
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;


	/**
	 * Wordlift_Relation_Rebuild_Service constructor.
	 *
	 * @param \Wordlift_Content_Filter_Service $content_filter_service
	 * @param \Wordlift_Entity_Service         $entity_service
	 */
	public function __construct( $content_filter_service, $entity_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Relation_Rebuild_Service' );

		$this->content_filter_service = $content_filter_service;
		$this->entity_service         = $entity_service;

	}

	public function process_all() {
		global $wpdb;

		set_time_limit( 21600 ); // 6 hours

		$this->log->debug( 'Deleting all existing relations...' );

		// Delete existing data.
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wl_relation_instances" );

		$this->log->debug( 'Processing contents...' );

		$this->process( array( $this, 'process_single' ) );

	}

	public function process_single( $post_id ) {

		$this->log->debug( "Processing post $post_id..." );

		// Bail out if the post is not found.
		$post = get_post( $post_id );

		if ( null === $post ) {
			$this->log->error( "Post $post_id not found." );

			return;
		}

		// Get the URIs from the post content.
		$uris = $this->content_filter_service->get_entity_uris( $post->post_content );

		// Map the URIs to post IDs.
		$ids = $this->uri_to_post_id( $uris );

		$this->log->info( 'Found ' . count( $uris ) . ' annotation(s) and ' . count( $ids ) . ' unique relation(s).' );

		// Create the relations.
		$this->create_relations( $post_id, $ids );

	}

	private function create_relations( $subject_id, $object_ids ) {

		$this->log->info( "Creating relations for post $subject_id..." );

		$entity_service = $this->entity_service;

		array_walk( $object_ids, function ( $item ) use ( $subject_id, $entity_service ) {
			wl_core_add_relation_instance(
				$subject_id,
				$entity_service->get_classification_scope_for( $item ),
				$item
			);
		} );

	}

	private function uri_to_post_id( $uris ) {

		$entity_service = $this->entity_service;

		$ids = array_unique( array_map( function ( $item ) use ( $entity_service ) {
			$post = $entity_service->get_entity_post_by_uri( $item );

			return null === $post ? null : $post->ID;
		}, (array) $uris ) );

		return array_filter( $ids, function ( $item ) {
			return null !== $item;
		} );
	}

	/**
	 * @inheritdoc
	 */
	function find( $offset = 0, $limit = 10, $args = array() ) {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare(
			"
			SELECT id
			FROM $wpdb->posts
			WHERE post_type NOT IN ( 'attachment', 'revision' )
				AND post_content REGEXP %s
			LIMIT %d OFFSET %d
			",
			'<[a-z]+ id="urn:[^"]+" class="[^"]+" itemid="[^"]+">',
			$limit,
			$offset
		) );

	}

}
