<?php
/**
 * This file provides a default strategy to add the occurences in analysis service.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */
namespace Wordlift\Analysis\Occurrences;

use Wordlift\Common\Singleton;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;

class No_Annotation_Strategy extends Singleton implements Occurrences {
	/**
	 * @return No_Annotation_Strategy
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	public function add_occurences_to_entities( $occurrences, $json, $post_id ) {


		 $references = Object_Relation_Service::get_instance()->get_references(
			$post_id,
			Object_Type_Enum::POST
		);

		 $entity_service = \Wordlift_Entity_Service::get_instance();

		$linked_entity_uris = array_map( function ( $reference ) use ( $entity_service ) {
			return $entity_service->get_uri( $reference->get_id(), $reference->get_type() );
		}, $references );



		foreach ( $json->entities as $entity_id => $entity ) {

			$json->entities->{$entity_id}->occurrences = $this->get_occurences( $entity_id, $linked_entity_uris );

			foreach ( $json->entities->{$entity_id}->occurrences as $annotation_id ) {
				$json->entities->{$entity_id}->annotations[ $annotation_id ] = array(
					'id' => $annotation_id,
				);
			}
		}

		return $json;
	}

	private function get_occurences( $entity_id, $linked_entity_uris ) {
		if ( in_array( $entity_id, $linked_entity_uris ) ) {
			// we need a single occurrence in order to notify the user the entity is selected.
			return array('placeholder-occurrence');
		}
		return array();
	}
}