<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class handles renders and saves the Entity_Type field.
 */

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Term_Checklist\Term_Checklist;
use Wordlift\Scripts\Scripts_Helper;
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
		?>
		<?php wp_nonce_field( 'wordlift_vocabulary_terms_entity_type', 'wordlift_vocabulary_terms_entity_type_nonce' ); ?>
		<tr class="form-field term-name-wrap">
			<th scope="row"><label for="wl-entity-type__checklist"><?php esc_html_e( 'Entity Types', 'wordlift' ); ?></label></th>
			<td>
				<div id="wl-entity-type__checklist">
					<?php
					echo wp_kses(
						Term_Checklist::render( 'tax_input[wl_entity_type]', $types, $selected_entity_types ),
						array(
							'li'    => array( 'id' => array() ),
							'ul'    => array( 'id' => array() ),
							'label' => array( 'class' => array() ),
							'input' => array(
								'value'       => array(),
								'type'        => array(),
								'name'        => array(),
								'id'          => array(),
								'placeholder' => array(),
								'checked'     => array(),
							),
						)
					);
					?>
				</div>
			</td>
		</tr>
		<?php
		$this->enqueue_script_and_style();
	}

	public function save_field( $term_id ) {

		if ( ! isset( $_REQUEST['tax_input'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$entity_types = array();
		if ( isset( $_REQUEST['tax_input'][ Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$entity_types_data = filter_var_array( $_REQUEST, array( 'tax_input' => array( 'flags' => FILTER_REQUIRE_ARRAY ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$entity_types      = $entity_types_data['tax_input'][ Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ];
		}

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
			add_action( "{$taxonomy}_edit_form_fields", array( $this, 'render_ui' ), 1 );
			add_action( "edited_{$taxonomy}", array( $this, 'save_field' ) );
		}
	}

	private function enqueue_script_and_style() {

		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-vocabulary-term',
			plugin_dir_url( dirname( __DIR__ ) ) . '/js/dist/vocabulary-term',
			array( 'wp-polyfill' )
		);
		wp_enqueue_style(
			'wl-vocabulary-term',
			plugin_dir_url( dirname( __DIR__ ) ) . '/js/dist/vocabulary-term.css',
			array(),
			WORDLIFT_VERSION
		);
	}

}
