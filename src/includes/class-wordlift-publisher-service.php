<?php
/**
 * Services: Publisher Service.
 *
 * The Publisher service provides functions to list potential publishers.
 *
 * @since   3.11.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Publisher_Service} class.
 *
 * @since   3.11.0
 * @package Wordlift
 */
class Wordlift_Publisher_Service {

	/**
	 * Counts the number of potential publishers.
	 *
	 * @since 3.11.0
	 *
	 * @return int The number of potential publishers.
	 */
	public function count() {

		/*
		 * Search for entities which have a thumbnail, and are either a Person
		 * or Organization.
		 * Get only the ids as all we need is the count.
		 */
		$entities = get_posts( array(
			'post_type' 	=> Wordlift_Entity_Service::valid_entity_post_types(),
			'post_status'	=> 'publish',
			'tax_query'		=> array(
				array(
			        'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			        'field'    => 'slug',
			        'terms'    => array( 'organization', 'person' ),
					'operator' => 'IN',
				),
		    ),
			'meta_query'	=> array(
				array(
					'key' 	  => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
			'fields'		=> 'ids',
		) );

		// Finally return the count.
		return count( $entities );
	}

	/**
	 * Search SQL filter for matching against post title only.
	 *
	 * @link    http://wordpress.stackexchange.com/a/11826/1685
	 *
	 * @since 3.15.0
	 *
	 * @param   string      $search
	 * @param   WP_Query    $wp_query
	 */
	public function limit_search_to_title( $search, $wp_query ) {
	    if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
	        global $wpdb;

	        $q = $wp_query->query_vars;
	        $n = ! empty( $q['exact'] ) ? '' : '%';
	        $search = array();
	        foreach ( (array) $q['search_terms'] as $term ) {
	            $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
			}

	        if ( ! is_user_logged_in() ) {
	            $search[] = "$wpdb->posts.post_password = ''";
			}

	        $search = ' AND ' . implode( ' AND ', $search );
	    }

	    return $search;
	}

	/**
	 * Query WP for potential publishers, i.e. {@link WP_Post}s which are associated`
	 * with `wl_entity_type` (taxonomy) terms of `Organization` or `Person`.
	 *
	 * @since 3.11.0
	 *
	 * @param string $filter The title filter.
	 *
	 * @return array An array of results in a select2 friendly format.
	 */
	public function query( $filter = '' ) {

		// Search for the filter in the titles only.
		add_filter( 'posts_search', array( $this, 'limit_search_to_title' ), 10, 2 );

		/*
		 * Search for entities which have a thumbnail, and are either a Person
		 * or Organization. Sort the results by title.
		 */
		$entities = get_posts( array(
			'post_type' 	=> Wordlift_Entity_Service::valid_entity_post_types(),
			'post_status'	=> 'publish',
			'tax_query'		=> array(
				array(
			        'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			        'field'    => 'slug',
			        'terms'    => array( 'organization', 'person' ),
					'operator' => 'IN',
				),
		    ),
			'meta_query'	=> array(
				array(
					'key' 	  => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
			's'			   => $filter,
			'orderby'	   => 'title',
		) );

		// Remove the search filter added before the query.
		remove_filter( 'posts_search', array( $this, 'limit_search_to_title' ), 10, 2 );

		// Set a reference to ourselves to pass to the closure.
		$publisher_service = $this;

		// Map the results in a `Select2` compatible array.
		return array_map( function ( $entity ) use ( $publisher_service ) {
			$type = wp_get_post_terms( $entity->ID, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
			$thumb_id = get_post_thumbnail_id( $entity->ID );

			return array(
				'id'            => $entity->ID,
				'text'          => $entity->post_title,
				'type'          => $type[0]->name,
				'thumbnail_url' => $publisher_service->get_attachment_image_url( $thumb_id ),
			);
		}, $entities );
	}

	/**
	 * Get the thumbnail's URL.
	 *
	 * @since 3.11.0
	 *
	 * @param int    $attachment_id The attachment id.
	 * @param string $size          The attachment size (default = 'thumbnail').
	 *
	 * @return string|bool The image URL or false if not found.
	 */
	public function get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {

		$image = wp_get_attachment_image_src( $attachment_id, $size );

		return isset( $image['0'] ) ? $image['0'] : false;
	}

}
