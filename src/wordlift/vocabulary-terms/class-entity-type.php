<?php

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Term_Checklist\Term_Checklist;
use Wordlift\Vocabulary\Terms_Compat;
use Wordlift_Entity_Type_Taxonomy_Service;

class Entity_Type {

	public function __construct() {

		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form", array( $this, 'render_ui' ), 1 );
			add_action( "created_${taxonomy}", array( $this, 'save_field' ) );
			add_action( "edited_${taxonomy}", array( $this, 'save_field' ) );
		}

	}



	/**
	 * @param $term  \WP_Term
	 */
	public function render_ui( $term ) {

		$selected_entity_types = get_term_meta( $term->term_id, 'wl_entity_types' );
		$entity_type_taxonomy  = Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME;
		$types = Terms_Compat::get_terms(
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			array(
				'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				'parent'        => 0,
				'hide_empty'    => false
			)
		);
		echo Term_Checklist::render( 'tax_input[wl_entity_type]', $types, $selected_entity_types );
	}

	public function save_field( $term_id ) {
		$entity_types = $_REQUEST['tax_input'][ Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ];
		if ( isset( $entity_types ) && is_array( $entity_types ) ) {
			// Save the taxonomies.
			delete_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			foreach ( $entity_types as $entity_type ) {
				add_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, (int) $entity_type );
			}
		}
	}

}