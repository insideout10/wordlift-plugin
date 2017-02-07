<?php
/**
 * Define the Entity Type Service.
 *
 * @since   3.7.0
 * @package Wordlift
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
	 * @since  3.7.0
	 * @access private
	 * @var \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	private $schema_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Entity_Type_Service} singleton instance.
	 *
	 * @since  3.7.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $instance The {@link Wordlift_Entity_Type_Service} singleton instance.
	 */
	private static $instance;

	/**
	 * Wordlift_Entity_Type_Service constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	public function __construct( $schema_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Type_Service' );

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
	 * Get the types associated with the specified entity post id.
	 *
	 * @since 3.7.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|null An array of type properties or null if no term is associated
	 */
	public function get( $post_id ) {

		// Return the correct entity type according to the post type.
		switch ( get_post_type( $post_id ) ) {

			case 'entity':
				// Get the type from the associated classification.

				$terms = wp_get_object_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

				if ( is_wp_error( $terms ) ) {
					// TODO: handle error
					return null;
				}

				// If there are not terms associated, return null.
				if ( 0 === count( $terms ) ) {
					return null;
				}

				// Return the entity type with the specified id.
				return $this->schema_service->get_schema( $terms[0]->slug );

			case 'post':
			case 'page':
				// Posts and pages are considered Articles.
				return array(
					'uri'       => 'http://schema.org/Article',
					'css_class' => 'wl-post',
				);

			default:
				// Everything else is considered a Creative Work.
				return array(
					'uri'       => 'http://schema.org/CreativeWork',
					'css_class' => 'wl-creative-work',
				);
		}

	}

	/**
	 * Set the main type for the specified entity post, given the type URI.
	 *
	 * @since 3.8.0
	 *
	 * @param int    $post_id  The post id.
	 * @param string $type_uri The type URI.
	 */
	public function set( $post_id, $type_uri ) {

		// If the type URI is empty we remove the type.
		if ( empty( $type_uri ) ) {

			wp_set_object_terms( $post_id, null, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

			return;
		}

		// Get all the terms bound to the wl_entity_type taxonomy.
		$terms = get_terms( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
			// Because of #334 (and the AAM plugin) we changed fields from 'id=>slug' to 'all'.
			// An issue has been opened with the AAM plugin author as well.
			//
			// see https://github.com/insideout10/wordlift-plugin/issues/334
			// see https://wordpress.org/support/topic/idslug-not-working-anymore?replies=1#post-8806863
			'fields'     => 'all',
		) );

		$this->log->error( "Type not found [ post id :: $post_id ][ type uri :: $type_uri ]" );

		// Check which term matches the specified URI.
		foreach ( $terms as $term ) {

			$term_id   = $term->term_id;
			$term_slug = $term->slug;

			// Load the type data.
			$type = $this->schema_service->get_schema( $term_slug );
			// Set the related term ID.
			if ( $type_uri === $type['uri'] || $type_uri === $type['css_class'] ) {

				$this->log->debug( "Setting entity type [ post id :: $post_id ][ term id :: $term_id ][ term slug :: $term_slug ][ type uri :: {$type['uri']} ][ type css class :: {$type['css_class']} ]" );

				wp_set_object_terms( $post_id, (int) $term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

				return;
			}
		}

	}

}
