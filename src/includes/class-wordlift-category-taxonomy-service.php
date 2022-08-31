<?php
/**
 * Services: Category Taxonomy Service.
 *
 * Enables entities to be listed in WP's queries based on categories.
 *
 * See https://github.com/insideout10/wordlift-plugin/issues/442
 *
 * @since   3.11.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Category_Taxonomy_Service} class.
 *
 * @since   3.11.0
 * @package Wordlift
 */
class Wordlift_Category_Taxonomy_Service {

	/**
	 * The {@link Wordlift_Entity_Post_Type_Service} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Entity_Post_Type_Service $entity_post_type_service The {@link Wordlift_Entity_Post_Type_Service} instance.
	 */
	private $entity_post_type_service;

	/**
	 * Create a {@link Wordlift_Category_Taxonomy_Service} instance.
	 *
	 * @since 3.11.0
	 *
	 * @param \Wordlift_Entity_Post_Type_Service $entity_post_type_service The {@link Wordlift_Entity_Post_Type_Service} instance.
	 */
	public function __construct( $entity_post_type_service ) {

		$this->entity_post_type_service = $entity_post_type_service;

	}

	/**
	 * Set the entity post types as one to be included in archive pages.
	 *
	 * In order to have entities show up in standard WP categories (Posts categories)
	 * we configure the `entity` post type, but we also need to alter the main
	 * WP query (which by default queries posts only) to include the `entities`.
	 *
	 * @since 3.11.0
	 *
	 * @param WP_Query $query WP's {@link WP_Query} instance.
	 */
	public function pre_get_posts( $query ) {

		// Only for the main query, avoid problems with widgets and what not.
		if ( ! $query->is_main_query() ) {
			return;
		}

		// We don't want to alter the query if we're in the admin UI, if this is
		// not a category query, or if the `suppress_filters` is set.
		//
		// Note that it is unlikely for `suppress_filter` to be set on the front
		// end, but let's be safe if it is set the calling code assumes no
		// modifications of queries.
		//
		// is_admin is needed, otherwise category based post filters will show
		// both types and at the current release (4.7) it causes PHP errors.
		if ( is_admin() || ! is_category() || ! empty( $query->query_vars['suppress_filters'] ) ) {
			return;
		}

		// Check the current post types, maybe the category archive pages
		// are already associated with other post types.
		//
		// If `post_type` isn't set, WP assumes `post` by default.
		$post_types = $query->get( 'post_type' );
		$post_types = (array) ( $post_types ? $post_types : 'post' );

		// Add the entities post type only if the post post type is used in the query
		// since we only want `entities` to appear alongside posts.
		if ( in_array( 'post', $post_types, true ) ) {
			$post_types[] = $this->entity_post_type_service->get_post_type();
		}

		// Update the query post types.
		$query->set( 'post_type', $post_types );

	}

}
