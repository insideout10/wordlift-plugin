<?php

namespace Wordlift\Entity\Classic_Editor;

class Entity_Uri_Mapper {

	private $entities;

	public function __construct( $entities ) {
		$this->entities = $entities;
	}

	public function get_map() {

		$map = array();

		foreach ( $this->entities as $entity_uri => $entity ) {
			// Only if the current entity is created from scratch let's avoid to
			// create more than one entity with same label & entity type.
			$entity_type = ( preg_match( '/^local-entity-.+/', $entity_uri ) > 0 ) ?
				$entity['main_type'] : null;

			// Look if current entity uri matches an internal existing entity, meaning:
			// 1. when $entity_uri is an internal uri
			// 2. when $entity_uri is an external uri used as sameAs of an internal entity
			$internal_entity = \Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $entity_uri );

			// Dont save the entities which are not found, but also local.
			if ( $this->is_internal_entity( $internal_entity, $entity_uri ) ) {
				continue;
			}

			// Detect the uri depending if is an existing or a new entity
			$uri = ( null === $internal_entity ) ? $this->build_entity_url( $entity['label'], $entity_type ) : wl_get_entity_uri( $internal_entity->ID );

			$map[ $entity_uri ] = $uri;
		}

		return $map;
	}

	/**
	 * @param $label
	 * @param $entity_type
	 *
	 * @return string
	 */
	protected function build_entity_url( $label, $entity_type ) {
		return \Wordlift_Uri_Service::get_instance()->build_uri(
			$label,
			\Wordlift_Entity_Service::TYPE_NAME,
			$entity_type
		);
	}

	/**
	 * @param \WP_Post $internal_entity
	 * @param $entity_uri
	 *
	 * @return bool
	 */
	protected function is_internal_entity( $internal_entity, $entity_uri ) {
		return $internal_entity === null && \Wordlift_Entity_Uri_Service::get_instance()->is_internal( $entity_uri );
	}

}