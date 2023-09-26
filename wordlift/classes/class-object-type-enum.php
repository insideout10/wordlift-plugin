<?php

namespace Wordlift;

class Object_Type_Enum {

	const POST     = 0;
	const TERM     = 1;
	const HOMEPAGE = 2;
	const USER     = 3;

	// Enum constant to represent currently unknown
	// value.
	const UNKNOWN = 4;

	public static function to_string( $object_type_enum ) {
		switch ( $object_type_enum ) {
			case 0:
				return 'post';
			case 1:
				return 'term';
			case 2:
				return 'home';
			case 3:
				return 'user';
			case 4:
				return 'unkn';
		}

		return null;
	}

	public static function from_string( $object_type_name ) {
		switch ( $object_type_name ) {
			case 'post':
				return 0;
			case 'term':
				return 1;
			case 'home':
				return 2;
			case 'user':
				return 3;
		}

		return 4;
	}

	public static function from_wordpress_instance( $instance ) {
		if ( ! is_object( $instance ) ) {
			return null;
		}

		switch ( get_class( $instance ) ) {
			case 'WP_Post':
				return self::POST;
			case 'WP_Term':
				return self::TERM;
			case 'WP_User':
				return self::USER;
		}

		return null;
	}

}
