<?php
/**
 * This class is created to provide object relation service when there is no annotation.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */
namespace Wordlift\Relation;

class Object_No_Annotation_Relation_Service extends Object_Relation_Service {

	public function get_relations_from_content( $content, $subject_type) {
		if ( ! isset($_POST['wl_entities'])) {
			return array();
		}

		$selected_entities = (array) $_POST['wl_entities'];

		// Returns the list of entity ids.
		return array_keys( $selected_entities );
	}


}