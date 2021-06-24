<?php
/**
 * This class is created to provide relation service for terms
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation;


use Wordlift\Common\Singleton;
use Wordlift\Jsonld\Term_Reference;
use Wordlift\Object_Type_Enum;

class Term_Relation_Service extends Singleton implements Relation_Service_Interface {

	public function get_references( $subject_id ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
		$query           = $wpdb->prepare( "SELECT object_id FROM $table_name WHERE subject_id = %d AND object_type = %d",
			$subject_id,
			Object_Type_Enum::TERM
		);
		$term_ids        = $wpdb->get_col( $query );

		return array_map( function ( $term_id ) {
			return new Term_Reference( $term_id );
		}, $term_ids );
	}


	public function get_relations( $post_content ) {
		// TODO: Implement get_relations() method.
	}
}