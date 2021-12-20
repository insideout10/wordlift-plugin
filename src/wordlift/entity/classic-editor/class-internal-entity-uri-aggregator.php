<?php

namespace Wordlift\Entity\Classic_Editor;

class Internal_Entity_Uri_Aggregator {


	protected $entities;

	public function __construct( $entities ) {
		$this->entities = $entities;
	}

	public function get_internal_entity_uris() {
		$internal_entity_uris = array();

		foreach ( $this->entities as $entity_uri => $entity ) {

			$classic_editor_entity = new Classic_Editor_Entity( $entity, $entity_uri );
			if ( $classic_editor_entity->is_internal_entity() ) {
				$internal_entity_uris[] = $entity_uri;
			} else {
				$internal_entity_uris[] = $classic_editor_entity->build_entity_uri();
			}
		}

		return $internal_entity_uris;
	}


}