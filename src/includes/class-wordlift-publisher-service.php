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

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since
	 * @access private
	 * @var Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	protected function __construct() {
		// Set a reference to the logger.
		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Publisher_Service' );
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

	/**
	 * Get the publisher logo structure.
	 *
	 * The function returns false when the publisher logo cannot be determined, i.e.:
	 *  - the post has no featured image.
	 *  - the featured image has no file.
	 *  - a wp_image_editor instance cannot be instantiated on the original file or on the publisher logo file.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|false Returns an array with the `url`, `width` and `height` for the publisher logo or false in case
	 *  of errors.
	 * @since 3.19.2
	 * @see https://github.com/insideout10/wordlift-plugin/issues/823 related issue.
	 */
	// @@todo move to the module
	public function get_publisher_logo( $post_id ) {

		// Get the featured image for the post.
		$thumbnail_id = get_post_thumbnail_id( $post_id );

		// Bail out if thumbnail not available.
		if ( empty( $thumbnail_id ) || 0 === $thumbnail_id ) {
			$this->log->info( "Featured image not set for post $post_id." );

			return false;
		}

		// Get the uploads base URL.
		$uploads_dir = wp_upload_dir();

		// Get the attachment metadata.
		$metadata = wp_get_attachment_metadata( $thumbnail_id );

		// Bail out if the file isn't set.
		if ( ! isset( $metadata['file'] ) ) {
			$this->log->warn( "Featured image file not found for post $post_id." );

			return false;
		}

		// Retrieve the relative filename, e.g. "2018/05/logo_publisher.png"
		$path = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . $metadata['file'];

		// Use image src, if local file does not exist. @see https://github.com/insideout10/wordlift-plugin/issues/1149
		if ( ! file_exists( $path ) ) {
			$this->log->warn( "Featured image file $path doesn't exist for post $post_id." );

			$attachment_image_src = wp_get_attachment_image_src( $thumbnail_id, '' );
			if ( $attachment_image_src ) {
				return array(
					'url'    => $attachment_image_src[0],
					'width'  => $attachment_image_src[1],
					'height' => $attachment_image_src[2],
				);
			}

			// Bail out if we cant fetch wp_get_attachment_image_src
			return false;

		}

		// Try to get the image editor and bail out if the editor cannot be instantiated.
		$original_file_editor = wp_get_image_editor( $path );
		if ( is_wp_error( $original_file_editor ) ) {
			$this->log->warn( "Cannot instantiate WP Image Editor on file $path for post $post_id." );

			return false;
		}

		// Generate the publisher logo filename, we cannot use the `width` and `height` because we're scaling
		// and we don't actually know the end values.
		$publisher_logo_path = $original_file_editor->generate_filename( '-publisher-logo' );

		// If the file doesn't exist yet, create it.
		if ( ! file_exists( $publisher_logo_path ) ) {
			$original_file_editor->resize( 600, 60 );
			$original_file_editor->save( $publisher_logo_path );
		}

		// Try to get the image editor and bail out if the editor cannot be instantiated.
		$publisher_logo_editor = wp_get_image_editor( $publisher_logo_path );
		if ( is_wp_error( $publisher_logo_editor ) ) {
			$this->log->warn( "Cannot instantiate WP Image Editor on file $publisher_logo_path for post $post_id." );

			return false;
		}

		// Get the actual size.
		$size = $publisher_logo_editor->get_size();

		// Finally return the array with data.
		return array(
			'url'    => $uploads_dir['baseurl'] . substr( $publisher_logo_path, strlen( $uploads_dir['basedir'] ) ),
			'width'  => $size['width'],
			'height' => $size['height'],
		);
	}

	/**
	 * Utility function to check if the Publisher is set.
	 *
	 * @return true|false
	 *
	 * @since 3.53.0
	 */
	public function is_publisher_set() {
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

		// Check that the ID is set.
		if ( empty( $publisher_id ) ) {
			return false;
		}

		// Check that the ID points to a valid Post.
		$post = get_post( $publisher_id );
		if ( ! is_a( $post, '\WP_Post' ) ) {
			// Publisher not found.
			return false;
		}

		return true;
	}
}
