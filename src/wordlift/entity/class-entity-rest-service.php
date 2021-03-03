<?php
/**
 * Assign the Entity type to the entities created via the block editor
 * @see https://github.com/insideout10/wordlift-plugin/issues/1304
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.29.0
 */

namespace Wordlift\Entity;

class Entity_Rest_Service {

	public function __construct() {

		add_action( "rest_insert_entity", "action_rest_insert_entity", 10, 3 );

	}


	public function action_rest_insert_entity( $post, $request, $creating ) {

		// Set the type only on entity create.
		if (! $creating ) {
			return;
		}

	}

}