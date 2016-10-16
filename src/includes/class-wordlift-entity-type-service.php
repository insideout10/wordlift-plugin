<?php

/**
 * Define the Entity Type Service.
 */

/**
 * The Wordlift_Entity_Type_Service provides functions to manipulate an entity
 * type.
 *
 * @since 3.7.0
 */
class Wordlift_Entity_Type_Service {

	/**
	 * The {@link Wordlift_Schema_Service} instance.
	 *
	 * @since 3.7.0
	 * @access private
	 * @var \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	private $schema_service;

	private static $instance;

	/**
	 * Wordlift_Entity_Type_Service constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	public function __construct( $schema_service ) {

		$this->schema_service = $schema_service;

		self::$instance = $this;

	}

	/**
	 * Get the {@link Wordlift_Entity_Type_Service} singleton instance.
	 *
	 * @since 3.7.0
	 * @return \Wordlift_Entity_Type_Service The {@link Wordlift_Entity_Type_Service} singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * @since 3.7.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|null An array of type properties or null if no term is associated
	 */
	public function get( $post_id ) {

		$terms = wp_get_object_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		if ( is_wp_error( $terms ) ) {
			// TODO: handle error
			return NULL;
		}

		// If there are not terms associated, return null.
		if ( 0 === count( $terms ) ) {
			return NULL;
		}

		// Return the entity type with the specified id.
		return $this->schema_service->get_schema( $terms[0]->slug );
	}

}
