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

	public function add_occurences_to_entities( $occurences, $json, $post_id ) {


		$relations = Object_Relation_Service::get_instance()->get_references(
			$post_id,
			Object_Type_Enum::POST
		);



		foreach ( $json->entities as $entity_id => $entity ) {

			$json->entities->{$entity_id}->occurrences = $this->get_occurences( $entity_id );

			foreach ( $json->entities->{$entity_id}->occurrences as $annotation_id ) {
				$json->entities->{$entity_id}->annotations[ $annotation_id ] = array(
					'id' => $annotation_id,
				);
			}
		}

		return $json;
	}

	private function get_occurences( $entity_id ) {
		return array(1);
	}
}