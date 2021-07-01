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
use Wordlift\Term\Uri_Service;

class Term_Relation_Service extends Singleton implements Relation_Service_Interface {

	/**
	 * @var Uri_Service
	 */
	private $term_uri_service;

	public function __construct() {
		parent::__construct();
		$this->term_uri_service = Uri_Service::get_instance();
	}

	/**
	 * @return Term_Relation_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	public function get_references( $subject_id, $subject_type ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
		$query           = $wpdb->prepare( "SELECT object_id FROM $table_name WHERE subject_id = %d AND object_type = %d AND subject_type = %d",
			$subject_id,
			Object_Type_Enum::TERM,
			$subject_type
		);
		$term_ids        = $wpdb->get_col( $query );
		return array_map( function ( $term_id ) {
			return new Term_Reference( $term_id );
		}, $term_ids );
	}


	public function get_relations_from_content( $content, $subject_type ) {
		$entity_uris =  Object_Relation_Service::get_entity_uris( $content );
		$that = $this;
		return array_map( function ( $entity_uri ) use ( $subject_type, $that ) {
			$term =  $that->term_uri_service->get_term( $entity_uri );
			if ( ! $term ) {
				return false;
			}
			return new Term_Relation( $term->term_id, $that->get_relation_type( $term->term_id ), $subject_type );
		}, $entity_uris );
	}

	/**
	 * @param $term_id int Term id.
	 */
	private function get_relation_type( $term_id ) {

	}
}