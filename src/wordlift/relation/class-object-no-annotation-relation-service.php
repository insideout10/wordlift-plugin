<?php
/**
 * This class is created to provide object relation service when there is no annotation.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation;

class Object_No_Annotation_Relation_Service extends Object_Relation_Service {


	public function get_relations_from_content( $content, $subject_type, $local_entity_uris ) {
		if ( !  $local_entity_uris ) {
			return array();
		}

		return $this->get_relations_from_entity_uris( $subject_type, $local_entity_uris );
	}
}