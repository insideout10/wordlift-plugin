<?php

namespace Wordlift\Entity\Classic_Editor;

class Entity_Repository {

	private $internal_entity_uris = array();

	private $entities_uri_mapping = array();
	/**
	 * @var array<array> List of entities posted from classic editor.
	 */
	private $entities;
	/**
	 * @var int
	 */
	private $post_id;

	public function __construct( $entities, $post_id ) {
		$this->entities = $entities;
		$this->post_id  = $post_id;
	}


	/**
	 * @return array
	 */
	public function get_internal_entity_uris() {
		return $this->internal_entity_uris;
	}

	/**
	 * @return array
	 */
	public function get_entities_uri_mapping() {
		return $this->entities_uri_mapping;
	}


	public function save_all() {

		foreach ( $this->entities as $entity_uri => $entity ) {

			// Only if the current entity is created from scratch let's avoid to
			// create more than one entity with same label & entity type.
			$entity_type = ( preg_match( '/^local-entity-.+/', $entity_uri ) > 0 ) ?
				$entity['main_type'] : null;

			// Look if current entity uri matches an internal existing entity, meaning:
			// 1. when $entity_uri is an internal uri
			// 2. when $entity_uri is an external uri used as sameAs of an internal entity
			$ie = \Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $entity_uri );

			// Dont save the entities which are not found, but also local.
			if ( $ie === null && \Wordlift_Entity_Uri_Service::get_instance()->is_internal( $entity_uri ) ) {
				$this->internal_entity_uris[] = $entity_uri;
				continue;
			}

			// Detect the uri depending if is an existing or a new entity
			$uri                    = ( null === $ie ) ?
				\Wordlift_Uri_Service::get_instance()->build_uri(
					$entity['label'],
					\Wordlift_Entity_Service::TYPE_NAME,
					$entity_type
				) : wl_get_entity_uri( $ie->ID );
			$this->internal_entity_uris[] = $uri;
			wl_write_log( "Map $entity_uri on $uri" );
			$this->entities_uri_mapping[ $entity_uri ] = $uri;

			// Local entities have a tmp uri with 'local-entity-' prefix
			// These uris need to be rewritten here and replaced in the content
			if ( preg_match( '/^local-entity-.+/', $entity_uri ) > 0 ) {
				// Override the entity obj
				$entity['uri'] = $uri;
			}

			// Update entity data with related post
			$entity['related_post_id'] = $this->post_id;

			// Save the entity if is a new entity
			if ( null === $ie ) {
				wl_save_entity( $entity );
			}

		}
	}


}