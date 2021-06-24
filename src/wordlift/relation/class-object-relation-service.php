<?php
/**
 * This class is created to provide compatibility for the term and post in
 * the relation table.
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
use Wordlift\Jsonld\Reference;

class Object_Relation_Service extends Singleton implements Relation_Service_Interface {

	/**
	 * @var \Wordlift_Relation_Service
	 */
	private $post_relation_service;
	/**
	 * @var Term_Relation_Service
	 */
	private $term_relation_service;

	public function __construct() {
		parent::__construct();
		$this->post_relation_service = \Wordlift_Relation_Service::get_instance();
		$this->term_relation_service = Term_Relation_Service::get_instance();
	}

	/**
	 * @return Object_Relation_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	/**
	 * @param $subject_id int
	 *
	 * @return array<Reference>
	 */
	public function get_references( $subject_id ) {
		$post_ids        = $this->post_relation_service->get_objects( $subject_id, 'ids' );
		$post_references = array_map( function ( $post_id ) {
			return new Post_Reference( $post_id );
		}, $post_ids );
		$term_references = $this->term_relation_service->get_references( $subject_id );
		return array_merge( $post_references, $term_references );
	}


}