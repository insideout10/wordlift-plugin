<?php

namespace Wordlift\Entity\Classic_Editor;

class Entity_Repository {


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


	public function save_all() {

		foreach ( $this->entities as $entity_uri => $entity ) {

			$classic_editor_entity = new Classic_Editor_Entity( $entity, $entity_uri );

			// Dont save the entities which are not found, but also local.
			if ( $classic_editor_entity->is_internal_entity() ) {
				continue;
			}

			// Local entities have a tmp uri with 'local-entity-' prefix
			// These uris need to be rewritten here and replaced in the content
			if ( preg_match( '/^local-entity-.+/', $entity_uri ) > 0 ) {
				// Override the entity obj
				$entity['uri'] = $classic_editor_entity->build_entity_uri();
			}

			// Update entity data with related post
			$entity['related_post_id'] = $this->post_id;

			wl_save_entity( $entity );
		}
	}


}