<?php
/**
 * Factories: Storage Factory.
 *
 * A factory which creates {@link Wordlift_Storage} instances.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Storage_Factory} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Storage_Factory {

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
	 * Create a {@link Wordlift_Storage_Factory} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_User_Service   $user_service   The {@link Wordlift_User_Service} instance.
	 */
	public function __construct( $entity_service, $user_service ) {

		$this->entity_service = $entity_service;
		$this->user_service   = $user_service;

	}

	/**
	 * Get a {@link Wordlift_Post_Property_Storage} to read {@link WP_Post}s'
	 * titles.
	 *
	 * @since 3.15.0
	 *
	 * @return \Wordlift_Post_Property_Storage A {@link Wordlift_Post_Property_Storage}
	 *                                         instance.
	 */
	public function post_title() {

		return new Wordlift_Post_Property_Storage( Wordlift_Post_Property_Storage::TITLE );
	}

	/**
	 * Get a {@link Wordlift_Post_Property_Storage} to read {@link WP_Post}s'
	 * descriptions stripped of tags and shortcodes.
	 *
	 * @since 3.15.0
	 *
	 * @return \Wordlift_Post_Property_Storage A {@link Wordlift_Post_Property_Storage}
	 *                                         instance.
	 */
	public function post_description_no_tags_no_shortcodes() {

		return new Wordlift_Post_Property_Storage( Wordlift_Post_Property_Storage::DESCRIPTION_NO_TAGS_NO_SHORTCODES );
	}

	/**
	 * Get a {@link Wordlift_Post_Property_Storage} to read {@link WP_Post}s'
	 * authors.
	 *
	 * @since 3.15.0
	 *
	 * @return \Wordlift_Post_Property_Storage A {@link Wordlift_Post_Property_Storage}
	 *                                         instance.
	 */
	public function post_author() {

		return new Wordlift_Post_Property_Storage( Wordlift_Post_Property_Storage::AUTHOR );
	}

	/**
	 * Get a {@link Wordlift_Post_Property_Storage} to read {@link WP_Post}s'
	 * metas.
	 *
	 * @since 3.15.0
	 *
	 * @param string $meta_key The meta key to read.
	 *
	 * @return Wordlift_Post_Meta_Storage A {@link Wordlift_Post_Meta_Storage}
	 *                                    instance.
	 */
	public function post_meta( $meta_key ) {

		return new Wordlift_Post_Meta_Storage( $meta_key );
	}

	/**
	 * Get a {@link Wordlift_Post_Schema_Class_Storage} to read {@link WP_Post}s'
	 * entity type class.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service}
	 *                                                 instance.
	 *
	 * @return Wordlift_Post_Schema_Class_Storage A {@link Wordlift_Post_Schema_Class_Storage}
	 *                                    instance.
	 */
	public function schema_class( $schema_service ) {

		return new Wordlift_Post_Schema_Class_Storage( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, $schema_service );

	}


	public function author_uri() {

		return new Wordlift_Post_Author_Storage( $this->entity_service, $this->user_service );
	}

	public function post_meta_to_uri( $meta_key ) {

		return new Wordlift_Post_Meta_Uri_Storage( $meta_key, $this->entity_service );
	}

	public function post_images() {

		return new Wordlift_Post_Image_Storage();
	}

	public function relations() {

		return new Wordlift_Post_Related_Storage( $this->entity_service );
	}

}
