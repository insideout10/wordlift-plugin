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
	function __construct( $entity_post_type_service ) {

		$this->entity_post_type_service = $entity_post_type_service;

	}

	/**
	 * Experimental function to set the entity post types as one to be included
	 * in archive pages.
	 *
	 * @since 3.11.0
	 *
	 * @param WP_Query $query WP's {@link WP_Query} instance.
	 *
	 * @return WP_Query The updated {@link WP_Query}.
	 */
	public function pre_get_posts( $query ) {

		// Only for the main query, avoid problems with widgets and what not.
		if ( ! $query->is_main_query() ) {
			return $query;
		}

		// Unlikely for `suppress_filter` to be set on the front end, but let's
		// be safe if it is set the calling code assumes no modifications of queries.
		// is_admin is needed, otherwise category based post filters will show
		// both types and at the current release (4.7) it causes PHP errors.
		if ( is_admin() || ! is_category() || ! empty( $query->query_vars['suppress_filters'] ) ) {
			return $query;
		}

		// Check the current post types, maybe the category archive pages
		// are already associated with other post types.
		$post_types = (array) ( $query->get( 'post_type' ) ?: 'post' );

		// Add the entities post type only if the post post type is used in the query
		if ( in_array( 'post', $post_types ) ) {
			$post_types[] = $this->entity_post_type_service->get_post_type();
		}

		// Update the query post types.
		$query->set( 'post_type', $post_types );

		// Finally return the query.
		return $query;
	}

}
