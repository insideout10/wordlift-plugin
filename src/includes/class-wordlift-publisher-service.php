<?php
/**
 * Services: Publisher Service.
 *
 * The Publisher service provides functions to list potential publishers.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Publisher_Service} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Publisher_Service {

	protected function __construct() {

	}

	private static $instance = null;

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Counts the number of potential publishers.
	 *
	 * @return int The number of potential publishers.
	 * @since 3.11.0
	 */
	public function count() {

		// Search for entities which are either a Person
		// or Organization.

		// Get only the ids as all we need is the count.
		$entities = get_posts(
			array(
				'post_type'      => Wordlift_Entity_Service::valid_entity_post_types(),
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => array(
					array(
						'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
						'field'    => 'slug',
						'terms'    => array( 'organization', 'person' ),
					),
				),
				'fields'         => 'ids',
			)
		);

		// Finally return the count.
		return count( $entities );
	}

	/**
	 * Search SQL filter for matching against post title only.
	 *
	 * @link    http://wordpress.stackexchange.com/a/11826/1685
	 *
	 * @since   3.15.0
	 *
	 * @param string   $search The search string.
	 * @param WP_Query $wp_query The {@link WP_Query} instance.
	 *
	 * @return array|string An array of results.
	 */
	public function limit_search_to_title( $search, $wp_query ) {

		// Bail out if the search or the `search_terms` haven't been set.
		if ( empty( $search ) || empty( $wp_query->query_vars['search_terms'] ) ) {
			return $search;
		}

		global $wpdb;

		$query_vars = $wp_query->query_vars;
		$percent    = ! empty( $query_vars['exact'] ) ? '' : '%';
		$search     = array();

		foreach ( (array) $query_vars['search_terms'] as $term ) {
			$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $percent . $wpdb->esc_like( $term ) . $percent );
		}

		if ( ! is_user_logged_in() ) {
			$search[] = "$wpdb->posts.post_password = ''";
		}

		$search = ' AND ' . implode( ' AND ', $search );

		return $search;
	}

	/**
	 * Query WP for potential publishers, i.e. {@link WP_Post}s which are associated`
	 * with `wl_entity_type` (taxonomy) terms of `Organization` or `Person`.
	 *
	 * @param string $filter The title filter.
	 *
	 * @return array An array of results in a select2 friendly format.
	 * @since 3.11.0
	 */
	public function query( $filter = '' ) {

		// Search for the filter in the titles only.
		add_filter(
			'posts_search',
			array(
				$this,
				'limit_search_to_title',
			),
			10,
			2
		);

		/*
		 * Search for entities which are either a Person
		 * or Organization. Sort the results by title in ascending order.
		 */
		$entities = get_posts(
			array(
				'post_type'      => Wordlift_Entity_Service::valid_entity_post_types(),
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => array(
					array(
						'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
						'field'    => 'slug',
						'terms'    => array( 'organization', 'person' ),
					),
				),
				's'              => $filter,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		// Remove the search filter added before the query.
		remove_filter(
			'posts_search',
			array(
				$this,
				'limit_search_to_title',
			),
			10,
			2
		);

		// Set a reference to ourselves to pass to the closure.
		$publisher_service = $this;

		// Map the results in a `Select2` compatible array.
		return array_map(
			function ( $entity ) use ( $publisher_service ) {
				$type     = wp_get_post_terms( $entity->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
				$thumb_id = get_post_thumbnail_id( $entity->ID );

				return array(
					'id'            => $entity->ID,
					'text'          => $entity->post_title,
					'type'          => $type[0]->name,
					'thumbnail_url' => $publisher_service->get_attachment_image_url( $thumb_id ),
				);
			},
			$entities
		);
	}

	/**
	 * Get the thumbnail's URL.
	 *
	 * @param int    $attachment_id The attachment id.
	 * @param string $size The attachment size (default = 'thumbnail').
	 *
	 * @return string|bool The image URL or false if not found.
	 * @since 3.11.0
	 */
	public function get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {

		$image = wp_get_attachment_image_src( $attachment_id, $size );

		return isset( $image['0'] ) ? $image['0'] : false;
	}

	/**
	 * Add additional instructions to featured image metabox
	 * when the entity type is the publisher.
	 *
	 * @param string $content Current metabox content.
	 *
	 * @return string $content metabox content with additional instructions.
	 * @since  3.19.0
	 */
	public function add_featured_image_instruction( $content ) {
		// Get the current post ID.
		$post_id = get_the_ID();

		// Get the publisher id.
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

		// Bail if for some reason the post id is not set.
		if (
			empty( $post_id ) ||
			$post_id !== (int) $publisher_id
		) {
			return $content;
		}

		$terms = wp_get_post_terms(
			$post_id, // The post id.
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, // The taxonomy slug.
			array(
				'fields' => 'slugs',
				// We don't need all fields, but only slugs.
			)
		);

		// Check that the entity type is "Organization".
		if ( in_array( 'organization', $terms, true ) ) {
			// Add the featured image description when the type is "Organization".

			$link     = sprintf(
				'<a target="_blank" href="%s">%s</a>',
				esc_attr__( 'https://developers.google.com/search/docs/data-types/article#logo-guidelines', 'wordlift' ),
				esc_html__( 'AMP logo guidelines', 'wordlift' )
			);
			$content .= sprintf(
				'<p>'
								/* translators: %s: AMP logo guidelines. */
								 . esc_html_x( 'According to the %s, the logo should fit in a 60x600px rectangle, and either be exactly 60px high (preferred), or exactly 600px wide. For example, 450x45px would not be acceptable, even though it fits in the 600x60px rectangle. To comply with the guidelines, WordLift will automatically resize the Featured Image for structured data formats.', 'After "According to the" goes the link to the "AMP logo guidelines".', 'wordlift' )
								 . '</p>',
				$link
			);
		}

		// Finally return the content.
		return $content;
	}

}
