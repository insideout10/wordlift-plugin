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
use Wordlift\Relation\Types\Term_Relation;

class Term_Relation_Service extends Singleton implements Relation_Service_Interface {

	/**
	 * @return Term_Relation_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


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


	public function get_relations_from_content( $post_content ) {
		/**
		 * This method is not implemented, this is implemented
		 * efficiently by the {@link Object_Relation_Service::get_relations_from_content()} method
		 */
	}

	public function get_relations( $post_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
		$query_template = <<<EOF
SELECT object_id FROM $table_name WHERE object_type = %d AND subject_id = %d
EOF;
		$query = $wpdb->prepare( $query_template, Object_Type_Enum::TERM, $post_id );
		$term_relations =  array_unique( $wpdb->get_col($query) );
		if ( ! $term_relations ) {
			$term_relations = array();
		}
		return array_map( function ( $term_id) {
			// @todo: this needs to be fixed, we need to determine the relation
			return new Term_Relation( $term_id,  WL_WHAT_RELATION );
		}, $term_relations );
	}
}