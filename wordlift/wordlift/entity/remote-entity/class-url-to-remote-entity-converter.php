<?php

namespace Wordlift\Entity\Remote_Entity;

use Wordlift\Api\Default_Api_Service;

class Url_To_Remote_Entity_Converter {

	/**
	 * @param $url
	 *
	 * @return Remote_Entity
	 */
	public static function convert( $url ) {
		$target_path = '/id/' . preg_replace( '@^(https?)://@', '$1/', $url );
		$response    = Default_Api_Service::get_instance()->get( $target_path );

		return Remote_Entity_Factory::from_response( $url, $response );
	}

}
