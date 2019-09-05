<?php


namespace Wordlift\Analysis\Response;


class Analysis_Response_Ops {

	/**
	 * The analysis response json.
	 *
	 * @since 3.21.5
	 * @access private
	 * @var mixed $json Holds the analysis response json.
	 */
	private $json;

	/**
	 * Holds the {@link Wordlift_Entity_Uri_Service}.
	 *
	 * @since 3.21.5
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	private $entity_service;

	/**
	 * Analysis_Response_Ops constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service}.
	 * @param \Wordlift_Entity_Service     $entity_service The {@link Wordlift_Entity_Service}.
	 * @param mixed                        $json The analysis response json.
	 *
	 * @since 3.21.5
	 */
	public function __construct( $entity_uri_service, $entity_service, $json ) {

		$this->json               = $json;
		$this->entity_uri_service = $entity_uri_service;
		$this->entity_service     = $entity_service;

	}

	public function make_entities_local() {

		if ( ! isset( $this->json->entities ) ) {
			return $this;
		}

		// Get the URIs.
		$uris = array_keys( get_object_vars( $this->json->entities ) );

		// Filter only the external URIs.
		$entity_uri_service = $this->entity_uri_service;
		$external_uris      = array_filter( $uris, function ( $item ) use ( $entity_uri_service ) {
			return ! $entity_uri_service->is_internal( $item );
		} );

		// Preload the URIs.
		$entity_uri_service->preload_uris( $external_uris );

		$mappings = array();
		foreach ( $external_uris as $external_uri ) {
			$entity = $entity_uri_service->get_entity( $external_uri );
			if ( null !== $entity ) {

				// Get the internal URI.
				$internal_uri              = $this->entity_service->get_uri( $entity->ID );
				$mappings[ $external_uri ] = $internal_uri;
			}
		}

		foreach ( $mappings as $external_uri => $internal_uri ) {

			// Move the data from the external URI to the internal URI.
			if ( ! isset( $this->json->entities->{$internal_uri} ) ) {
				$this->json->entities->{$internal_uri} = $this->json->entities->{$external_uri};
			}

			// Ensure sameAs is an array.
			if ( ! isset( $this->json->entities->{$internal_uri}->sameAs )
			     || ! is_array( $this->json->entities->{$internal_uri}->sameAs ) ) {
				$this->json->entities->{$internal_uri}->sameAs = array();
			}

			// Add the external URI as sameAs.
			$this->json->entities->{$internal_uri}->sameAs[] = $external_uri;

			// Finally remove the external URI.
			unset( $this->json->entities->{$external_uri} );
		}

		if ( isset( $this->json->annotations ) ) {
			foreach ( $this->json->annotations as $key => $annotation ) {
				if ( isset( $annotation->entityMatches ) ) {
					foreach ( $annotation->entityMatches as $match ) {
						if ( isset( $match->entityId ) && isset( $mappings[ $match->entityId ] ) ) {
							$match->entityId = $mappings[ $match->entityId ];
						}
					}
				}
			}
		}

		return $this;
	}

	public function to_string() {

		return wp_json_encode( $this->json, JSON_UNESCAPED_UNICODE );

	}

	public static function create( $json ) {

		return new static(
			\Wordlift_Entity_Uri_Service::get_instance(),
			\Wordlift_Entity_Service::get_instance(),
			$json );
	}

	public static function create_with_response( $response ) {

		if ( ! isset( $response['body'] ) ) {
			throw new \Exception( "`body` is required in response." );
		}

		return static::create( json_decode( $response['body'] ) );
	}

}
