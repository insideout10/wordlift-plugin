<?php

namespace Wordlift\Entity;

class Entity_Helper {

	private $entity_uri_service;
	private $entity_service;

	/**
	 * Entity_Helper constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service
	 * @param \Wordlift_Entity_Service $entity_service
	 */
	public function __construct( $entity_uri_service, $entity_service ) {

		$this->entity_uri_service = $entity_uri_service;
		$this->entity_service     = $entity_service;

	}

	public function map_many_to_local( $uris ) {

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

		return $mappings;
	}

}