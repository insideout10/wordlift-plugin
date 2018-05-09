<?php
/**
 * Renditions: Sparql Tuple Rendition.
 *
 * Renders a property (accessed using a {@link Wordlift_Storage} instance) to
 * a tuple for use in SPARQL statements.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Sparql_Tuple_Rendition} class.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Aggregate_Rating_Sparql_Tuple_Rendition implements Wordlift_Sparql_Tuple_Rendition {
	/**
	 * The AggregateRating entity renditions.
	 *
	 * @since  3.19.0
	 * @access private
	 * @var array $renditions The AggregateRating entity renditions.
	 */
	private $renditions;

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.19.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * Create a {@link Wordlift_Aggregate_Rating_Sparql_Tuple_Rendition} instance.
	 *
	 * @since 3.19.0
	 *
	 * @param \Wordlift_Entity_Service                 $entity_service    The {@link Wordlift_Entity_Service}.
	 *
	 * @param \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory}.
	 *                                                                    instance.
	 * @param \Wordlift_Storage                        $storage           The {@link Wordlift_Storage}
	 *                                                                    instance.
	 * @param string|null                              $language          The language code or null.
	 */
	public function __construct( $entity_service, $rendition_factory, $storage, $language ) {

		$this->entity_service = $entity_service;

		$this->renditions = array(
			// ### schema:reviewCount.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_RECIPE_REVIEW_COUNT ),
				'http://schema.org/reviewCount',
				null,
				$language,
				'/aggregaterating'
			),
			// ### schema:ratingValue.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_RECIPE_RATING_VALUE ),
				'http://schema.org/ratingValue',
				null,
				null,
				'/aggregaterating'
			),
		);

	}
	
	/**
	 * Get tuple representations for the specified {@link WP_Post}.
	 *
	 * @since 3.19.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of triples.
	 */
	public function get_insert_triples( $post_id ) {
		$triples = array();

		/** @var Wordlift_Sparql_Tuple_Rendition $rendition */
		foreach ( $this->renditions as $rendition ) {
			$triples = array_merge( $triples, $rendition->get_insert_triples( $post_id ) );
		}

		// Add a reference to the main entity if the triples are not empty.
		if ( ! empty( $triples ) ) {
			// Get the main entity uri.
			$uri = $this->entity_service->get_uri( $post_id );

			// Push the reference.
			$triples[] = sprintf( '<%1$s> <%2$s> <%1$s/aggregaterating> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/AggregateRating'
			);

			$triples[] = sprintf( '<%s/aggregaterating> a <%s> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/AggregateRating'
			);
		};

		// Finally return the triples.
		return $triples;
	}

	/**
	 * Get the delete statement for current post id.
	 *
	 * @since 3.19.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An array of delete triples for current tuple renditions.
	 */
	public function get_delete_triples( $post_id ) {
		$deletes = array();

		// Loop through all renditions and generate the delete statements.
		foreach ( $this->renditions as $rendition ) {
			// Generate delete triples for each rendition.
			$deletes = array_merge(
				$deletes,
				// Get the triple from current rendition.
				$rendition->get_delete_triples( $post_id )
			);
		}

		// Return the delete statements along with delete statements for
		// aggregaterating reference and AggregateRating rdf:type.
		return array_merge( $deletes, $this->get_aggregate_rating_delete_triples( $post_id ) );
	}

	/**
	 * Provide delete triples for aggregaterating reference
	 * and AggregateRating rdf:type.
	 *
	 * @since 3.19.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An arary of additional delete triples.
	 */
	private function get_aggregate_rating_delete_triples( $post_id ) {
		// Get the main entity uri.
		$uri = $this->entity_service->get_uri( $post_id );

		// Build and return the aggregaterating delete triples.
		return array(
			// Push the aggregaterating reference.
			sprintf( '<%1$s> <%2$s> <%1$s/aggregaterating> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/AggregateRating'
			),

			// Push the delete AggregateRating rdf:type.
			sprintf( '<%s/aggregaterating> a <%s> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/AggregateRating'
			),
		);

	}
}
