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

		echo sprintf( "<h2>%s</h2>", esc_html( __( 'Entity Types', 'wordlift' ) ) );
		echo "<div style='height: 300px; overflow-y: scroll;'>";
		echo wp_terms_checklist( 0, array(
			'taxonomy'     => \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME
		) );
		echo "</div>";
	}

}