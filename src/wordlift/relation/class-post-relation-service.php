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
use Wordlift\Relation\Types\Relation;
use Wordlift_Entity_Service;
use Wordlift_Entity_Uri_Service;
use Wordlift_Relation_Service;

class Post_Relation_Service extends Singleton implements Relation_Service_Interface {

	/**
	 * @var Wordlift_Relation_Service
	 */
	private $legacy_post_relation_service;

	/**
	 * @var Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;

	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;

	public function __construct() {
		parent::__construct();
		$this->legacy_post_relation_service = Wordlift_Relation_Service::get_instance();
		$this->entity_uri_service           = Wordlift_Entity_Uri_Service::get_instance();
		$this->entity_service               = Wordlift_Entity_Service::get_instance();
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function get_references( $subject_id, $subject_type ) {
		$post_ids = $this->legacy_post_relation_service->get_objects( $subject_id, 'ids' );

		return array_map(
			function ( $post_id ) {
				return new Post_Reference( $post_id );
			},
			$post_ids
		);
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function get_relations_from_content( $content, $subject_type, $local_entity_uris ) {
		$entity_uris = Object_Relation_Service::get_entity_uris( $content );

		return $this->get_relations_from_entity_uris( $subject_type, $entity_uris );
	}

	/**
	 * @param $subject_type
	 * @param $entity_uris
	 *
	 * @return false[]|Relation[]
	 */
	public function get_relations_from_entity_uris( $subject_type, $entity_uris ) {
		$that = $this;

		return array_map(
			function ( $entity_uri ) use ( $subject_type, $that ) {
				$entity = $that->entity_uri_service->get_entity( $entity_uri );
				if ( ! $entity ) {
					return false;
				}

				return new Post_Relation( $entity->ID, $that->entity_service->get_classification_scope_for( $entity->ID ), $subject_type );
			},
			$entity_uris
		);
	}
}
