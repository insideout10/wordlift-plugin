<?php

/**
 * Process references to other entities, local or remote, by returning a
 * {@link Wordlift_Property_Entity_Reference} with the URL of the referenced entity.
 *
 * @since 3.8.0
 */
class Wordlift_Entity_Property_Service extends Wordlift_Simple_Property_Service {

	/**
	 * {@inheritdoc}
	 */
	public function get( $id, $meta_key, $type ) {

		$entity_service = Wordlift_Entity_Service::get_instance();

		// Map each returned value to a Wordlift_Property_Entity_Reference.
		return array_map(
			function ( $item ) use ( $entity_service ) {

				// If the $item is a number and it's an existing post, return the
				// URI of the referenced entity. Otherwise return the value.
				return is_numeric( $item ) && null !== get_post( $item )
				? new Wordlift_Property_Entity_Reference( $entity_service->get_uri( $item ), (int) $item )
				: $item;
			},
			parent::get( $id, $meta_key, $type )
		);
	}

}
