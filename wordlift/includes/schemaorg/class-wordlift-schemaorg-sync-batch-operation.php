<?php
/**
 * Services: Schema.org Sync Service.
 *
 * Provide the function to synchronize the Schema.org hierarchy with the local taxonomy.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/schemaorg
 */

/**
 * Define the Wordlift_Schemaorg_Sync_Batch_Operation class.
 *
 * @since 3.20.0
 */
class Wordlift_Schemaorg_Sync_Batch_Operation implements Wordlift_Batch_Operation_Interface {

	private static $instance = null;

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Process the batch operation starting from the specified offset.
	 *
	 * @param int $offset Start from the specified offset (or 0 if not specified).
	 * @param int $limit Process the specified amount of items per call (or 10 if not specified).
	 *
	 * @return array {
	 * The operation result.
	 *
	 * @type int $next The next offset.
	 * @type int $limit The amount of items to process per call.
	 * @type int $remaining The remaining number of elements to process.
	 * }
	 * @since 3.20.0
	 */
	public function process( $offset = 0, $limit = 10 ) {

		// Get the schema classes.
		$all_schema_classes = $this->get_schema_classes();

		// Get only the part that we need to process.
		$schema_classes = array_slice( $all_schema_classes, $offset, $limit );

		// Load the Schema.org classes.
		foreach ( $schema_classes as $schema_class ) {
			$slug = $schema_class['dashname'];
			$term = term_exists( $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			$args = array(
				'parent'      => 0,
				'description' => $schema_class['description'],
				'slug'        => $schema_class['dashname'],
			);
			if ( null !== $term ) {
				wp_update_term( $term['term_id'], Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $args );
			} else {
				$term = wp_insert_term( $schema_class['name'], Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $args );
			}

			// Update the parents/children relationship.
			delete_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::PARENT_OF_META_KEY );
			foreach ( $schema_class['children'] as $child ) {
				add_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::PARENT_OF_META_KEY, $child['dashname'] );
			}

			// Update the term name.
			delete_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::NAME_META_KEY );
			update_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::NAME_META_KEY, $schema_class['name'] );

			// Update the term URI.
			delete_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::URI_META_KEY );
			update_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::URI_META_KEY, "http://schema.org/{$schema_class['name']}" );

		}

		// Calculate the return values.
		$next      = $offset + $limit;
		$remaining = $this->count();

		return array(
			'next'      => $next,
			'limit'     => $limit,
			'complete'  => ( 0 === $remaining ),
			'remaining' => $remaining,
		);
	}

	/**
	 * Count the number of elements that would be affected by the operation.
	 *
	 * @return int The number of elements that would be affected.
	 * @since 3.20.0
	 */
	public function count() {

		// Schema Classes count.
		$schema_classes_count = count( $this->get_schema_classes() );

		// Terms count.
		$terms_count = wp_count_terms( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// Return the difference.
		return $schema_classes_count - $terms_count;
	}

	/**
	 * Get the schema.org classes from the JSON file.
	 *
	 * @return array An array of schema classes.
	 * @since 3.20.0
	 */
	private function get_schema_classes() {

		// Load the file contents.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( __DIR__ . '/schema-classes.json' );

		// Decode the JSON contents.
		$json = json_decode( $contents, true );

		// Return the schema classes or an empty array.
		return isset( $json['schemaClasses'] ) ? $json['schemaClasses'] : array();
	}

}
