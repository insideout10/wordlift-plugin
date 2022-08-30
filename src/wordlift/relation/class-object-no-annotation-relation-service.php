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

use Wordlift\Jsonld\Post_Reference;
use Wordlift\Object_Type_Enum;

class Object_No_Annotation_Relation_Service extends Object_Relation_Service {

	public function get_relations_from_content( $content, $subject_type, $local_entity_uris ) {
		if ( ! $local_entity_uris ) {
			return array();
		}

		return $this->get_relations_from_entity_uris( $subject_type, $local_entity_uris );
	}

	public function get_references( $subject_id, $subject_type ) {
		/**
		 * Object_Relation_Service encapsulates Post_Relation_Service which returns only the entities which are
		 * not article, in case of no annotation we need to add them to analysis response, so wee need to get those entities.
		 */
		$references_without_articles = parent::get_references( $subject_id, $subject_type );
		$references_with_articles    = $this->get_all_entities_references( $subject_id, $subject_type );

		// merge, filter and array_unique the references.
		return array_unique( array_filter( array_merge( $references_with_articles, $references_without_articles ) ) );
	}

	public function get_all_entities_references( $subject_id, $subject_type ) {
		global $wpdb;
		$post_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT object_id FROM {$wpdb->prefix}wl_relation_instances WHERE subject_id = %d AND object_type = %d AND subject_type = %d",
				$subject_id,
				Object_Type_Enum::POST,
				$subject_type
			)
		);

		return array_map(
			function ( $term_id ) {
				return new Post_Reference( $term_id );
			},
			$post_ids
		);

	}

}
