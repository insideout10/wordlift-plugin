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
	 * @param bool    $leavename Whether to keep the post name.
	 * @param bool    $sample    Is it a sample permalink.
	 *
	 * @return string The link to the post.
	 */
	public function post_type_link( $post_link, $post, $leavename, $sample ) {

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
		$post_type = is_array( $query->get( 'post_type' ) ) ? $query->get( 'post_type' ) : array();
		$query->set( 'post_type', array_merge( $post_type, array(
			'post',
			$this->entity_type_service->get_post_type(),
			'page',
		) ) );

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
		$post_types = array(
			'post',
			'page',
			$this->entity_type_service->get_post_type(),
		);

		// Ignore post types different from the ones we need to check.
		if ( ! in_array( $post_type, $post_types ) ) {
			return $bad_slug;
		}

		$exists = $this->slug_exists( $slug, array_diff( $post_types, array( $post_type ) ) );

		$this->log->debug( "Checking if a slug exists [ post type :: $post_type ][ slug :: $slug ][ exists :: " . ( $exists ? "yes" : "no" ) . " ]" );

		return $exists;
	}

	/**
	 * Hook to WordPress' wp_unique_post_slug_is_bad_hierarchical_slug filter. This is called when a page is saved.
	 *
	 * @since 3.6.0
	 *
	 * @param bool   $bad_slug  Whether the post slug would be bad as a flat slug.
	 * @param string $slug      The post slug.
	 * @param string $post_type Post type.
	 * @param int    $post_parent
	 *
	 * @return bool Whether the slug is bad.
	 */
	public function wp_unique_post_slug_is_bad_hierarchical_slug( $bad_slug, $slug, $post_type, $post_parent ) {

		// We only care about pages here.
		if ( 'page' !== $post_type ) {
			return $bad_slug;
		}

		// We redirect the call to the flat hook, this means that this check is going to solve also the 6-years old issue
		// about overlapping slugs among pages and posts:
		//  https://core.trac.wordpress.org/ticket/13459
		return $this->wp_unique_post_slug_is_bad_flat_slug( $bad_slug, $slug, $post_type );
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

		// Post slugs must be unique across all posts.
		$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type IN ('" . implode( "', '", array_map( 'esc_sql', $post_types ) ) . "') LIMIT 1";

		return null !== $wpdb->get_var( $wpdb->prepare( $check_sql, $slug ) );
	}

}
