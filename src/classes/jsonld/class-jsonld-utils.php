<?php

namespace Wordlift\Jsonld;

class Jsonld_Utils {

	/**
	 * Get about match name value
	 *
	 * @param $jsonld
	 *
	 * @return mixed|null
	 */
	public static function get_about_match_name( $jsonld ) {
		$data = json_decode( $jsonld, true );
		if ( ! $data || ! array_key_exists( 'name', $data ) ) {
			return null;
		}

		return $data['name'];
	}
}
