<?php
/**
 * Assign the Entity type to the entities created via the block editor
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1304
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.29.0
 */

namespace Wordlift\Entity;

class Entity_Rest_Service {
	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;

	/**
	 * Entity_Rest_Service constructor.
	 *
	 * @param $entity_type_service \Wordlift_Entity_Type_Service
	 */
	public function __construct( $entity_type_service ) {

		$this->entity_type_service = $entity_type_service;

		add_action( 'rest_insert_entity', array( $this, 'action_rest_insert_entity' ), 10, 3 );

	}

	/**
	 * @param $post \WP_Post
	 * @param $request \WP_REST_Request
	 * @param $creating bool
	 */
	public function action_rest_insert_entity( $post, $request, $creating ) {

		// Set the type only on entity create.
		if ( ! $creating ) {
			return;
		}

		$entity_types = $request->get_param( 'wlEntityMainType' );

		if ( null === $entity_types ) {
			// Return early if entity types not set.
			return;
		}

		foreach ( $entity_types as $type_uri ) {
			// we don't replace since its entity creation and only one entity will be present.
			$this->entity_type_service->set( $post->ID, $type_uri );
		}

	}

}
