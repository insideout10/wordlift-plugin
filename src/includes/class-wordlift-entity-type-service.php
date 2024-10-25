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
	 * Wordlift_Entity_Type_Service constructor.
	 *
	 * @since 3.7.0
	 */
	protected function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Type_Service' );

		$this->schema_service = Wordlift_Schema_Service::get_instance();

		$this->prepare_post_types();

	}

	/**
	 * Prepare post types for Gutenberg use
	 *
	 * @since 3.26.0
	 */
	private function prepare_post_types() {

		add_action(
			'init',
			function () {
				// Add post type support for 'custom-fields' for all post types. Specifically needed in Gutenberg
				$post_types = get_post_types();
				foreach ( $post_types as $post_type ) {
					add_post_type_support( $post_type, 'custom-fields' );
				}
			}
		);
	}

	/**
	 * The {@link Wordlift_Entity_Type_Service} singleton instance.
	 *
	 * @since  3.7.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $instance The {@link Wordlift_Entity_Type_Service} singleton instance.
	 */
	private static $instance = null;

	/**
	 * Get the {@link Wordlift_Entity_Type_Service} singleton instance.
	 *
	 * @return \Wordlift_Entity_Type_Service The {@link Wordlift_Entity_Type_Service} singleton instance.
	 * @since 3.7.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the types associated with the specified entity post id.
	 *
	 * We have a strategy to define the entity type, given that everything is
	 * an entity, i.e. also posts/pages and custom post types.
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
	 * }
	 * @since 3.33.9 The `linked_data` key has been removed.
	 *
	 * @since 3.20.0 This function will **not** return entity types introduced with 3.20.0.
	 *
	 * @since 3.18.0 The cases are the following:
	 *  1. the post has a term from the Entity Types Taxonomy: the term defines
	 *     the entity type, e.g. Organization, Person, ...
	 *  2. the post doesn't have a term from the Entity Types Taxonomy:
	 *      a) the post is a `wl_entity` custom post type, then the post is
	 *           assigned the `Thing` entity type by default.
	 *      b) the post is a `post` post type, then the post is
	 *           assigned the `Article` entity type by default.
	 *      c) the post is a custom post type then it is
	 *          assigned the `WebPage` entity type by default.
	 */
	public function get( $post_id ) {

		$this->log->trace( "Getting the post type for post $post_id..." );

		// Get the post type.
		$post_type = get_post_type( $post_id );

		// Return `web-page` for non entities.
		if ( ! self::is_valid_entity_post_type( $post_type ) ) {
			$this->log->info( "Returning `web-page` for post $post_id." );

			return $this->schema_service->get_schema( 'web-page' );
		}

		// Get the type from the associated classification.
		$terms = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// Return the schema type if there is a term found.
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			// Cycle through the terms and return the first one with a valid schema.
			foreach ( $terms as $term ) {
				$this->log->debug( "Found `{$term->slug}` term for post $post_id." );

				// Try to get the schema for the term.
				$schema = $this->schema_service->get_schema( $term->slug );

				// If found, return it, ignoring the other types.
				if ( null !== $schema ) {
					// Return the entity type with the specified id.
					return $schema;
				}
			}

			/*
			 * When a schema isn't found, we return `thing`. Schema may not be found because
			 * the new schema classes that we support since #852 aren't configured in the schema
			 * service.
			 *
			 * https://github.com/insideout10/wordlift-plugin/issues/852
			 *
			 * @since 3.20.0
			 */

			return $this->schema_service->get_schema( 'thing' );
		}

		// If it's a page or post return `Article`.
		if ( in_array( $post_type, array( 'post', 'page' ), true ) ) {
			$this->log->debug( "Post $post_id has no terms, and it's a `post` type, returning `Article`." );

			// Return "Article" schema type for posts.
			return $this->schema_service->get_schema( 'article' );
		}

		// Return "Thing" schema type for entities.
		$this->log->debug( "Post $post_id has no terms, but it's a `wl_entity` type, returning `Thing`." );

		// Return the entity type with the specified id.
		return $this->schema_service->get_schema( 'thing' );

	}

	/**
	 * Get the term ids of the entity types associated to the specified post.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|WP_Error An array of entity types ids or a {@link WP_Error}.
	 * @since 3.20.0
	 */
	public function get_ids( $post_id ) {

		return wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, array( 'fields' => 'ids' ) );
	}

	/**
	 * Get the camel case names of the entity types associated to the specified post.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|WP_Error An array of entity types camel case names or a {@link WP_Error}.
	 * @since 3.20.0
	 */
	public function get_names( $post_id ) {

		$ids = $this->get_ids( $post_id );

		// Filter out invalid terms (ones without _wl_name term meta)
		return array_values(
			array_filter(
				array_map(
					function ( $id ) {
						return get_term_meta( $id, '_wl_name', true );
					},
					$ids
				)
			)
		);
	}

	/**
	 * Set the main type for the specified entity post, given the type URI.
	 *
	 * @param int    $post_id The post id.
	 * @param string $type_uri The type URI.
	 * @param bool   $replace Whether the provided type must replace the existing types, by default `true`.
	 *
	 * @since 3.8.0
	 */
	public function set( $post_id, $type_uri, $replace = true ) {

		// If the type URI is empty we remove the type.
		if ( empty( $type_uri ) ) {
			$this->log->debug( "Removing entity type for post $post_id..." );

			wp_set_object_terms( $post_id, null, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return;
		}

		$this->log->debug( "Setting entity type for post $post_id..." );

		// if the `$type_uri` starts with `wl-`, we're looking at the class name, which is `wl-` + slug.
		$term = ( 0 === strpos( $type_uri, 'wl-' ) )
			// Get term by slug.
			? $this->get_term_by_slug( substr( $type_uri, 3 ) )
			// Get term by URI.
			: $this->get_term_by_uri( $type_uri );

		/*
		 * We always want to assign a type to an entity otherwise it won't show in the Vocabulary and it won't be
		 * connected to Articles via mentions. We realized that the client JS code is passing `wl-other` when the
		 * entity type isn't "notable". In which case we couldn't find an entity type.
		 *
		 * When an entity type is not found, we'll now switch by default to "thing" which is the most basic entity type.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/991
		 *
		 * @since 3.23.4
		 */
		if ( false === $term ) {
			$this->log->warn( "No term found for URI $type_uri, will use Thing." );

			$term = $this->get_term_by_slug( 'thing' );

			// We still need to be able to bali out here, for example WordPress 5.1 tests create posts before our taxonomy
			// is installed.
			if ( false === $term ) {
				return;
			}
		}

		$this->log->debug( "Setting entity type [ post id :: $post_id ][ term id :: $term->term_id ][ term slug :: $term->slug ][ type uri :: $type_uri ]..." );

		// `$replace` is passed to decide whether to replace or append the term.
		wp_set_object_terms( $post_id, $term->term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, ! $replace );

	}

	/**
	 * Get an entity type term given its slug.
	 *
	 * @param string $slug The slug.
	 *
	 * @return false|WP_Term WP_Term instance on success. Will return false if `$taxonomy` does not exist
	 *                             or `$term` was not found.
	 * @since 3.20.0
	 */
	public function get_term_by_slug( $slug ) {

		return get_term_by( 'slug', $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
	}

	/**
	 * Get an entity type term given its URI.
	 *
	 * @param string $uri The uri.
	 *
	 * @return false|WP_Term WP_Term instance on success. Will return false if `$taxonomy` does not exist
	 *                             or `$term` was not found.
	 * @since 3.20.0
	 */
	public function get_term_by_uri( $uri ) {

		$terms = get_terms(
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			array(
				'fields'     => 'all',
				'get'        => 'all',
				'number'     => 1,
				'meta_query' => array(
					array(
						// Don't use a reference to Wordlift_Schemaorg_Class_Service, unless
						// `WL_ALL_ENTITY_TYPES` is set to true.
						'key'   => '_wl_uri',
						'value' => $uri,
					),
				),
				'orderby'    => 'term_id',
				'order'      => 'ASC',
			)
		);

		return is_array( $terms ) && ! empty( $terms ) ? $terms[0] : false;
	}

	/**
	 * Check whether an entity type is set for the {@link WP_Post} with the
	 * specified id.
	 *
	 * @param int    $post_id The {@link WP_Post}'s `id`.
	 * @param string $uri The entity type URI.
	 *
	 * @return bool True if an entity type is set otherwise false.
	 * @since 3.15.0
	 */
	public function has_entity_type( $post_id, $uri = null ) {

		$this->log->debug( "Checking if post $post_id has an entity type [ $uri ]..." );

		// If an URI hasn't been specified just check whether we have at least
		// one entity type.
		if ( null === $uri ) {
			return has_term( '', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $post_id );
		}

		$has_entity_type = $this->has_post_term_by_uri( $post_id, $uri );

		$this->log->debug( "Post $post_id has $uri type: " . ( $has_entity_type ? 'yes' : 'no' ) );

		// Check whether the post has an entity type with that URI.
		return $has_entity_type;
	}

	/**
	 * Get the list of entity types' terms for the specified {@link WP_Post}.
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 *
	 * @return array|WP_Error An array of entity types' terms or {@link WP_Error}.
	 * @since 3.15.0
	 */
	private function get_post_terms( $post_id ) {

		return wp_get_object_terms(
			$post_id,
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			array(
				'hide_empty' => false,
				// Because of #334 (and the AAM plugin) we changed fields from 'id=>slug' to 'all'.
				// An issue has been opened with the AAM plugin author as well.
				//
				// see https://github.com/insideout10/wordlift-plugin/issues/334
				// see https://wordpress.org/support/topic/idslug-not-working-anymore?replies=1#post-8806863
				'fields'     => 'all',
			)
		);
	}

	/**
	 * Get an entity type term given its URI.
	 *
	 * @param int    $post_id The {@link WP_Post} id.
	 * @param string $uri The entity type URI.
	 *
	 * @return bool True if the post has that type URI bound to it otherwise false.
	 * @since 3.15.0
	 *
	 * @since 3.20.0 function renamed to `has_post_term_by_uri` and return type changed to `bool`.
	 */
	private function has_post_term_by_uri( $post_id, $uri ) {

		// Get the post terms bound to the specified post.
		$terms = $this->get_post_terms( $post_id );

		// Look for a term if the specified URI.
		foreach ( $terms as $term ) {
			$term_uri = get_term_meta( $term->term_id, '_wl_uri', true );
			if ( $uri === $term_uri ) {
				return true;
			}
		}

		// Return null.
		return false;
	}

	/**
	 * Get the custom fields for a specific post.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array An array of custom fields (see `custom_fields` in Wordlift_Schema_Service).
	 * @since 3.25.2
	 */
	public function get_custom_fields_for_post( $post_id ) {

		// Return custom fields for this specific entity's type.
		$types = $this->get_ids( $post_id );

		/** @var WP_Term[] $terms */
		$terms = array_filter(
			array_map(
				function ( $item ) {
					return get_term( $item );
				},
				$types
			),
			function ( $item ) {
				return isset( $item ) && is_a( $item, 'WP_Term' );
			}
		);

		$term_slugs = array_map(
			function ( $item ) {
				return $item->slug;
			},
			$terms
		);

		$term_slugs[] = 'thing';

		return $this->get_custom_fields_by_term_slugs( $term_slugs );
	}

	/**
	 * Get the custom fields for a specific term.
	 *
	 * @param int $term_id The term ID.
	 *
	 * @return array|null An array of custom fields (see `custom_fields` in Wordlift_Schema_Service).
	 * @since 3.32.0
	 */
	public function get_custom_fields_for_term( $term_id ) {
		$selected_entity_types = get_term_meta( $term_id, 'wl_entity_type' );
		if ( ! is_array( $selected_entity_types ) ) {
			return null;
		}

		$selected_entity_types[] = 'thing';
		$selected_entity_types   = array_unique( $selected_entity_types );

		return $this->get_custom_fields_by_term_slugs( $selected_entity_types );
	}

	/**
	 * Determines whether a post type can be used for entities.
	 *
	 * Criteria is that the post type is public. The list of valid post types
	 * can be overridden with a filter.
	 *
	 * @param string $post_type A post type name.
	 *
	 * @return bool Return true if the post type can be used for entities, otherwise false.
	 * @since 3.15.0
	 */
	public static function is_valid_entity_post_type( $post_type ) {

		return in_array( $post_type, Wordlift_Entity_Service::valid_entity_post_types(), true );
	}

	/**
	 * @param $term_slugs
	 *
	 * @return array
	 */
	private function get_custom_fields_by_term_slugs( $term_slugs ) {
		$schema_service = Wordlift_Schema_Service::get_instance();

		return array_reduce(
			$term_slugs,
			function ( $carry, $item ) use ( $schema_service ) {

				$schema = $schema_service->get_schema( $item );

				if ( ! isset( $schema['custom_fields'] ) ) {
					return $carry;
				}

				return $carry + $schema['custom_fields'];
			},
			array()
		);
	}

}
