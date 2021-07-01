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
use Wordlift\Jsonld\Post_Reference;
use Wordlift\Relation\Types\Post_Relation;

class Post_Relation_Service extends Singleton implements Relation_Service_Interface {
	/**
	 * @var \Wordlift_Relation_Service
	 */
	private $legacy_post_relation_service;
	/**
	 * @var \Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;
	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;


	/**
	 * @return Post_Relation_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	public function __construct() {
		parent::__construct();
		$this->legacy_post_relation_service = \Wordlift_Relation_Service::get_instance();
		$this->entity_uri_service    = \Wordlift_Entity_Uri_Service::get_instance();
		$this->entity_service        = \Wordlift_Entity_Service::get_instance();
	}


	public function get_references( $subject_id, $subject_type ) {
		$post_ids        = $this->legacy_post_relation_service->get_objects( $subject_id, 'ids' );
		return array_map( function ( $post_id ) {
			return new Post_Reference( $post_id );
		}, $post_ids );
	}

	public function get_relations_from_content( $content, $subject_type ) {
		$entity_uris =  Object_Relation_Service::get_entity_uris( $content );
		return array_map( function ( $entity_uri ) {
			$entity =  $this->entity_uri_service->get_entity( $entity_uri );
			if (  ! $entity ) {
				return false;
			}
			return new Post_Relation( $entity->ID, $this->entity_service->get_classification_scope_for( $entity->ID ) );
		}, $entity_uris );
	}
}