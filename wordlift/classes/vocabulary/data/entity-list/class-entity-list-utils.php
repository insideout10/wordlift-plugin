<?php

namespace Wordlift\Vocabulary\Data\Entity_List;

/**
 * This class helps to return the entities which are selected in the ui
 * by the user, this is used to provide the data for widget rendered
 * on the tag screen
 *
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_List_Utils {

	/**
	 * @param $term_id int Term id.
	 * @param $entities array An array of entities
	 *
	 * @return array<array> An Array of entities with isActive filter.
	 */
	public static function mark_is_active_for_entities( $term_id, $entities ) {

		$active_entities = self::get_active_entities( $term_id );

		if ( ! is_array( $entities ) ) {
			return $entities;
		}

		foreach ( $entities as &$entity ) {
			$entity_id          = $entity['entityId'];
			$entity['isActive'] = in_array( $entity_id, $active_entities, true );
		}

		return $entities;
	}

	/**
	 * @param $term_id
	 *
	 * @return array<string> An array of Entity URIs
	 */
	public static function get_active_entities( $term_id ) {

		// retrieve jsonld data.
		$entity = Entity_List_Factory::get_instance( $term_id );

		$entity_data_list = $entity->get_jsonld_data();

		$active_entity_ids = array();

		foreach ( $entity_data_list as $item ) {
			$sameas = $item['sameAs'];
			if ( is_array( $sameas ) && count( $sameas ) > 0 ) {
				// The entity id is stored on last position, so
				// we get the entity id from there.
				$active_entity_ids[] = array_pop( $sameas );
			}
		}

		return $active_entity_ids;
	}

}
