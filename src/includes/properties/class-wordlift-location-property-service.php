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

	/**
	 * {@inheritdoc}
	 */
	public function get( $id, $meta_key, $type ) {

		return array_map(
			function ( $item ) {

				// If this is an entity reference, set that this entity is always required in SD output.
				if ( $item instanceof Wordlift_Property_Entity_Reference ) {
					  $item->set_required( true );

					  return $item;
				}

				return array(
					'@type' => 'Place',
					'name'  => $item,
				);
			},
			parent::get( $id, $meta_key, $type )
		);
	}

}
