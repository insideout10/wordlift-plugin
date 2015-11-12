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
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The Entity service.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * A singleton instance of the Entity service (useful for unit tests).
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Service $instance The singleton instance.
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
	 * @uses wl_core_get_related_entity_ids() to retrieve the entities referenced by the specified post.
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
			: $this->entity_service->get_all_related_to_last_50_published_posts() );

		// Add the post itself if it's an entity.
		if ( is_numeric( $post_id ) && $this->entity_service->is_entity( $post_id ) ) {
			$ids[] = $post_id;
		}

		// If there's no entities, return an empty array right away.
		if ( 0 === sizeof( $ids ) ) {
			$this->log_service->trace( "No events found [ post id :: $post_id ]" );

			return array();
		}

		$this->log_service->trace( "Getting events [ entity ids :: " . join( ', ', $ids ) . " ]" );

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
					'compare' => '!='
				),
				array(
					'key'     => Wordlift_Schema_Service::FIELD_DATE_END,
					'value'   => null,
					'compare' => '!='
				),
				array(
					'key'   => Wordlift_Schema_Service::FIELD_ENTITY_TYPE,
					'value' => Wordlift_Schema_Service::SCHEMA_EVENT_TYPE
				)
			)
		) );
	}

	/**
	 * Convert timeline events to JSON.
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

		// Model data from:
		// https://github.com/NUKnightLab/TimelineJS/blob/master/examples/example_json.json

		$timeline         = array();
		$timeline['type'] = 'default';

		// Prepare for the starting slide data. The starting slide will be the one where *now* is between *start/end* dates.
		$start_at_slide = 0;
		$event_index    = - 1;
		$now            = time();

		$timeline['date'] = array_map( function ( $post ) use ( &$timeline, &$event_index, &$start_at_slide, &$now ) {

			$start_date = strtotime( get_post_meta( $post->ID, Wordlift_Schema_Service::FIELD_DATE_START, true ) );
			$end_date   = strtotime( get_post_meta( $post->ID, Wordlift_Schema_Service::FIELD_DATE_END, true ) );

			// Set the starting slide.
			$event_index ++;
			if ( 0 === $start_at_slide && $now >= $start_date && $now <= $end_date ) {
				$start_at_slide = $event_index;
			}

			$date['startDate'] = date( 'Y,m,d', $start_date );
			$date['endDate']   = date( 'Y,m,d', $end_date );
			$date['headline']  = '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
			$date['text']      = strip_shortcodes( $post->post_content );

			// Load thumbnail
			if ( '' !== ( $thumbnail_id = get_post_thumbnail_id( $post->ID ) ) &&
			     false !== ( $attachment = wp_get_attachment_image_src( $thumbnail_id ) )
			) {

				$date['asset'] = array(
					'media' => $attachment[0]
				);

				// Add debug data.
				if ( WP_DEBUG ) {
					$date['debug'] = array(
						'post'        => $post,
						'thumbnailId' => $thumbnail_id,
						'attachment'  => $attachment
					);
				}
			}

			return $date;

		}, $posts );


		// The *timeline* library expects the data to be encapsulated in a *timeline* element, e.g.:
		//  {timeline: ...}
		return array(
			'timeline'     => $timeline,
			'startAtSlide' => $start_at_slide
		);
	}

}
