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
use Wordlift\Relation\Types\Post_Relation;
use Wordlift\Relation\Types\Term_Relation;
use Wordlift\Term\Uri_Service;

class Object_Relation_Service extends Singleton implements Relation_Service_Interface {

	/**
	 * @var \Wordlift_Relation_Service
	 */
	private $post_relation_service;
	/**
	 * @var Term_Relation_Service
	 */
	private $term_relation_service;
	/**
	 * @var \Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;
	/**
	 * @var Uri_Service
	 */
	private $term_uri_service;
	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;
	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	public function __construct() {
		parent::__construct();
		$this->post_relation_service = \Wordlift_Relation_Service::get_instance();
		$this->term_relation_service = Term_Relation_Service::get_instance();
		$this->entity_uri_service    = \Wordlift_Entity_Uri_Service::get_instance();
		$this->term_uri_service      = Uri_Service::get_instance();
		$this->entity_service        = \Wordlift_Entity_Service::get_instance();
		$this->log                   = \Wordlift_Log_Service::get_logger( get_class() );
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


	public function get_relations_from_content( $post_content ) {
		$entity_uris = array_unique( self::get_entity_uris( $post_content ) );
		$this->log->debug( "Found " . var_export( $entity_uris, true ) . " by object relation service" );
		/**
		 * We should never have cases where the term entity URI conflicts
		 * with the post entity URI, check if it matches entity then
		 * check if it matches term
		 */
		$relations = array_map( function ( $uri ) {

			$entity = $this->entity_uri_service->get_entity( $uri );

			if ( $entity ) {
				return new Post_Relation( $entity->ID, $this->entity_service->get_classification_scope_for( $entity->ID ) );
			}

			$term = $this->term_uri_service->get_term( $uri );
			if ( $term ) {
				return new Term_Relation( $term->term_id, WL_WHAT_RELATION );
			}

			return false;
		}, $entity_uris );

		return array_filter( $relations );

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

	public function get_relations( $post_id ) {

	}
}