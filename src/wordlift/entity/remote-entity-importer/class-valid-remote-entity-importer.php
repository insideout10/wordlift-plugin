<?php

namespace Wordlift\Entity\Remote_Entity_Importer;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Entity\Remote_Entity\Valid_Remote_Entity;
use Wordlift_Entity_Type_Service;

class Valid_Remote_Entity_Importer implements Remote_Entity_Importer {

	/**
	 * @var Valid_Remote_Entity
	 */
	private $entity;

	/**
	 * @param $entity Valid_Remote_Entity
	 */
	public function __construct( $entity ) {
		$this->entity = $entity;
	}

	public function import() {

		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();

		$post_id = wp_insert_post(
			array(
				'post_title'   => $this->entity->get_name(),
				'post_content' => $this->entity->get_description(),
				'post_status'  => 'draft',
				'post_type'    => 'entity',
			)
		);

		foreach ( $this->entity->get_types() as $type ) {
			$entity_type_service->set( $post_id, "http://schema.org/$type", false );
		}

		foreach ( $this->entity->get_same_as() as $same_as ) {
			add_post_meta( $post_id, 'entity_same_as', $same_as );
		}

		return Wordpress_Content_Id::create_post( $post_id );
	}
}
