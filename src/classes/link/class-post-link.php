<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class is created to handle the post link.
 */

namespace Wordlift\Link;

use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift_Entity_Service;
use Wordlift_Schema_Service;

class Post_Link extends Default_Link {

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	public function __construct() {
		parent::__construct();
		$this->entity_service = Wordlift_Entity_Service::get_instance();
	}

	public function get_same_as_uris( $id ) {

		// It appears that some installs are receiving false here. Which means that an invalid $post_id has been
		// provided. Because we pass $single=false (the default) we're going to ignore return values that are not
		// arrays.
		$same_as = get_post_meta( $id, Wordlift_Schema_Service::FIELD_SAME_AS );
		if ( ! is_array( $same_as ) ) {
			$same_as = array();
		}

		return array_merge(
			array( $this->entity_service->get_uri( $id ) ),
			$same_as
		);

	}

	public function get_id( $uri ) {
		$content = Wordpress_Content_Service::get_instance()
											->get_by_entity_id_or_same_as( $uri );

		if ( ! isset( $content ) || ! is_a( $content->get_bag(), '\WP_Post' ) ) {
			return false;
		}

		return $content->get_bag()->ID;
	}

	public function get_synonyms( $id ) {
		// Get possible alternative entity_labels we can select from.
		$entity_labels = $this->entity_service->get_alternative_labels( $id );

		/*
		 * Since the original text might use an alternative entity_label than the
		 * Entity title, add the title itself which is not returned by the api.
		 */
		$entity_labels[] = get_the_title( $id );

		// Add some randomness to the entity_label selection.
		shuffle( $entity_labels );

		return $entity_labels;
	}

	public function get_permalink( $id ) {
		return get_permalink( $id );
	}

	public function get_edit_page_link( $id ) {
		return get_edit_post_link( $id, 'none' );
	}
}
