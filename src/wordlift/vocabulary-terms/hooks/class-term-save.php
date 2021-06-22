<?php

namespace Wordlift\Vocabulary_Terms\Hooks;

class Term_Save {

	public function init() {
		add_action( 'create_term', array( $this, 'saved_term' ) );
		//add_action( 'edit_term', array( $this, 'saved_term' ) );
	}

	public function saved_term( $term_id ) {
		// we need to build an entity uri for the term.
		wl_set_term_entity_uri( $term_id, wl_build_term_uri($term_id) );
	}


}