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
use Wordlift\Jsonld\Reference;
use Wordlift_Relation_Service;

class Object_Relation_Service extends Singleton implements Relation_Service_Interface {

	/**
	 * @var Wordlift_Relation_Service $post_relation_service
	 */
	protected $post_relation_service;

	/**
	 * @var Term_Relation_Service $term_relation_service
	 */
	protected $term_relation_service;

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	public function __construct() {
		parent::__construct();

		$this->post_relation_service = Post_Relation_Service::get_instance();
		$this->term_relation_service = Term_Relation_Service::get_instance();
		$this->log                   = \Wordlift_Log_Service::get_logger( get_class() );
	}

	/**
	 * @param $subject_id int
	 *
	 * @return array<Reference>
	 */
	public function get_references( $subject_id, $subject_type ) {
		$post_references = $this->post_relation_service->get_references( $subject_id, $subject_type );
		$term_references = $this->term_relation_service->get_references( $subject_id, $subject_type );

		/**
		 * @since 3.31.3
		 * Should return only unique references.
		 */
		return array_unique( array_merge( $post_references, $term_references ) );
	}

	public function get_relations_from_content( $content, $subject_type, $local_entity_uris ) {
		$post_relations = $this->post_relation_service->get_relations_from_content( $content, $subject_type, $local_entity_uris );
		$term_relations = $this->term_relation_service->get_relations_from_content( $content, $subject_type, $local_entity_uris );

		return array_filter( array_merge( $post_relations, $term_relations ) );
	}

	public static function get_entity_uris( $content ) {
		// Remove quote escapes.
		$content = str_replace( '\\"', '"', $content );

		// Match all itemid attributes.
		$pattern = '/<\w+[^>]*\sitemid="([^"]+)"[^>]*>/im';

		// Remove the pattern while it is found (match nested annotations).
		$matches = array();

		// In case of errors, return an empty array.
		if ( false === preg_match_all( $pattern, $content, $matches ) ) {

			return array();
		}

		return $matches[1];
	}

	public function get_relations_from_entity_uris( $subject_type, $entity_uris ) {
		$post_relations = $this->post_relation_service->get_relations_from_entity_uris( $subject_type, $entity_uris );
		$term_relations = $this->term_relation_service->get_relations_from_entity_uris( $subject_type, $entity_uris );

		return array_filter( array_merge( $post_relations, $term_relations ) );
	}
}
