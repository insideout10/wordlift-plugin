<?php
/**
 * Storage: Post Author Storage.
 *
 * Provides access to {@link WP_Post} properties.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Author_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Author_Storage extends Wordlift_Post_Property_Storage {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_User_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_User_Service $user_service The {@link Wordlift_User_Service} instance.
	 */
	private $user_service;

	/**
	 * Create a {@link Wordlift_Post_Author_Storage} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_User_Service   $user_service   The {@link Wordlift_User_Service} instance.
	 */
	public function __construct( $entity_service, $user_service ) {
		parent::__construct( Wordlift_Post_Property_Storage::AUTHOR );

		$this->entity_service = $entity_service;
		$this->user_service   = $user_service;

	}

	/**
	 * Get the property value.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array|string|null A single string, or an array of values or null
	 *                           if the property isn't recognized.
	 */
	public function get( $post_id ) {
		$author_id = parent::get( $post_id );

		// Get the entity bound to this user.
		$entity_id = $this->user_service->get_entity( $author_id );

		// If there's no entity bound return a simple author structure.
		if ( empty( $entity_id ) ) {
			return $this->user_service->get_uri( $author_id );
		}

		// Return the entity URI.
		return $this->entity_service->get_uri( $entity_id );
	}

}
