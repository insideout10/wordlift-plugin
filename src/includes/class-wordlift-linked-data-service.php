<?php
/**
 * Services: Linked Data Service.
 *
 * A service to handle data to be pushed to the remote Linked Data store.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Linked_Data_Service} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Linked_Data_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * The {@link Wordlift_Schema_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	private $schema_service;

	/**
	 * The {@link Wordlift_Linked_Data_Service} singleton instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Linked_Data_Service $instance The {@link Wordlift_Linked_Data_Service} singleton instance.
	 */
	private static $instance;

	/**
	 * The {@link Wordlift_Sparql_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service The {@link Wordlift_Sparql_Service} instance.
	 */
	private $sparql_service;

	/**
	 * Create a {@link Wordlift_Linked_Data_Service} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Entity_Service      $entity_service      The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 * @param \Wordlift_Schema_Service      $schema_service      The {@link Wordlift_Schema_Service} instance.
	 * @param \Wordlift_Sparql_Service      $sparql_service      The {@link Wordlift_Sparql_Service} instance.
	 */
	public function __construct( $entity_service, $entity_type_service, $schema_service, $sparql_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Linked_Data_Service' );

		$this->entity_service      = $entity_service;
		$this->entity_type_service = $entity_type_service;
		$this->schema_service      = $schema_service;
		$this->sparql_service      = $sparql_service;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of {@link Wordlift_Linked_Data_Service}.
	 *
	 * @since 3.15.0
	 *
	 * @return Wordlift_Linked_Data_Service The singleton instance of <a href='psi_element://Wordlift_Linked_Data_Service'>Wordlift_Linked_Data_Service</a>.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Push a {@link WP_Post} to the Linked Data store.
	 *
	 * If the {@link WP_Post} is an entity and it's not of the `Article` type,
	 * then it is pushed to the remote Linked Data store.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 */
	public function push( $post_id ) {

		$this->log->debug( "Pushing post $post_id..." );

		// Bail out if it's not an entity: we do NOT publish non entities or
		// entities of type `Article`s.
		if ( ! $this->entity_service->is_entity( $post_id ) ) {
			$this->log->debug( "Post $post_id is not an entity." );

			return;
		}

		// Get the post and push it to the Linked Data store.
		$this->do_push( $post_id );

		// Reindex the triple store if buffering is turned off.
		if ( false === WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING ) {
			wordlift_reindex_triple_store();
		}

	}

	/**
	 * Remove the specified {@link WP_Post} from the Linked Data.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 */
	public function remove( $post_id ) {

		// Get the delete statements.
		$deletes      = $this->get_delete_statements( $post_id );
		$delete_query = implode( "\n", $deletes );
		$this->sparql_service->execute( $delete_query );

	}

	/**
	 * Push an entity to the Linked Data store.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 */
	private function do_push( $post_id ) {
		$this->log->debug( "Doing post $post_id push..." );

		// Get the post.
		$post = get_post( $post_id );

		// Bail out if the post isn't found.
		if ( null === $post ) {
			$this->log->warn( "Post $post_id not found." );

			return;
		}

		// Bail out if the post isn't published.
		if ( 'publish' !== $post->post_status ) {
			$this->log->info( "Post $post_id not published." );

			return;
		}

		// Bail out if the URI isn't valid.
		if ( ! $this->has_valid_uri( $post_id ) ) {
			$this->log->warn( "Post $post_id URI invalid." );

			return;
		}

		// First remove the post data.
		$this->remove( $post_id );

		// Get the insert statements.
		$insert_tuples     = $this->get_insert_tuples( $post_id );
		$insert_query_body = implode( "\n", $insert_tuples );
		$insert_query      = "INSERT DATA { $insert_query_body };";
		$this->sparql_service->execute( $insert_query );

	}

	/**
	 * Check if an entity's {@link WP_Post} has a valid URI.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The entity's {@link WP_Post}'s id.
	 *
	 * @return bool True if the URI is valid otherwise false.
	 */
	private function has_valid_uri( $post_id ) {

		// Get the entity's URI.
		$uri = $this->entity_service->get_uri( $post_id );

		// If the URI isn't found, return false.
		if ( null === $uri ) {
			return false;
		}

		// If the URI ends with a trailing slash, return false.
		if ( '/' === substr( $uri, - 1 ) ) {
			return false;
		}

		// URI is valid.
		return true;
	}

	/**
	 * Get the delete statements.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of delete statements.
	 */
	private function get_delete_statements( $post_id ) {

		// Get the entity URI.
		$uri = $this->entity_service->get_uri( $post_id );

		// Prepare the delete statements with the entity as subject.
		$as_subject = array_map( function ( $item ) use ( $uri ) {
			return Wordlift_Query_Builder
				::new_instance()
				->delete()
				->statement( $uri, $item, '?o' )
				->build();
		}, $this->schema_service->get_all_predicates() );

		// Prepare the delete statements with the entity as object.
		$as_object = array_map( function ( $item ) use ( $uri ) {
			return Wordlift_Query_Builder
				::new_instance()
				->delete()
				->statement( '?s', $item, $uri, Wordlift_Query_Builder::OBJECT_URI )
				->build();
		}, $this->schema_service->get_all_predicates() );

		// Merge the delete statements and return them.
		return array_merge( $as_subject, $as_object );
	}

	/**
	 * Get the SPARQL insert tuples ( ?s ?p ?o ) for the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of insert tuples.
	 */
	private function get_insert_tuples( $post_id ) {

		// Get the entity type.
		$type = $this->entity_type_service->get( $post_id );

		/**
		 * Filter: 'wl_tuple_properties' - Allow third parties to hook and add additional tuples.
		 *
		 * @since 3.17.0
		 * @api   arr $type['linked_data'] A {@link Wordlift_Sparql_Tuple_Rendition} instances.
		 * @api   \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
		 */
		$properties = apply_filters( 'wl_tuple_properties', $type['linked_data'], $this->entity_service );

		// Accumulate the tuples.
		$tuples = array();
		/** @var Wordlift_Sparql_Tuple_Rendition $property A {@link Wordlift_Sparql_Tuple_Rendition} instance. */
		foreach ( $properties as $property ) {
			foreach ( $property->get( $post_id ) as $tuple ) {
				$tuples[] = $tuple;
			}
		}

		// Finally return the tuples.
		return $tuples;
	}

}
