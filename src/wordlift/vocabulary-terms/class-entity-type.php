<?php

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Term_Checklist\Term_Checklist;
use Wordlift\Vocabulary\Terms_Compat;
use Wordlift_Entity_Type_Taxonomy_Service;

class Entity_Type {

	public function __construct() {

		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form_fields", array( $this, 'render_ui' ), 1 );
			add_action( "edited_${taxonomy}", array( $this, 'save_field' ) );
		}

	}


	/**
	 * @param $term  \WP_Term
	 */
	public function render_ui( $term ) {


		$entity_types_text     = __( 'Entity Types', 'wordlift' );
		$selected_entity_types = array_map( 'intval', get_term_meta( $term->term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) );
		$entity_type_taxonomy  = Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME;
		$types                 = Terms_Compat::get_terms(
			$entity_type_taxonomy,
			array(
				'taxonomy'   => $entity_type_taxonomy,
				'parent'     => 0,
				'hide_empty' => false
			)
		);

		$terms_html = Term_Checklist::render( 'tax_input[wl_entity_type]', $types, $selected_entity_types );


		$template = <<<EOF
        <tr class="form-field term-name-wrap">
            <th scope="row"><label for="wl-entity-type__checklist">%s</label></th>
            <td>
                <div id="wl-entity-type__checklist">%s</div>
            </td>
        </tr>
EOF;


		echo sprintf( $template, $entity_types_text, $terms_html );
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