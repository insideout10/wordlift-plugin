<?php

namespace Wordlift\Common;

abstract class Singleton {

	public static $instances = array();

	protected function __construct() {
	}

	public static function get_instance() {
		$child_class_name = get_called_class();
		if ( ! array_key_exists( $child_class_name, self::$instances ) ) {
			self::$instances[ $child_class_name ] = new $child_class_name();
		}

		return self::$instances[ $child_class_name ];
	}

}
