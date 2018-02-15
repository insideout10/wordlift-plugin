<?php
/**
 * Services: Entity Type Service.
 *
 * Define the Entity Type Service.
 *
 * @since      3.7.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * The Wordlift_Entity_Type_Service provides functions to manipulate an entity
 * type.
 *
 * @since      3.7.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
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
	 * We have a strategy to define the entity type, given that everything is
	 * an entity, i.e. also posts/pages and custom post types.
	 *
	 * @since 3.18.0 the cases are the following:
	 *  1. the post has a term from the Entity Types Taxonomy: the term defines
	 *     the entity type, e.g. Organization, Person, ...
	 *  2. the post doesn't have a term from the Entity Types Taxonomy:
	 *      a) the post is a `wl_entity` custom post type, then the post is
	 *           assigned the `Thing` entity type by default.
	 *      b) the post is not a `wl_entity` custom post type then it is
	 *          assigned the `WebPage` entity type by default.
	 *
	 * @since 3.7.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|null {
	 * An array of type properties or null if no term is associated
	 *
	 * @type string css_class     The css class, e.g. `wl-thing`.
	 * @type string uri           The schema.org class URI, e.g. `http://schema.org/Thing`.
	 * @type array  same_as       An array of same as attributes.
	 * @type array  custom_fields An array of custom fields.
	 * @type array  linked_data   An array of {@link Wordlift_Sparql_Tuple_Rendition}.
	 * }
	 */
	public function get( $post_id ) {

		$this->log->trace( "Getting the post type for post $post_id..." );

		// Get the type from the associated classification.
		$terms = wp_get_object_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		if ( is_wp_error( $terms ) ) {
			$this->log->error( "An error occurred while getting the post type for post $post_id: " . $terms->get_error_message() );

			// TODO: handle error
			return null;
		}

		// Return the schema type if there is a term found.
		if ( 0 !== count( $terms ) ) {
			$this->log->debug( "Found {$terms[0]->slug} term for post $post_id." );

			// Return the entity type with the specified id.
			return $this->schema_service->get_schema( $terms[0]->slug );
		}

		// Get the post type.
		$post_type = get_post_type( $post_id );

		// If it's not an entity post type return `WebPage` by default.
		if ( ! self::is_valid_entity_post_type( $post_type ) ) {
			$this->log->info( "Returning `WebPage` for post $post_id." );

			// Return the entity type with the specified id.
			return $this->schema_service->get_schema( 'webpage' );
		}

		$this->log->debug( "Post $post_id has no terms, returning `Thing`." );

		// Return the entity type with the specified id.
		return $this->schema_service->get_schema( 'thing' );
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
			$this->log->debug( "Removing entity type for post $post_id..." );

			wp_set_object_terms( $post_id, null, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

			return;
		}

		$this->log->debug( "Setting entity type for post $post_id..." );

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

		// Check which term matches the specified URI.
		foreach ( $terms as $term ) {

			$term_id   = $term->term_id;
			$term_slug = $term->slug;

			$this->log->trace( "Parsing term {$term->slug}..." );

			// Load the type data.
			$type = $this->schema_service->get_schema( $term_slug );

			// Set the related term ID.
			if ( $type_uri === $type['uri'] || $type_uri === $type['css_class'] ) {

				$this->log->debug( "Setting entity type [ post id :: $post_id ][ term id :: $term_id ][ term slug :: $term_slug ][ type uri :: {$type['uri']} ][ type css class :: {$type['css_class']} ]" );

				wp_set_object_terms( $post_id, (int) $term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

				return;
			}
		}

		$this->log->error( "Type not found [ post id :: $post_id ][ type uri :: $type_uri ]" );

	}

	/**
	 * Check whether an entity type is set for the {@link WP_Post} with the
	 * specified id.
	 *
	 * @since 3.15.0
	 *
	 * @param int    $post_id The {@link WP_Post}'s `id`.
	 * @param string $uri     The entity type URI.
	 *
	 * @return bool True if an entity type is set otherwise false.
	 */
	public function has_entity_type( $post_id, $uri = null ) {

		$this->log->debug( "Checking if post $post_id has an entity type [ $uri ]..." );

		// If an URI hasn't been specified just check whether we have at least
		// one entity type.
		if ( null === $uri ) {

			// Get the post terms for the specified post ID.
			$terms = $this->get_post_terms( $post_id );

			$this->log->debug( "Post $post_id has " . count( $terms ) . ' type(s).' );

			// True if there's at least one term bound to the post.
			return ( 0 < count( $terms ) );
		}

		$has_entity_type = ( null !== $this->get_term_by_uri( $post_id, $uri ) );

		$this->log->debug( "Post $post_id has $uri type: " . ( $has_entity_type ? 'yes' : 'no' ) );

		// Check whether the post has an entity type with that URI.
		return $has_entity_type;
	}

	/**
	 * Get the list of entity types' terms for the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 *
	 * @return array|WP_Error An array of entity types' terms or {@link WP_Error}.
	 */
	private function get_post_terms( $post_id ) {

		return wp_get_post_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, array(
			'hide_empty' => false,
			// Because of #334 (and the AAM plugin) we changed fields from 'id=>slug' to 'all'.
			// An issue has been opened with the AAM plugin author as well.
			//
			// see https://github.com/insideout10/wordlift-plugin/issues/334
			// see https://wordpress.org/support/topic/idslug-not-working-anymore?replies=1#post-8806863
			'fields'     => 'all',
		) );
	}

	/**
	 * Get an entity type term given its URI.
	 *
	 * @since 3.15.0
	 *
	 * @param int    $post_id The {@link WP_Post} id.
	 * @param string $uri     The entity type URI.
	 *
	 * @return array|null {
	 * An array of entity type properties or null if no term is associated
	 *
	 * @type string css_class     The css class, e.g. `wl-thing`.
	 * @type string uri           The schema.org class URI, e.g. `http://schema.org/Thing`.
	 * @type array  same_as       An array of same as attributes.
	 * @type array  custom_fields An array of custom fields.
	 * }
	 */
	private function get_term_by_uri( $post_id, $uri ) {

		// Get the post terms bound to the specified post.
		$terms = $this->get_post_terms( $post_id );

		// Look for a term if the specified URI.
		foreach ( $terms as $term ) {
			// Get the schema by slug.
			$schema = $this->schema_service->get_schema( $term->slug );

			// Continue to the next one, if a schema isn't found (or hasn't got
			// and URI.
			if ( null === $schema || ! isset( $schema['uri'] ) ) {
				continue;
			}

			// Return the schema if the URI matches.
			if ( $uri === $schema['uri'] ) {
				return $schema;
			}
		}

		// Return null.
		return null;
	}


	/**
	 * Determines whether a post type can be used for entities.
	 *
	 * Criteria is that the post type is public. The list of valid post types
	 * can be overridden with a filter.
	 *
	 * @since 3.15.0
	 *
	 * @param string $post_type A post type name.
	 *
	 * @return bool Return true if the post type can be used for entities, otherwise false.
	 */
	public static function is_valid_entity_post_type( $post_type ) {

		return in_array( $post_type, Wordlift_Entity_Service::valid_entity_post_types(), true );
	}

}
