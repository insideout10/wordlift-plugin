<?php

namespace Wordlift\Analysis\Response;

abstract class Object_Analysis {

	/**
	 * Should return the local entity array.
	 * @param $uri
	 *
	 * @return array | bool Associative array or false
	 */
	abstract public function get_local_entity( $uri);

}