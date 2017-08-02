<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:10
 */

class Wordlift_Post_Meta_Uri_Storage extends Wordlift_Post_Meta_Storage {

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * @param string                   $meta_key
	 * @param \Wordlift_Entity_Service $entity_service
	 */
	public function __construct( $meta_key, $entity_service ) {
		parent::__construct( $meta_key );


		$this->entity_service = $entity_service;
	}

	public function get( $post_id ) {
		$values = parent::get( $post_id );

		$entity_service = $this->entity_service;

		return array_map( function ( $item ) use ( $entity_service ) {
			return $this->entity_service->get_uri( $item );
		}, $values );
	}

}
