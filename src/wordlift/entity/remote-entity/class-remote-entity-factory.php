<?php

namespace Wordlift\Entity\Remote_Entity;

class Remote_Entity_Factory {

	/**
	 * @param $response \Wordlift\Api\Response
	 *
	 * @return Remote_Entity
	 */
	static function from_response( $response ) {

		if ( ! $response->is_success() ) {
			return new Invalid_Remote_Entity();
		}

		$json = json_decode( $response->get_body() );

		if ( ! $json ) {
			return new Invalid_Remote_Entity();
		}

		return new Valid_Remote_Entity();

	}


}