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
 * Define the Wordlift_Schemaorg_Sync_Service class.
 *
 * @since 3.20.0
 */
class Wordlift_Schemaorg_Sync_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The singleton instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Schemaorg_Sync_Service $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * Create a {@link Wordlift_Schemaorg_Sync_Service} instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Hook the `wl_sync_schemaorg` ajax action.
		add_action( 'wp_ajax_wl_sync_schemaorg', array( $this, 'load' ) );

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 3.20.0
	 *
	 * @return \Wordlift_Schemaorg_Sync_Service The singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Load the Schema.org classes from a file.
	 *
	 * @since 3.20.0
	 *
	 * @return bool True if successful otherwise false.
	 */
	public function load_from_file() {

		// Load the file contents.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( __DIR__ . '/schema-classes.json' );

		// Load the file contents.
		return $this->load( $contents );
	}

	/**
	 * Load the Schema.org classes from the provided contents.
	 *
	 * @param $contents
	 *
	 * @return bool
	 */
	private function load( $contents ) {

		// Decode the JSON contents.
		$json = json_decode( $contents, true );

		if ( null === $json ) {
			$this->log->error( 'Invalid json.' );

			// Error: invalid body.
			return false;
		}

		if ( ! isset( $json['schemaClasses'] ) ) {
			$this->log->error( '`schemaClasses` missing from json.' );

			// Error: invalid json.
			return false;
		}

		// Load the Schema.org classes.
		foreach ( $json['schemaClasses'] as $schema_class ) {
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

		return true;
	}

}
