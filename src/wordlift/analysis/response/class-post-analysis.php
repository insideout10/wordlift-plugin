<?php
namespace Wordlift\Analysis\Response;

class Post_Analysis extends Object_Analysis {

	public function make_entities_local() {
		// TODO: Implement make_entities_local() method.
	}

	public function add_occurrences( $content ) {
		// TODO: Implement add_occurrences() method.
	}

	public function add_local_entities() {
		// TODO: Implement add_local_entities() method.
	}


	public function get_local_entity( $uri ) {

		$entity = $this->entity_uri_service->get_entity( $uri );

		if ( null === $entity ) {
			return false;
		}

		$type   = $this->entity_type_service->get( $entity->ID );
		$images = $this->post_image_storage->get( $entity->ID );

		return (object) array(
			'id'          => $uri,
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