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


	public function get_relations_from_content( $content, $subject_type ) {
		if ( ! isset($_POST['wl_entities'])) {
			return array();
		}

		$selected_entities = (array) $_POST['wl_entities'];
		// Returns the list of entity ids.
		$entity_uris =  array_keys( $selected_entities );
		return $this->get_relations_from_entity_uris( $subject_type, $entity_uris );
	}
}