<?php

namespace Wordlift\Dataset;

interface Sync_Object_Adapter {

	/**
	 * @return int see {@link Object_Type_Enum}
	 */
	public function get_type();

	public function get_object_id();

	public function is_published();

	public function is_public();

	public function set_values( $arr );

	public function get_value( $key );

}
