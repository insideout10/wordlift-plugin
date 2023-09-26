<?php
/**
 * This file provides a class for post entity provider.
 * This is used to retrieve the entity for post.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 * @package Wordlift\Analysis\Entity_Provider
 */

namespace Wordlift\Analysis\Entity_Provider;

use Wordlift\Content\Wordpress\Wordpress_Content_Service;

class Post_Entity_Provider implements Entity_Provider {

	/**
	 * @var \Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;
	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;
	/**
	 * @var \Wordlift_Post_Image_Storage
	 */
	private $post_image_storage;

	public function __construct( $entity_uri_service, $entity_type_service, $post_image_storage ) {
		$this->entity_uri_service  = $entity_uri_service;
		$this->entity_type_service = $entity_type_service;
		$this->post_image_storage  = $post_image_storage;
	}

	public function get_entity( $uri ) {
		$content = Wordpress_Content_Service::get_instance()->get_by_entity_id_or_same_as( $uri );

		if ( ! isset( $content ) || ! is_a( $content->get_bag(), '\WP_Post' ) ) {
			return false;
		}

		$entity = $content->get_bag();
		$type   = $this->entity_type_service->get( $entity->ID );
		$images = $this->post_image_storage->get( $entity->ID );

		return (object) array(
			'id'          => $uri,
			'entityId'    => $uri,
			'label'       => $entity->post_title,
			/*
			 * As of 2020.06.29 we're comment out the `post_content` because Gutenberg posts will return here
			 * the whole Gutenberg source including potentially our own wordlift/classification block, which means
			 * that data may grow quickly to more than a 100 KBytes and could break web servers.
			 *
			 * We don't really need the description for local entities (because the description is indeed taken from
			 * the local WordPress database) and we're not using it anywhere in the UI.
			 *
			 * So take extra care in enabling this line: eventually consider using the post_excerpt.
			 *
			 * PS: We didn't test using the WordLift Post Excerpt Helper.
			 */
			// 'description' => $entity->post_content,
			'description' => '',
			'sameAs'      => wl_schema_get_value( $entity->ID, 'sameAs' ),
			'mainType'    => str_replace( 'wl-', '', $type['css_class'] ),
			'types'       => wl_get_entity_rdf_types( $entity->ID ),
			'images'      => $images,
		);
	}

}
