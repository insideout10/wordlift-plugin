<?php

/**
 * Provides functions and AJAX endpoints to support the Timeline widget.
 *
 * @since 3.1.0
 */
class Wordlift_Timeline_Service {

	/**
	 * The Log service.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The Entity service.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * The number of words to use for the excerpt, set in the `to_json` function
	 * and used by a filter.
	 *
	 * @since  3.7.0
	 * @access private
	 * @var int $excerpt_length The number of words to use for the excerpt.
	 */
	private $excerpt_length;

	/**
	 * A singleton instance of the Timeline service (useful for unit tests).
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Timeline_Service $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * Create a Wordlift_Timeline_Service instance.
	 *
	 * @since 3.1.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	public function __construct( $entity_service ) {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Timeline_Service' );

		$this->entity_service = $entity_service;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Wordlift_Timeline_Service
	 *
	 * @since 3.1.0
	 *
	 * @return \Wordlift_Timeline_Service The singleton instance of the Wordlift_Timeline_Service.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Retrieve timeline events and output them in JSON.
	 *
	 * @since 3.1.0
	 */
	public function ajax_timeline() {

		// Get the ID of the post who requested the timeline.
		$post_id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : null );

		// Get the events and transform them for the JSON response, then send them to the client.
		wp_send_json( $this->to_json( $this->get_events( $post_id ) ) );

	}

	/**
	 * Retrieve timeline events.
	 *
	 * @since 3.1.0
	 *
	 * @uses  wl_core_get_related_entity_ids() to retrieve the entities referenced by the specified post.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array An array of event posts.
	 */
	public function get_events( $post_id = null ) {

		// Get the entity IDs either from the entities related to the specified post or from the last 50 published
		// posts if no post has been specified.
		$ids = ( is_numeric( $post_id )
			? wl_core_get_related_entity_ids( $post_id )
			: $this->get_all_related_to_last_50_published_posts() );

		// Add the post itself if it's an entity.
		if ( is_numeric( $post_id ) && $this->entity_service->is_entity( $post_id ) ) {
			$ids[] = $post_id;
		}

		// If there's no entities, return an empty array right away.
		if ( 0 === sizeof( $ids ) ) {
			$this->log_service->trace( "No events found [ post id :: $post_id ]" );

			return array();
		}

		$this->log_service->trace( 'Getting events [ entity ids :: ' . join( ', ', $ids ) . ' ]' );

		return get_posts( array(
			'post__in'       => $ids,
			'post_type'      => Wordlift_Entity_Service::TYPE_NAME,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => Wordlift_Schema_Service::FIELD_DATE_START,
					'value'   => null,
					'compare' => '!=',
				),
				array(
					'key'     => Wordlift_Schema_Service::FIELD_DATE_END,
					'value'   => null,
					'compare' => '!=',
				),
			),
			'tax_query'      => array(
				'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
				'field'    => 'slug',
				'terms'    => 'event',
			),
		) );
	}

	/**
	 * Convert timeline events to JSON. This function sets the global post in order
	 * to get an automatic excerpt. Since we're being called inside an AJAX request,
	 * we're not taking care of restoring any previous post: there isn't any.
	 *
	 * @since 3.1.0
	 *
	 * @param array $posts An array of posts.
	 *
	 * @return array|string An array of timeline events or an empty string if no posts are provided.
	 */
	public function to_json( $posts ) {

		// If there are no events, return empty JSON
		if ( empty( $posts ) || is_null( $posts ) ) {
			return '';
		}

		// {media|thumbnail}: if set to 'media' the image is attached to the slide, if set to 'background' the image is set as background.
		$display_images_as = isset( $_REQUEST['display_images_as'] ) ? $_REQUEST['display_images_as'] : 'media';

		// The number of words for the excerpt (by default 55, as WordPress).
		$this->excerpt_length = $excerpt_length = isset( $_REQUEST['excerpt_length'] ) && is_numeric( $_REQUEST['excerpt_length'] ) ? $_REQUEST['excerpt_length'] : 55;
		add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );

		// Add a filter to remove the [...] after excerpts, since we're adding
		// a link to the post itself.
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );

		// Prepare for the starting slide data. The starting slide will be the one where *now* is between *start/end* dates.
		$start_at_slide = 0;
		$event_index    = - 1;
		$now            = time();

		// Prepare the timeline variable.
		$timeline = array();

		// Populate the arrays.
		$timeline['events'] = array_map( function ( $item ) use ( &$timeline, &$event_index, &$start_at_slide, &$now, $display_images_as, $excerpt_length ) {

			// Get the start and end dates.
			$start_date = strtotime( get_post_meta( $item->ID, Wordlift_Schema_Service::FIELD_DATE_START, true ) );
			$end_date   = strtotime( get_post_meta( $item->ID, Wordlift_Schema_Service::FIELD_DATE_END, true ) );

			// Set the starting slide.
			$event_index ++;
			if ( 0 === $start_at_slide && $now >= $start_date && $now <= $end_date ) {
				$start_at_slide = $event_index;
			}

			// Load thumbnail
			if ( '' !== ( $thumbnail_id = get_post_thumbnail_id( $item->ID ) )
			     && false !== ( $attachment = wp_get_attachment_image_src( $thumbnail_id ) )
			) {

				// Set the thumbnail URL.
				if ( 'background' === $display_images_as ) {
					$date['background'] = array( 'url' => $attachment[0] );
					$date['media']      = array( 'thumbnail' => $attachment[0] );
				} else {
					$date['media'] = array(
						'url'       => $attachment[0],
						'thumbnail' => $attachment[0],
					);
				}
			}

			// Set the start/end dates by converting them to TimelineJS required format.
			$date['start_date'] = Wordlift_Timeline_Service::date( $start_date );
			$date['end_date']   = Wordlift_Timeline_Service::date( $end_date );

			setup_postdata( $GLOBALS['post'] = $item );

			$more_link_text = sprintf(
				'<span aria-label="%1$s">%2$s</span>',
				sprintf(
				/* translators: %s: Name of current post */
					__( 'Continue reading %s' ),
					the_title_attribute( array( 'echo' => false ) )
				),
				__( '(more&hellip;)' )
			);

			// Set the event text only with the headline (see https://github.com/insideout10/wordlift-plugin/issues/352).
			$date['text'] = array(
				'headline' => '<a href="' . get_permalink( $item->ID ) . '">' . $item->post_title . '</a>',
			);

			// If we have an excerpt, set it.
			if ( 0 < $excerpt_length ) {
				$date['text']['text'] = sprintf( '%s <a href="%s">%s</a>', get_the_excerpt(), get_permalink(), $more_link_text );
			}

			return $date;

		}, $posts );

		// Finally remove the excerpt filter.
		remove_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );

		// The JSON format is defined here: https://timeline.knightlab.com/docs/json-format.html
		return array(
			'timeline'       => $timeline,
			'start_at_slide' => $start_at_slide,
		);
	}

	/**
	 * This function filters {@link excerpt_more} by removing it, since we're
	 * adding the 'read more' link. This filter is set by {@see to_json}.
	 *
	 * @since 3.7.0
	 *
	 * @param string $excerpt_more The excerpt more preset.
	 *
	 * @return string An empty string.
	 */
	public function excerpt_more( $excerpt_more ) {

		return '';
	}

	/**
	 * A filter for the excerpt length, set by the `to_json` function, to tailor
	 * how many words to return according to the client setting.
	 *
	 * @since 3.7.0
	 *
	 * @param int $length The preset number of words.
	 *
	 * @return int The number of words for the preset.
	 */
	public function excerpt_length( $length ) {

		return $this->excerpt_length;
	}


	/**
	 * Convert the date to a date array.
	 *
	 * @since 3.7.0
	 *
	 * @param $value int A date value.
	 *
	 * @return array An array containing year, month and day values.
	 */
	public static function date( $value ) {

		return array(
			'year'  => (int) date( 'Y', $value ),
			'month' => (int) date( 'm', $value ),
			'day'   => (int) date( 'd', $value ),

		);
	}

	/**
	 * Get the entities related to the last 50 posts published on this blog (we're keeping a long function name due to
	 * its specific function).
	 *
	 * @since 3.1.0
	 *
	 * @return array An array of post IDs.
	 */
	public function get_all_related_to_last_50_published_posts() {

		// Global timeline. Get entities from the latest posts.
		$latest_posts_ids = get_posts( array(
			'numberposts' => 50,
			'fields'      => 'ids', //only get post IDs
			'post_type'   => array( 'post', 'page' ),
			'post_status' => 'publish',
		) );

		if ( empty( $latest_posts_ids ) ) {
			// There are no posts.
			return array();
		}

		// Collect entities related to latest posts
		$entity_ids = array();
		foreach ( $latest_posts_ids as $id ) {
			$entity_ids = array_merge( $entity_ids, wl_core_get_related_entity_ids( $id, array(
				'status' => 'publish',
			) ) );
		}

		return $entity_ids;
	}

}
