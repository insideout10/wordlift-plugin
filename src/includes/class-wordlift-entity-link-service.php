<?php

/**
 * Services: Entity Link Service.
 *
 * The Wordlift_Entity_Link_Service handles linking and rendering of entities. In order to perform such actions it hooks
 * on different WordPress' actions and filters:
 *
 *  1. to change links to entities, also supporting an empty entity post type slug, e.g. http://example.org/entity-name
 *     instead of http://example.org/entity/entity-name, this requires hooking to:
 *     a) post_type_link in order to remove the 'entity' post type slug, when links are rendered by WordPress,
 *     b) pre_get_posts in order to alter the WP Query instance and add our own entity post type to the query when WordPress
 *        needs to decide which post to show (otherwise we would get a 404).
 *
 *  2. when using an empty entity post type slug (but we perform in any case, should the entity post type slug set to
 *     empty later on), we need to ensure that the posts/pages/entities' slugs to not conflict (even though WordPress
 *     itself allows conflicts, see https://core.trac.wordpress.org/ticket/13459 ). To do so we hook to a couple of filters
 *     to validate a slug:
 *     a) for posts and entities, to wp_unique_post_slug_is_bad_flat_slug, and we check that no other page, post or entity
 *        uses that slug,
 *     b) for pages, to wp_unique_post_slug_is_bad_hierarchical_slug.
 *     If we find that the slug (postname) is already used, we tell WordPress, which in turn will append a sequential number.
 *
 * @since 3.6.0
 */
class Wordlift_Entity_Link_Service {

	/**
	 * The entity type service.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var Wordlift_Entity_Post_Type_Service $entity_type_service The entity type service.
	 */
	private $entity_type_service;

	/**
	 * The entity post type slug.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var string $slug The entity post type slug.
	 */
	private $slug;

	/**
	 * A logger instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var Wordlift_Log_Service
	 */
	private $log;

	/**
	 * Wordlift_Entity_Link_Service constructor.
	 *
	 * @since 3.6.0
	 *
	 * @param Wordlift_Entity_Post_Type_Service $entity_type_service
	 * @param string                            $slug The entity post type slug.
	 */
	public function __construct( $entity_type_service, $slug ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Link_Service' );

		$this->entity_type_service = $entity_type_service;
		$this->slug                = $slug;

	}

	/**
	 * Intercept link generation to posts in order to customize links to entities.
	 *
	 * @since 3.6.0
	 *
	 * @param string  $post_link The post's permalink.
	 * @param WP_Post $post      The post in question.
	 *
	 * @return string The link to the post.
	 */
	public function post_type_link( $post_link, $post ) {

		// Return the post link if this is not our post type.
		if ( ! empty( $this->slug ) || $this->entity_type_service->get_post_type() !== get_post_type( $post ) ) {
			return $post_link;
		}

		// Replace /slug/post_name/ with /post_name/
		// The slug comes from the Entity Type Service since that service is responsible for registering the default
		// slug.
		return str_replace( "/{$this->entity_type_service->get_slug()}/$post->post_name/", "/$post->post_name/", $post_link );
	}

	/**
	 * Alter the query to look for our own custom type.
	 *
	 * @since 3.6.0
	 *
	 * @param WP_Query $query
	 */
	public function pre_get_posts( $query ) {

		// If a slug has been set, we don't need to alter the query.
		if ( ! empty( $this->slug ) ) {
			return;
		}

		// Check if it's a query we should extend with our own custom post type.
		//
		// The `$query->query` count could be > 2 if the preview parameter is passed too.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/439
		if ( ! $query->is_main_query() || 2 > count( $query->query ) || ! isset( $query->query['page'] ) || empty( $query->query['name'] ) ) {
			return;
		}

		// Add our own post type to the query.
		$post_types = '' === $query->get( 'post_type' )
			? Wordlift_Entity_Service::valid_entity_post_types()
			: array_merge( (array) $query->get( 'post_type' ), (array) $this->entity_type_service->get_post_type() );
		$query->set( 'post_type', $post_types );

	}

	/**
	 * Hook to WordPress' wp_unique_post_slug_is_bad_flat_slug filter. This is called when a page is saved.
	 *
	 * @since 3.6.0
	 *
	 * @param bool   $bad_slug  Whether the post slug would be bad as a flat slug.
	 * @param string $slug      The post slug.
	 * @param string $post_type Post type.
	 *
	 * @return bool Whether the slug is bad.
	 */
	public function wp_unique_post_slug_is_bad_flat_slug( $bad_slug, $slug, $post_type ) {

		// The list of post types that might have conflicting slugs.
		$post_types = Wordlift_Entity_Service::valid_entity_post_types();

		// Ignore post types different from the ones we need to check.
		if ( ! in_array( $post_type, $post_types, true ) ) {
			return $bad_slug;
		}

		// We remove the request post type since WordPress is already checking that the slug doesn't conflict.
		$exists = $this->slug_exists( $slug, array_diff( $post_types, array( $post_type ) ) );

		$this->log->debug( "Checking if a slug exists [ post type :: $post_type ][ slug :: $slug ][ exists :: " . ( $exists ? 'yes' : 'no' ) . ' ]' );

		return apply_filters( 'wl_unique_post_slug_is_bad_flat_slug', $exists, $bad_slug, $slug, $post_type );
	}

	/**
	 * Check whether a slug exists already for the specified post types.
	 *
	 * @since 3.6.0
	 *
	 * @param string $slug       The slug.
	 * @param array  $post_types An array of post types.
	 *
	 * @return bool True if the slug exists, otherwise false.
	 */
	private function slug_exists( $slug, $post_types ) {
		global $wpdb;

		// Loop through all post types and check
		// whether they have archive pages and if
		// the archive slug matches the post slug.
		//
		// Note that the condition below checks only post types used by WordLift.
		// We don't check other post types for archive pages,
		// because this is a job of WordPress.
		//
		// There is a open ticket that should solve this, when it's merged:
		// https://core.trac.wordpress.org/ticket/13459
		$all_post_types = Wordlift_Entity_Service::valid_entity_post_types();
		foreach ( $all_post_types as $post_type ) {

			// Get the post type object for current post type.
			$post_type_object = get_post_type_object( $post_type );

			if (
				// Check whether the post type object is not empty.
				! empty( $post_type_object ) &&
				// And the post type has archive page.
				$post_type_object->has_archive &&
				// And `rewrite` options exists..
				! empty( $post_type_object->rewrite ) &&
				// And the `rewrite` slug property is not empty.
				! empty( $post_type_object->rewrite['slug'] ) &&
				// And if the rewrite slug equals to the slug.
				$post_type_object->rewrite['slug'] === $slug
			) {
				// Return true which means that the slug is already in use.
				return true;
			}
		}

		return null !== $wpdb->get_var(
			$wpdb->prepare(
				sprintf(
					"SELECT post_name
			FROM $wpdb->posts
			WHERE post_name = %s
			AND post_type IN (%s)
			LIMIT 1
			",
					'%s',
					implode( ',', array_fill( 0, count( $post_types ), '%s' ) )
				),
				array_merge( array( $slug ), $post_types )
			)
		);
	}

}
