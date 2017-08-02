<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:17
 */

class Wordlift_Sparql_Tuple_Rendition {
	private $storage;
	private $predicate;
	/**
	 * @var null
	 */
	private $data_type;
	/**
	 * @var null
	 */
	private $language;
	private $entity_service;


	/**
	 * Wordlift_Sparql_Tuple_Rendition constructor.
	 *
	 * @param \Wordlift_Entity_Service $entity_service
	 * @param \Wordlift_Storage        $storage
	 * @param                          $predicate
	 * @param null                     $data_type
	 * @param null                     $language
	 */
	public function __construct( $entity_service, $storage, $predicate, $data_type = null, $language = null ) {

		$this->entity_service = $entity_service;
		$this->storage        = $storage;
		$this->predicate      = $predicate;
		$this->data_type      = $data_type;
		$this->language       = $language;

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
	public function get( $post_id ) {

		// Get the entity URI.
		$uri = $this->entity_service->get_uri( $post_id );

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

			return sprintf( '<%s> <%s> %s',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				Wordlift_Sparql_Service::escape_uri( $predicate ),
				Wordlift_Sparql_Service::format( $item, $data_type, $language )
			);
		}, $values );
	}

}
