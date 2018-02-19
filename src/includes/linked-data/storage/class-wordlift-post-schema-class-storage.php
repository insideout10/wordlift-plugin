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

		// @@todo: get the Entity Type Service here to get the correct
		// schema class. There's no need anymore to extend the
		// Wordlift_Post_Taxonomy_Storage.

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

		// @@todo: use Wordlift_Entity_Type_Service here to get the correct
		// schema class.

		// Get the schema by term slug if the terms are not empty.
		// Otherwise fallback to webpage, which means that the post is not an entity.
		if ( ! empty( $terms ) ) {
			$schema = $this->schema_service->get_schema( $terms[0]->slug );
		} else {
			$schema = $this->schema_service->get_schema( 'webpage' );
		}

		// Finally return the schema uri.
		return $schema['uri'];
	}

}
