<?php
/**
 * This file provides a class for term entity provider.
 * It retrieves the entity for the term.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 * @package Wordlift\Analysis\Entity_Provider
 */
namespace Wordlift\Analysis\Entity_Provider;

use Wordlift\Term\Type_Service;
use Wordlift\Term\Uri_Service;

class Term_Entity_Provider extends Entity_Provider {

	/**
	 * @var Uri_Service
	 */
	private $term_uri_service;
	/**
	 * @var Type_Service
	 */
	private $term_type_service;


	public function __construct( ) {
		parent::__construct();
		$this->term_uri_service   = Uri_Service::get_instance();
		$this->term_type_service  = Type_Service::get_instance();
	}


	public function get_entity( $uri ) {

		$term_entity = $this->term_uri_service->get_term( $uri );

		if ( ! $term_entity ) {
			return false;
		}

		$type   = $this->term_type_service->get_entity_types( $term_entity->term_id );
		// @todo: For now we dont support images
//		$images = $this->post_image_storage->get( $term_entity->ID );
		$same_as = get_term_meta( $term_entity->term_id, 'entity_same_as' );
		$same_as = $same_as ? $same_as : array();
		return (object) array(
			'id'          => $uri,
			'label'       => $term_entity->name,
			'description' => '',
			'sameAs'      => $same_as,

			// @todo
			'mainType'    => 'thing',
			// @todo
			'types'       => array('Thing'),

			'images'      => array(),
		);
	}

}
