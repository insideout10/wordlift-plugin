<?php
/**
 * Storage: Post Schema Class Storage.
 *
 * Get the schema class of a {@link WP_Post}.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Schema_Class_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Schema_Class_Storage extends Wordlift_Post_Taxonomy_Storage {

	/**
	 * The {@link Wordlift_Schema_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	private $schema_service;

	/**
	 * Create a {@link Wordlift_Post_Schema_Class_Storage} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param string                   $taxonomy       The taxonomy with the entity type.
	 * @param \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	public function __construct( $taxonomy, $schema_service ) {
		parent::__construct( $taxonomy );

		$this->schema_service = $schema_service;

	}

	/**
	 * Get the schema class for the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of schema classes.
	 */
	public function get( $post_id ) {
		$terms = parent::get( $post_id );

		$schema_service = $this->schema_service;

		return array_map( function ( $item ) use ( $schema_service ) {
			$schema = $schema_service->get_schema( $item->slug );

			return $schema['uri'];
		}, $terms );

	}

}
