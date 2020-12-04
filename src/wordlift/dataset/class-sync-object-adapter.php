<?php

namespace Wordlift\Dataset;

interface Sync_Object_Adapter {

	function get_meta( $meta_key, $single );

	function update_meta( $meta_key, $meta_value );

	function is_published();

	function is_public();

}