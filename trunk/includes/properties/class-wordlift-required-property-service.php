<?php
/**
 * Process required references returning a {@link Wordlift_Property_Entity_Reference}
 *
 * @since 3.29.1
 */
class Wordlift_Required_Property_Service extends Wordlift_Entity_Property_Service {

	/**
	 * {@inheritdoc}
	 */
	public function get( $post_id, $meta_key ) {

		return array_map( function ( $item ) {

			// If this is an entity reference, set that this entity is always required in SD output.
			if ( $item instanceof Wordlift_Property_Entity_Reference ) {
				$item->set_required( true );
			}

			return $item;
		}, parent::get( $post_id, $meta_key ) );
	}

}
