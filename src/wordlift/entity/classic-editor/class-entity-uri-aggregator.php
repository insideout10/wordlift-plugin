<?php

namespace Wordlift\Entity\Classic_Editor;

class Entity_Uri_Aggregator {

	private $entities;

	public function __construct( $entities ) {
		$this->entities = $entities;
	}

	public function get_map() {

		$map = array();

		foreach ( $this->entities as $entity_uri => $entity ) {
			$classic_editor_entity = new Classic_Editor_Entity( $entity, $entity_uri );
			if ( $classic_editor_entity->is_internal_entity() ) {
				continue;
			}

			// Detect the uri depending if is an existing or a new entity
			$uri = $classic_editor_entity->build_entity_uri();

			$map[ $entity_uri ] = $uri;
		}

		return $map;
	}

}