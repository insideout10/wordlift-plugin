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

use Wordlift\Content\Wordpress\Wordpress_Term_Content_Service;
use Wordlift\Term\Type_Service;

class Term_Entity_Provider implements Entity_Provider {

	/**
	 * @var Type_Service
	 */
	private $term_type_service;

	public function __construct() {
		$this->term_type_service = Type_Service::get_instance();
	}

	public function get_entity( $uri ) {

		$content = Wordpress_Term_Content_Service::get_instance()->get_by_entity_id_or_same_as( $uri );

		if ( ! isset( $content ) ) {
			return false;
		}

		$term    = $content->get_bag();
		$term_id = $term->term_id;

		$schema = $this->term_type_service->get_schema( $term_id );
		// @todo: For now we dont support images
		// $images = $this->post_image_storage->get( $term_entity->ID );
		$same_as = get_term_meta( $term_id, 'entity_same_as' );
		$same_as = $same_as ? $same_as : array();

		return (object) array(
			'id'          => $uri,
			'entityId'    => $uri,
			'label'       => $term->name,
			'description' => '',
			'sameAs'      => $same_as,
			'mainType'    => str_replace( 'wl-', '', $schema['css_class'] ),
			'types'       => $this->term_type_service->get_entity_types_labels( $term_id ),
			'images'      => array(),
		);
	}

}
