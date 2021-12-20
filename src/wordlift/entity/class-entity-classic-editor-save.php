<?php

namespace Wordlift\Entity;

/**
 * This class handles the entity save via classic editor.
 * @since 3.33.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_Classic_Editor_Save {


	public function init() {
		add_action( '_wl_classic_editor_save_entity', array( $this, 'save_entity' ) );
	}

	public function save_entity( $entity ) {

		wl_save_entity( $entity );

	}


}