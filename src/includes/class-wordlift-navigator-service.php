<?php
/**
 * Services: Navigator Service.
 *
 * The Navigator Service provides data for the Navigator Widget.
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Navigator_Service} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Navigator_Service {

	/**
	 * The Ws relations.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var array An array of Ws names.
	 */
	private static $relations = array(
		WL_WHEN_RELATION,
		WL_WHERE_RELATION,
		WL_WHAT_RELATION,
		WL_WHO_RELATION,
	);

	/**
	 * The navigator thumbnail image size.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var array An array of `width` and `height` properties.
	 */
	private static $image_size = array(
		'width'  => 180,
		'height' => 120,
	);

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * Create a {@link Wordlift_Navigator_Service} instance.
	 *
	 * @since 3.12.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	function __construct( $entity_service ) {

		$this->entity_service = $entity_service;

	}

	/**
	 * Get data related to the {@link WP_Post} with the specified id.
	 *
	 * @since 3.12.0
	 *
	 * @param int $post_id The {@link WP_Post} id.
	 *
	 * @return array An array of related posts/entities.
	 */
	function get( $post_id ) {

		// Bail out if a post doesn't exist.
		if ( null === get_post( $post_id ) ) {
			return array();
		}

		// Get the related entities, ordering them by WHO, WHAT, WHERE, WHEN
		// TODO Replace with a single query if it is possible
		// We select in inverse order to give priority to less used entities
		$entities = array_reduce( self::$relations, function ( $carry, $item ) use ( $post_id ) {

			// Get the related entities.
			$entities = wl_core_get_related_entities( $post_id, array(
				'predicate' => $item,
				'status'    => 'publish',
			) );

			// Merge the results with the previous results.
			return array_merge( $carry, $entities );
		}, array() );

		// Prepare structures to memorize other related posts.
		$results = array();

		// Cycle through all the referenced entities.
		foreach ( $entities as $entity ) {

			// Take the id of posts referencing the entity.
			$posts = wl_core_get_related_posts( $entity->ID, array(
				'status' => 'publish',
			) );

			// Loop over them and take the first one which is not already in the $related_posts.
			foreach ( $posts as $post ) {

				// Don't consider the source post, we don't link to ourselves.
				if ( $post_id === (int) $post->ID ) {
					continue;
				}

				// Don't consider a post which has already been set in the results.
				if ( isset( $results[ $post->ID ] ) ) {
					continue;
				}

				// Get the image.
				$attachment_id = get_post_thumbnail_id( $post->ID, 'thumbnail' );
				$thumbnail_url = image_downsize( $attachment_id, self::$image_size );

				// $thumbnail = wp_get_attachment_url( $attachment_id );

				// Continue to the next item if we can't get an image for this one.
				if ( false === $thumbnail_url || ! is_array( $thumbnail_url ) ) {
					continue;
				}

				// Prepare the results.
				$results[ $post->ID ] = array(
					'post'   => array(
						'permalink' => get_post_permalink( $post->ID ),
						'title'     => $post->post_title,
						'thumbnail' => $thumbnail_url[0],
						'excerpt'   => Wordlift_Post_Excerpt_Helper::get_excerpt( $post ),
					),
					'entity' => array(
						'label'     => $entity->post_title,
						'relation'  => $this->entity_service->get_classification_scope_for( $entity->ID ),
						'permalink' => get_post_permalink( $entity->ID ),
					),
				);

				// Be sure no more than 1 post for entity is returned
				break;

			}
		}

		// Return first 4 results in json accordingly to 4 columns layout
		return array_slice( array_reverse( array_values( $results ) ), 0, 4 );
	}

}
