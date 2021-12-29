<?php

namespace Wordlift\Entity;

use Wordlift\Object_Type_Enum;

class Entity_Uri_Generator {

	public static function create_uri( $object_type_enum, $id ) {

		switch ( $object_type_enum ) {
			case Object_Type_Enum::POST:
				$post = get_post( $id );
				if ( ! isset( $post ) || in_array( $post->post_status, array( 'auto-draft', 'inherit' ) ) ) {
					return null;
				}

				return $post->post_type . '/' . $post->post_name;

			case Object_Type_Enum::TERM:
				$term = get_term( $id );
				if ( ! isset( $term ) ) {
					return null;
				}

				return $term->taxonomy . '/' . $term->slug;

			case Object_Type_Enum::USER:
				$user = get_user_by( 'id', $id );

				if ( ! isset( $user ) ) {
					return null;
				}

				return 'user/' . $user->user_nicename;

			default:
		}

		return null;
	}

}
