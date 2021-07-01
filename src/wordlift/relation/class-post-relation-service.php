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
use Wordlift\Jsonld\Abstract_Reference;
use Wordlift\Jsonld\Post_Reference;


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


	public function get_references( $subject_id ) {
		$post_ids        = $this->legacy_post_relation_service->get_objects( $subject_id, 'ids' );
		return array_map( function ( $post_id ) {
			return new Post_Reference( $post_id );
		}, $post_ids );
	}

	public function get_relations_from_content( $post_content ) {
		// TODO: Implement get_relations_from_content() method.
	}

	public function get_relations( $post_id ) {
		// TODO: Implement get_relations() method.
	}
}