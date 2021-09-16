<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class handles renders and saves the Entity_Type field.
 */


namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Term_Checklist\Term_Checklist;
use Wordlift\Vocabulary\Terms_Compat;
use Wordlift_Entity_Type_Taxonomy_Service;

class Entity_Type {

	public function __construct() {

		$that = $this;

		add_action(
			'init',
			function () use ( $that ) {
				$that->init_ui_and_save_handlers();
			}
		);
	}


	/**
	 * @param $term  \WP_Term
	 */
	public function render_ui( $term ) {

		$entity_types_text     = __( 'Entity Types', 'wordlift' );
		$selected_entity_types = get_term_meta( $term->term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		/**
		 * Thing should be the default selected entity type
		 * when this feature is activated.
		 */
		if ( count( $selected_entity_types ) === 0 ) {
			$selected_entity_types[] = 'thing';
		}

		$entity_type_taxonomy = Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME;
		$types                = Terms_Compat::get_terms(
			$entity_type_taxonomy,
			array(
				'taxonomy'   => $entity_type_taxonomy,
				'parent'     => 0,
				'hide_empty' => false,
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
		echo sprintf( $template, esc_html( $entity_types_text ), $terms_html );
	}

	public function save_field( $term_id ) {
		if ( ! isset( $_REQUEST['tax_input'] ) ) {
			return;
		}
		$entity_types = isset( $_REQUEST['tax_input'][ Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ] )
			? (array) $_REQUEST['tax_input'][ Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ] : array();
		if ( isset( $entity_types ) && is_array( $entity_types ) ) {
			// Save the taxonomies.
			delete_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			foreach ( $entity_types as $entity_type ) {
				add_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, (string) $entity_type );
			}
		}
	}

	public function init_ui_and_save_handlers() {
		$taxonomies = Terms_Compat::get_public_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "${taxonomy}_edit_form_fields", array( $this, 'render_ui' ), 1 );
			add_action( "edited_${taxonomy}", array( $this, 'save_field' ) );
		}
	}

}
