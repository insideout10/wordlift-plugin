<?php
/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms\Jsonld;

use Wordlift_Entity_Type_Taxonomy_Service;

class Jsonld_Generator {

	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;

	public function __construct( $entity_type_service ) {
		$this->entity_type_service = $entity_type_service;
	}

	public function init() {
		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array' ), 10, 2 );
	}

	public function wl_term_jsonld_array( $data, $term_id ) {
		$jsonld     = $data['jsonld'];
		$references = $data['references'];

		$term_jsonld = $this->get_jsonld_for_term( $term_id );

		array_unshift( $jsonld, $term_jsonld  );

		return array(
			'jsonld'     => $jsonld,
			'references' => $references
		);
	}

	private function get_jsonld_for_term( $term_id ) {

		$custom_fields = $this->entity_type_service->get_custom_fields_for_term( $term_id );

		return array(
			'@context' => 'http://schema.org',
			'@type'    => $this->get_all_selected_entity_type_labels( $term_id )
		);

	}

	private function get_all_selected_entity_type_labels( $term_id ) {
		$selected_entity_type_slugs = get_term_meta( $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		return array_filter( array_map( function ( $type_slug ) {
			$term = get_term_by( 'slug', $type_slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			if ( ! $term ) {
				return false;
			}

			return $term->name;
		}, $selected_entity_type_slugs ) );
	}

}