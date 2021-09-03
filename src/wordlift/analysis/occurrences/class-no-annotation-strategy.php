<?php
/**
 * This file provides a default strategy to add the occurences in analysis service.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */

namespace Wordlift\Analysis\Occurrences;

use Wordlift\Common\Singleton;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Factory;
use Wordlift\Relation\Object_Relation_Service;

class No_Annotation_Strategy extends Singleton implements Occurrences {
	/**
	 * @return No_Annotation_Strategy
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	public function add_occurences_to_entities( $occurrences, $json, $post_id ) {


		$references = Object_Relation_Factory::get_instance( $post_id )->get_references(
			$post_id,
			Object_Type_Enum::POST
		);

		$entity_service = \Wordlift_Entity_Service::get_instance();

		foreach ( $references as $reference ) {
			$entity_uri                    = $entity_service->get_uri( $reference->get_id(), $reference->get_type() );
			$entity_data                   = wl_serialize_entity( $reference->get_id() );
			$entity_data['occurrences']    = array( 'placeholder-occurrence' );
			$json->entities->{$entity_uri} = $entity_data;
		}

		return $json;
	}
}