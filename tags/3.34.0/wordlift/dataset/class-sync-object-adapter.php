<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

interface Sync_Object_Adapter {

	/**
	 * @return int see {@link Object_Type_Enum}
	 */
	function get_type();

	function get_object_id();

	function is_published();

	function is_public();

	function set_values( $arr );

	function get_value( $key );

}