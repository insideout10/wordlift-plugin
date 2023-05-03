<?php
/**
 * This file provides a default strategy to add the occurences in analysis service.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */

namespace Wordlift\Analysis\Occurrences;

use Wordlift\Common\Singleton;
use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Relation\Relation;
use Wordlift\Relation\Relation_Service;
use Wordlift\Relation\Relation_Service_Interface;

class No_Annotation_Strategy extends Singleton implements Occurrences {

	/**
	 * @var Relation_Service_Interface
	 */
	private $relation_service;

	/**
	 * @var Content_Service
	 */
	private $content_service;

	protected function __construct() {
		parent::__construct();
		$this->relation_service = Relation_Service::get_instance();
		$this->content_service  = Wordpress_Content_Service::get_instance();
	}

	public function add_occurrences_to_entities( $occurrences, $json, $post_id ) {

		$content_id       = Wordpress_Content_Id::create_post( $post_id );
		$relation_service = Relation_Service::get_instance();
		$relations        = $relation_service->get_relations( $content_id );

		/** @var Relation $relation */
		foreach ( $relations->toArray() as $relation ) {
			$object = $relation->get_object();
			// @@todo is this working okey with term id?
			$entity_uri = $this->content_service->get_entity_id( $relation->get_object() );
			if ( ! $entity_uri ) {
				continue;
			}
			$entity_data                   = wl_serialize_entity( $object->get_id() );
			$entity_data['occurrences']    = array( 'placeholder-occurrence' );
			$json->entities->{$entity_uri} = $entity_data;
		}

		return $json;
	}

}
