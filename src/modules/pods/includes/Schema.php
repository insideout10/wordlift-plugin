<?php

namespace Wordlift\Modules\Pods;

class Schema {

	public function get_context_type() {

		if ( isset( $_REQUEST['post'] ) || isset( $_REQUEST['post_ID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return Context::POST;
		}
		if ( isset( $_REQUEST['tag_ID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return Context::TERM;
		}

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return Context::ADMIN_AJAX;
		}

		return Context::UNKNOWN;
	}

	/**
	 * @return Context
	 */
	public function get() {
		// we need to identify the context to filter the results.

		$identifier = isset( $_REQUEST['post'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			: sanitize_text_field( wp_unslash( isset( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $identifier ) {
			// If post identifier, get schema.
			return new Context( Context::POST, $identifier, $this->get_fields_for_post( $identifier ) );
		}

		$identifier = isset( $_REQUEST['tag_ID'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tag_ID'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $identifier ) {
			// If post identifier, get schema.
			return new Context( Context::TERM, $identifier, $this->get_fields_for_term( $identifier ) );
		}

		if ( is_admin() && defined( 'DOING_AJAX' ) ) {
			return new Context( Context::ADMIN_AJAX, null, $this->get_all_fields() );
		}

		return new Context( Context::UNKNOWN, null, null );

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

		return array_filter(
			array_map(
				function ( $schema_type ) use ( $schema_classes ) {
					$data = $schema_classes->get_schema( strtolower( $schema_type ) );

					if ( ! $data ) {
						return false;
					}

					return new Schema_Field_Group( $schema_type, $data['custom_fields'] );
				},
				$term_entity_types
			)
		);
	}

	private function get_all_fields() {
		$schema_classes   = \Wordlift_Schema_Service::get_instance();
		$all_schema_slugs = $schema_classes->get_all_schema_slugs();

		return array_filter(
			array_map(
				function ( $schema_type ) use ( $schema_classes ) {
					$data = $schema_classes->get_schema( strtolower( $schema_type ) );

					return new Schema_Field_Group( $schema_type, $data['custom_fields'] );
				},
				$all_schema_slugs
			)
		);
	}

}
