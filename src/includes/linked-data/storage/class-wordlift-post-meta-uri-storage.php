<?php
/**
 * Storage: Post Meta URI Storage.
 *
 * A {@link Wordlift_Storage} class which loads URI data from the post metas.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Meta_Uri_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Meta_Uri_Storage extends Wordlift_Post_Meta_Storage {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * Create a {@link Wordlift_Post_Meta_Uri_Storage} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param string                   $meta_key       The meta key to read data from.
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	public function __construct( $meta_key, $entity_service ) {
		parent::__construct( $meta_key );

		$this->entity_service = $entity_service;

	}

	/**
	 * Get the value for the specified meta key.
	 *
	 * The value is expected to be an entity post, for which the URI is loaded
	 * and returned.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of URIs (or an empty array if nothing is set).
	 */
	public function get( $post_id ) {
		$values = parent::get( $post_id );

		$entity_service = $this->entity_service;

		return array_map(
			function ( $item ) use ( $entity_service ) {
				return $entity_service->get_uri( $item );
			},
			$values
		);
	}

}
