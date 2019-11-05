<?php
/**
 * Commands: Push Reference Data Command
 *
 * This is a sample command to push the References to the Linked Dataset.
 *
 * @since   3.18.0
 * @package Wordlift
 * @package Wordlift/cli
 */

/**
 * Define the {@link Wordlift_Push_Reference_Data_Command} class.
 *
 * @since 3.18.0
 */
class Wordlift_Push_Reference_Data_Command {

	/**
	 * The {@link Wordlift_Relation_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Relation_Service $relation_service The {@link Wordlift_Relation_Service} instance.
	 */
	private $relation_service;

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Sparql_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Sparql_Service $sparql_service The {@link Wordlift_Sparql_Service} instance.
	 */
	private $sparql_service;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;
	/**
	 * @var Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;

	/**
	 * Wordlift_Push_Reference_Data_Command constructor.
	 *
	 * @param \Wordlift_Relation_Service      $relation_service The {@link Wordlift_Relation_Service} instance.
	 * @param \Wordlift_Entity_Service        $entity_service The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Sparql_Service        $sparql_service The {@link Wordlift_Sparql_Service} instance.
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 * @param \Wordlift_Entity_Type_Service   $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since 3.18.0
	 *
	 */
	public function __construct( $relation_service, $entity_service, $sparql_service, $configuration_service, $entity_type_service ) {

		$this->relation_service      = $relation_service;
		$this->entity_service        = $entity_service;
		$this->sparql_service        = $sparql_service;
		$this->configuration_service = $configuration_service;
		$this->entity_type_service   = $entity_type_service;

	}

	public function __invoke( $args ) {

		$relations    = $this->relation_service->find_all_grouped_by_subject_id();
		$progress_bar = \WP_CLI\Utils\make_progress_bar( 'Processing...', count( $relations ) );

		foreach ( $relations as $relation ) {
			$progress_bar->tick();

			// Get the post.
			$post = get_post( $relation->subject_id );

			// Bail out if the post isn't found.
			if ( null == $post ) {
				continue;
			}

			// Bail out if it's an entity: we're only interested in articles
			// *referencing* entities.
			if ( $this->entity_service->is_entity( $post->ID ) ) {
				continue;
			}

			// Get the article URI.
			$uri = $this->entity_service->get_uri( $post->ID );

			// Prepare the DELETE query to delete existing data.
			$query = self::get_delete_query( $uri )
			         . $this->get_insert_query( $post, $uri, explode( ',', $relation->object_ids ) );

			$this->sparql_service->execute( $query, false );

		}

		$progress_bar->finish();

	}

	private static function get_delete_query( $uri ) {

		return Wordlift_Query_Builder
			       ::new_instance()
			       ->delete()->statement( $uri, Wordlift_Query_Builder::DCTERMS_REFERENCES_URI, '?o' )
			       ->build()
		       . Wordlift_Query_Builder
			       ::new_instance()
			       ->delete()->statement( $uri, Wordlift_Query_Builder::RDFS_TYPE_URI, '?o' )
			       ->build()
		       . Wordlift_Query_Builder
			       ::new_instance()
			       ->delete()->statement( $uri, Wordlift_Query_Builder::SCHEMA_HEADLINE_URI, '?o' )
			       ->build()
		       . Wordlift_Query_Builder
			       ::new_instance()
			       ->delete()->statement( $uri, Wordlift_Query_Builder::SCHEMA_URL_URI, '?o' )
			       ->build();
	}

	private function get_insert_query( $post, $uri, $object_ids ) {

		$language_code = $this->configuration_service->get_language_code();
		$type          = $this->entity_type_service->get( $post->ID );

		/*
		 * When inserting the schema:url property in the triple store, we want to use the production
		 * URL, i.e. we must take into consideration that the current URL is a staging one and that
		 * 3rd parties may want to update the URL with a production one.
		 *
		 * @since 3.20.0
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/850.
		 */
		$builder = Wordlift_Query_Builder
			::new_instance()
			->insert()
			->statement( $uri, Wordlift_Query_Builder::SCHEMA_HEADLINE_URI, $post->post_title, Wordlift_Query_Builder::OBJECT_VALUE, null, $language_code )
			->statement( $uri, Wordlift_Query_Builder::RDFS_TYPE_URI, $type['uri'] );

		$permalink = Wordlift_Post_Adapter::get_production_permalink( $post->ID );
		if ( ! empty( $permalink ) ) {
			$builder->statement( $uri, Wordlift_Query_Builder::SCHEMA_URL_URI, $permalink );
		}

		$entity_service = $this->entity_service;
		array_walk( $object_ids, function ( $item ) use ( $entity_service, $builder, $uri ) {
			$object_uri = $entity_service->get_uri( $item );
			$builder->statement( $uri, Wordlift_Query_Builder::DCTERMS_REFERENCES_URI, $object_uri );
		} );

		return $builder->build();
	}

}
