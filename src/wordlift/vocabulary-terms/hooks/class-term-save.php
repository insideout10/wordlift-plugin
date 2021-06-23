<?php

namespace Wordlift\Vocabulary_Terms\Hooks;


/**
 * This class assigns the entity_url meta to the term when it is
 * created or edited, we need to check if it already has entity_url before
 * assigning one because we dont want to generate it again if we have it
 * assigned before.
 *
 * Class Term_Save
 * @package Wordlift\Vocabulary_Terms\Hooks
 */
class Term_Save {

	public function init() {
		add_action( 'create_term', array( $this, 'saved_term' ) );
		add_action( 'edited_term', array( $this, 'saved_term' ) );
	}

	public function saved_term( $term_id ) {

		// check if entity url already exists.

		$entity_url = get_term_meta( $term_id, WL_ENTITY_URL_META_NAME, true);

		if (  ! $entity_url ) {
			// we need to build an entity uri for the term.
			wl_set_term_entity_uri( $term_id, wl_build_term_uri( $term_id ) );
		}
	}


}