<?php
/**
 * Renditions: Sparql Tuple Rendition.
 *
 * Renders a property (accessed using a {@link Wordlift_Storage} instance) to
 * a tuple for use in SPARQL statements.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Sparql_Tuple_Rendition} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Default_Sparql_Tuple_Rendition implements Wordlift_Sparql_Tuple_Rendition {

	/**
	 * A {@link Wordlift_Storage} instance to read a property.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Storage $storage A {@link Wordlift_Storage} instance to
	 *                                 read a property.
	 */
	private $storage;

	/**
	 * The data type (or null if not set).
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string|null $data_type The data type (or null if not set).
	 */
	private $data_type;

	/**
	 * The language (or null if not set).
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string|null $language The language (or null if not set).
	 */
	private $language;

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The predicate URI.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string $predicate The predicate URI.
	 */
	private $predicate;

	/**
	 * The URI suffix.
	 *
	 * @since 3.18.0
	 * @access private
	 * @var null|string $uri_suffix The URI suffix or null if no suffix is required.
	 */
	private $uri_suffix;

	/**
	 * Create a {@link Wordlift_Sparql_Tuple_Rendition} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service}
	 *                                                 instance.
	 * @param \Wordlift_Storage        $storage        The {@link Wordlift_Storage}
	 *                                                 instance.
	 * @param string                   $predicate      The predicate URI.
	 * @param string|null              $data_type      The data type or null.
	 * @param string|null              $language       The language code or null.
	 * @param string|null              $uri_suffix     The URI suffix, used for example for 2nd level entities, like location.
	 */
	public function __construct( $entity_service, $storage, $predicate, $data_type = null, $language = null, $uri_suffix = null ) {

		$this->entity_service = $entity_service;
		$this->storage        = $storage;
		$this->predicate      = $predicate;
		$this->data_type      = $data_type;
		$this->language       = $language;
		$this->uri_suffix     = ( ! is_null( $uri_suffix ) ) ? $uri_suffix : '';

	}

	/**
	 * Get tuple representations for the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of tuples.
	 */
	public function get_insert_triples( $post_id ) {
		// Get the entity URI.
		$uri = $this->entity_service->get_uri( $post_id ) . $this->uri_suffix;

		// Get the predicate, data type and language.
		$predicate = $this->predicate;
		$data_type = $this->data_type;
		$language  = $this->language;

		// Filter out empty values.
		$values = array_filter( (array) $this->storage->get( $post_id ), function ( $item ) {
			return ! empty( $item );
		} );

		// Map the values to tuples.
		return array_map( function ( $item ) use ( $uri, $predicate, $data_type, $language ) {

			return sprintf( '<%s> <%s> %s . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				Wordlift_Sparql_Service::escape_uri( $predicate ),
				Wordlift_Sparql_Service::format( $item, $data_type, $language )
			);
		}, $values );
	}

	/**
	 * Get the delete statement for current post id.
	 *
	 * @since 3.18.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An array containing delete statements for both
	 *               the uri as subject and object.
	 */
	public function get_delete_triples( $post_id ) {
		// Get the entity URI.
		$uri = $this->entity_service->get_uri( $post_id ) . $this->uri_suffix;

		return array(
			// As object.
			sprintf( '<%s> <%s> ?o',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				Wordlift_Sparql_Service::escape_uri( $this->predicate )
			),
			// As subject.
			sprintf( '?s <%s> <%s>',
				Wordlift_Sparql_Service::escape_uri( $this->predicate ),
				Wordlift_Sparql_Service::escape_uri( $uri )
			),
		);
	}
}
