<?php
/**
 * This file is part of the properties group of files which handle the location
 * property of entities.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Process references to locations either returning a {@link Wordlift_Property_Entity_Reference}
 * instance or a place name.
 *
 * @since 3.8.0
 */
class Wordlift_Location_Property_Service extends Wordlift_Entity_Property_Service {
//	/**
//	 * A {@link Wordlift_Entity_Service} instance.
//	 * @since  3.8.0
//	 * @access private
//	 * @var \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
//	 */
//	private $entity_service;
//
//	/**
//	 * Wordlift_Location_Property_Service constructor.
//	 *
//	 * @since 3.8.0
//	 *
//	 * @param \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
//	 */
//	public function __construct( $entity_service ) {
//
//		$this->entity_service = $entity_service;
//
//	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $post_id, $meta_key ) {

		return array_map( function ( $item ) {

			return $item instanceof Wordlift_Property_Entity_Reference
				? $item
				: array( '@type' => 'Place', 'name' => $item );
		}, parent::get( $post_id, $meta_key ) );
	}

}
