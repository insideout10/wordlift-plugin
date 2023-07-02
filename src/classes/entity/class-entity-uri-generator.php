<?php

namespace Wordlift\Entity;

use Exception;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Object_Type_Enum;

class Entity_Uri_Generator {

	public static function create_uri( $object_type_enum, $id ) {

		switch ( $object_type_enum ) {
			case Object_Type_Enum::POST:
				$post = get_post( $id );
				if ( ! isset( $post ) || in_array( $post->post_status, array( 'auto-draft', 'inherit' ), true ) ) {
					return null;
				}

				$slug = $post->post_name ? $post->post_name : sanitize_title( $post->post_title ) . '-' . $post->ID;

				return self::ensure_unique( $post->post_type . '/' . $slug );

			case Object_Type_Enum::TERM:
				$term = get_term( $id );
				if ( ! is_a( $term, 'WP_Term' ) ) {
					return null;
				}

				return self::ensure_unique( $term->taxonomy . '/' . $term->slug );

			case Object_Type_Enum::USER:
				$user = get_user_by( 'id', $id );

				if ( ! is_a( $user, 'WP_User' ) ) {
					return null;
				}

				return self::ensure_unique( 'user/' . $user->user_nicename );

			default:
		}

		return null;
	}

	/**
	 * @throws Exception when an error occurs.
	 */
	private static function ensure_unique( $rel_uri ) {
		for ( $try_rel_uri = $rel_uri, $i = 2; $i < 100; $i ++ ) {
			$content = Wordpress_Content_Service::get_instance()->get_by_entity_id( $try_rel_uri );
			if ( ! isset( $content ) ) {
				return $try_rel_uri;
			}

			$try_rel_uri = $rel_uri . '-' . $i;
		}

		// Giving up.
		return $rel_uri . '-' . uniqid();
	}

}
