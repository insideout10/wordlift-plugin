<?php

namespace Wordlift\Modules\Pods;

class Schema {

	/**
	 * @return Schema_Field_Group[]
	 */
	public function get() {
		// we need to identify the context to filter the results.

		$identifier = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : '';

		if ( $identifier ) {
			// If post identifier, get schema.
			return $this->get_fields_for_post( $identifier );
		}

		$identifier = isset( $_GET['tag_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['tag_ID'] ) ) : '';

		if ( $identifier ) {
			// If post identifier, get schema.
			return $this->get_fields_for_term( $identifier );
		}

		return array();

	}

	/**
	 * @return Schema_Field_Group[]
	 */
	private function get_fields_for_post( $identifier ) {
		$types          = \Wordlift_Entity_Type_Service::get_instance()->get_names( $identifier );
		$schema_classes = \Wordlift_Schema_Service::get_instance();
		return array_map(
			function ( $schema_type ) use ( $schema_classes ) {
				$data = $schema_classes->get_schema( strtolower( $schema_type ) );
				return new Schema_Field_Group( $schema_type, $data['custom_fields'] );
			},
			$types
		);
	}

	/**
	 * @return Schema_Field_Group[]
	 */
	private function get_fields_for_term( $identifier ) {
		$term_entity_types = get_term_meta( (int) $identifier, \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$schema_classes    = \Wordlift_Schema_Service::get_instance();
		return array_map(
			function ( $schema_type ) use ( $schema_classes ) {
				$data = $schema_classes->get_schema( strtolower( $schema_type ) );
				return new Schema_Field_Group( $schema_type, $data['custom_fields'] );
			},
			$term_entity_types
		);
	}


}
