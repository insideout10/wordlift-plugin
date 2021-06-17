<?php

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Vocabulary\Terms_Compat;
use Wordlift_Entity_Type_Taxonomy_Service;

class Entity_Type {

	public function __construct() {

		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form_fields", array( $this, 'render_ui' ), 1 );
			add_action( "created_${taxonomy}", array( $this, 'save_field' ) );
			add_action( "edited_${taxonomy}", array( $this, 'save_field' ) );
		}

	}

	/**
	 * @param $term  \WP_Term
	 */
	public function render_ui( $term ) {

		$selected_entity_types = get_term_meta( $term->term_id, 'wl_entity_types' );

		echo sprintf( "<h2>%s</h2>", esc_html( __( 'Entity Types', 'wordlift' ) ) );
		echo "<div style='height: 300px; overflow-y: scroll;'>";
		echo wp_terms_checklist( 0, array(
			'taxonomy'      => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			'selected_cats' => array_values( $selected_entity_types ),
			'checked_ontop' => false,
			'popular_cats' => false
		) );
		echo "</div>";
	}

	public function save_field( $term_id ) {
		$entity_types = $_REQUEST['tax_input'][ Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ];
		if ( isset( $entity_types ) && is_array( $entity_types ) ) {
			// Save the taxonomies.
			delete_term_meta( $term_id, 'wl_entity_types' );
			foreach ( $entity_types as $entity_type ) {
				add_term_meta( $term_id, 'wl_entity_types', (int) $entity_type );
			}
		}
	}

}