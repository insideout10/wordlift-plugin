<?php

namespace Wordlift\Term_Entity;

use Wordlift\Vocabulary\Terms_Compat;

class Entity_Type {

	public function __construct() {

		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form_fields", array( $this, 'render_ui' ), 1 );
		}

	}

	public function render_ui() {
		echo "<h2>Entity types</h2>";
		echo wp_terms_checklist( 0, array(
			'taxonomy' => \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME) );
	}

}