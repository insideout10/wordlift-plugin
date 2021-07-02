<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class is created to handle the post link.
 */

namespace Wordlift\Link;

use Wordlift\Common\Singleton;
use Wordlift_Entity_Service;
use Wordlift_Schema_Service;

class Post_Link extends Singleton implements Link  {

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;


	public function __construct(  ) {
		parent::__construct();
		$this->entity_service = Wordlift_Entity_Service::get_instance();
	}


	function get_link_title( $id, $label_to_be_ignored ) {

		// Get possible alternative entity_labels we can select from.
		$entity_labels = $this->entity_service->get_alternative_labels( $id );

		/*
		 * Since the original text might use an alternative entity_label than the
		 * Entity title, add the title itself which is not returned by the api.
		 */
		$entity_labels[] = get_the_title( $id );

		// Add some randomness to the entity_label selection.
		shuffle( $entity_labels );

		// Select the first entity_label which is not to be ignored.
		$title = '';
		foreach ( $entity_labels as $entity_label ) {
			if ( 0 !== strcasecmp( $entity_label, $label_to_be_ignored ) ) {
				$title = $entity_label;
				break;
			}
		}

		return $title;
	}


	public function get_same_as_uris( $id ) {

		return array_merge(
			(array) $this->entity_service->get_uri( $id ),
			get_post_meta( $id, Wordlift_Schema_Service::FIELD_SAME_AS )
		);

	}
}
