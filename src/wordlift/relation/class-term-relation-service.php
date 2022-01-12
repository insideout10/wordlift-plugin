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
use Wordlift\Content\Wordpress\Wordpress_Term_Content_Legacy_Service;
use Wordlift\Jsonld\Term_Reference;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Types\Term_Relation;
use Wordlift\Term\Type_Service;

class Term_Relation_Service extends Singleton implements Relation_Service_Interface {

	/**
	 * @var Type_Service
	 */
	private $term_entity_type_service;


	public function __construct() {
		parent::__construct();
		$this->term_entity_type_service = Type_Service::get_instance();
	}

	/**
	 * @return Term_Relation_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	public function get_references( $subject_id, $subject_type ) {
		global $wpdb;
		$table_name = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
		$query      = $wpdb->prepare( "SELECT object_id FROM $table_name WHERE subject_id = %d AND object_type = %d AND subject_type = %d",
			$subject_id,
			Object_Type_Enum::TERM,
			$subject_type
		);

		$term_ids = $wpdb->get_col( $query );

		return array_map( function ( $term_id ) {
			return new Term_Reference( $term_id );
		}, $term_ids );
	}


	public function get_relations_from_content( $content, $subject_type, $local_entity_uris ) {
		$entity_uris = Object_Relation_Service::get_entity_uris( $content );

		return $this->get_relations_from_entity_uris( $subject_type, $entity_uris );
	}

	/**
	 * @param $term_id int Term id.
	 */
	public function get_relation_type( $term_id ) {
		$schema               = $this->term_entity_type_service->get_schema(
			$term_id
		);
		$classification_boxes = unserialize( WL_CORE_POST_CLASSIFICATION_BOXES );

		$entity_type = str_replace( 'wl-', '', $schema['css_class'] );
		foreach ( $classification_boxes as $cb ) {
			if ( in_array( $entity_type, $cb['registeredTypes'] ) ) {
				return $cb['id'];
			}
		}

		return WL_WHAT_RELATION;
	}

	/**
	 * @param $subject_type
	 * @param $entity_uris
	 *
	 * @return false[]|Types\Relation[]
	 */
	public function get_relations_from_entity_uris( $subject_type, $entity_uris ) {
		$that = $this;

		return array_map( function ( $entity_uri ) use ( $subject_type, $that ) {
			$content = Wordpress_Term_Content_Legacy_Service::get_instance()
			                                                ->get_by_entity_id( $entity_uri );

			if ( ! isset( $content ) ) {
				return false;
			}

			$term_id = $content->get_bag()->term_id;

			return new Term_Relation( $term_id, $that->get_relation_type( $term_id ), $subject_type );
		}, $entity_uris );
	}


}