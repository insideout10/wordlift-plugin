<?php

namespace Wordlift;

class Object_Type_Enum {

	const POST = 0;
	const TERM = 1;
	const HOMEPAGE = 2;
	const USER = 3;

	// Enum constant to represent currently unknown
	// value.
	const UNKNOWN = 4;

	public static function to_string( $object_type_enum ) {
		switch ( $object_type_enum ) {
			case 0:
				return 'post';
			case 1:
				return 'type';
			case 2:
				return 'home';
			case 3:
				return 'user';
			case 4:
				return 'unkn';
		}

		return null;
	}

}
