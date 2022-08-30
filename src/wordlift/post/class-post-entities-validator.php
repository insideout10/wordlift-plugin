<?php
/**
 * This file validates the entities present in the post content, if any of the entity has id set to
 * local uri, and its not present on the WP, instead of creating a new entity we need to remove the
 * existing annotation which points to non existent local entity.
 *
 * @authod Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.29.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Post
 */

namespace Wordlift\Post;

class Post_Entities_Validator {

	/**
	 * @param $entity_uri_service \Wordlift_Entity_Uri_Service
	 * @param $ids array<string> An array of entity ids
	 *
	 * @return bool
	 */
	public static function is_local_entity_uri_exist( $entity_uri_service, $ids ) {
		foreach ( $ids as $id ) {
			if ( $entity_uri_service->is_internal( $id ) ) {
				return true;
			}
		}

		return false;
	}

}
