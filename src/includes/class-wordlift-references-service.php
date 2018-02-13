<?php
/**
 * Define the {@link Wordlift_References_Service} class.
 *
 * @since   3.18.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_References_Service} class
 *
 * @since 3.18.0
 */
class Wordlift_References_Service {

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
	 * The {@link Wordlift_Post_Property_Storage_Factory} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Storage_Factory $storage_factory The {@link Wordlift_Post_Property_Storage_Factory} instance.
	 */
	private $storage_factory;

	/**
	 * The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 */
	private $rendition_factory;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Wordlift_Entity_To_Jsonld_Converter constructor.
	 *
	 * @since 3.18.0
	 *
	 * @param \Wordlift_Entity_Service                 $entity_service        The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Sparql_Service                 $sparql_service        The {@link Wordlift_Sparql_Service} instance.
	 * @param \Wordlift_Configuration_Service          $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 * @param \Wordlift_Storage_Factory                $storage_factory       The {@link Wordlift_Post_Property_Storage_Factory} instance.
	 * @param \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory     The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 */
	public function __construct( $entity_service, $sparql_service, $configuration_service, $storage_factory, $rendition_factory ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->entity_service        = $entity_service;
		$this->sparql_service        = $sparql_service;
		$this->configuration_service = $configuration_service;
		$this->storage_factory       = $storage_factory;
		$this->rendition_factory     = $rendition_factory;
	}

	/**
	 * Add custom predicates to the existing ones.
	 *
	 * @since 3.18.0
	 *
	 * @param array $predicates Array of predicates
	 *
	 * @return array $predicates Modified predicates.
	 */
	function update_predicates( $predicates ) {
		// Add headline predicate.
		$predicates[] = 'http://schema.org/headline';

		// Add references predicate.
		$predicates[] = 'http://purl.org/dc/terms/references';

		// Finally return modified predicates.
		return $predicates;
	}

	/**
	 * Add insert tuples for non-entities.
	 *
	 * @param type $properties 
	 * @param type $entity_service 
	 * @param type $post_id 
	 * @return type
	 */
	function add_non_entity_tuples( $properties, $entity_service, $post_id ) {
		// Bail if the post is entity.
		if ( $this->entity_service->is_entity( $post_id ) ) {
			return $properties;
		}

		$language_code = $this->configuration_service->get_language_code();

		$properties = array(
			// ### schema:headline.
			$this->rendition_factory->create(
				$this->storage_factory->post_title(),
				'http://schema.org/headline',
				null,
				$language_code
			),
			// ### schema:url.
			$this->rendition_factory->create(
				$this->storage_factory->url_property(),
				Wordlift_Query_Builder::SCHEMA_URL_URI,
				Wordlift_Schema_Service::DATA_TYPE_URI,
				$language_code
			),
			// ### rdf:type.
			// $this->rendition_factory->create(
			// 	$this->storage_factory->schema_class( $this ),
			// 	Wordlift_Query_Builder::RDFS_TYPE_URI,
			// 	Wordlift_Schema_Service::DATA_TYPE_URI,
			// 	$language_code
			// ),
			// ### dcterms:references.
			$this->rendition_factory->create(
				new Wordlift_Post_References_Storage( $this->entity_service ),
				Wordlift_Query_Builder::DCTERMS_REFERENCES_URI,
				Wordlift_Schema_Service::DATA_TYPE_URI,
				$language_code
			),
		);

		// Finally return the new properties.
		return $properties;
	}

	/**
	 * Delete the reference from the triple when a relation is removed/deleted.
	 *
	 * @since 3.18.0
	 *
	 * @param int $subject_id The subject {@link WP_Post} id.
	 *
	 * @return void
	 */
	public function delete_reference( $subject_id ) {
		// Retrieve the uri by post id.
		$uri = $this->get_uri_by_subject_id( $subject_id );

		// Bail if the uri is empty.
		// The post doesn't exists or the post is an entity.
		if ( empty( $uri ) ) {
			return;
		}

		// Prepare the DELETE query to delete existing data.
		$query = Wordlift_Query_Builder
				   ::new_instance()
				   ->delete()->statement( $uri, Wordlift_Query_Builder::DCTERMS_REFERENCES_URI, '?o' )
				   ->build();

		// Delete the reference.
		$this->sparql_service->execute( $query, false );
	}

	/**
	 * Add single reference to the subject on creating relation in database.
	 *
	 * @since 3.18.0
	 *
	 * @param int    $subject_id The subject {@link WP_Post} id.
	 * @param string $predicate  The predicate.
	 * @param int    $object_id  The object {@link WP_Post} id.
	 *
	 * @return void
	 */
	public function add_reference( $subject_id, $predicate, $object_id ) {

		// Retrieve the uri by post id.
		$uri = $this->get_uri_by_subject_id( $subject_id );

		// Bail if the uri is empty.
		// The post doesn't exists or the post is an entity.
		if ( empty( $uri ) ) {
			return;
		}

		// Get object uri.
		$object_uri = $this->entity_service->get_uri( $object_id );

		// Prepare the INSERT query to add the reference to the existing data.
		$query = Wordlift_Query_Builder
			::new_instance()
			->insert()->statement( $uri, Wordlift_Query_Builder::DCTERMS_REFERENCES_URI, $object_uri )
			->build();

		// Add the reference.
		$this->sparql_service->execute( $query, false );
	}

	/**
	 * Retrieve the URI from post id.
	 *
	 * @since 3.18.0
	 *
	 * @param int $subject_id The post id.
	 *
	 * @return string|bool The uri on success, false on failure.
	 */
	private function get_uri_by_subject_id( $subject_id ) {
		// Get the post.
		$post = get_post( $subject_id );

		// Bail out if the post isn't found.
		if ( null == $post ) {
			return false;
		}

		// Bail out if it's an entity: we're only interested in articles
		// *referencing* entities.
		if ( $this->entity_service->is_entity( $post->ID ) ) {
			return false;
		}

		// Get the article URI.
		$uri = $this->entity_service->get_uri( $post->ID );

		return $uri;
	}
}
