<?php
/**
 * Provides helper functions for entity. In particular a function to map external URIs to local URIs.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.1
 * @package Wordlift\Entity
 */

namespace Wordlift\Entity;

use Wordlift_Entity_Service;
use Wordlift_Entity_Uri_Service;

class Entity_Helper {

	private $entity_uri_service;

	private $entity_service;

	/**
	 * Entity_Helper constructor.
	 *
	 * @param Wordlift_Entity_Uri_Service $entity_uri_service
	 * @param Wordlift_Entity_Service     $entity_service
	 */
	protected function __construct( $entity_uri_service, $entity_service ) {

		$this->entity_uri_service = $entity_uri_service;
		$this->entity_service     = $entity_service;

	}

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( Wordlift_Entity_Uri_Service::get_instance(), Wordlift_Entity_Service::get_instance() );
		}

		return self::$instance;
	}

	/**
	 * Maps the provided URIs to local URIs.
	 *
	 * The input array is filtered out of the local URIs. Then each URI is checked with itemid and sameAs in the local
	 * database. If found a mapping is added from the external URI to the local URI.
	 *
	 * An array of mappings is returned, where the key is the external URI and the value is the local URI.
	 *
	 * @param array $uris An array of URIs.
	 *
	 * @return array The mappings array.
	 */
	public function map_many_to_local( $uris ) {

		// Filter only the external URIs.
		$entity_uri_service = $this->entity_uri_service;
		$external_uris      = array_filter(
			$uris,
			function ( $item ) use ( $entity_uri_service ) {
				return ! $entity_uri_service->is_internal( $item );
			}
		);

		// Preload the URIs.
		$entity_uri_service->preload_uris( $external_uris );

		$mappings = array();
		foreach ( $external_uris as $external_uri ) {
			if ( empty( $external_uri ) ) {
				continue;
			}

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
