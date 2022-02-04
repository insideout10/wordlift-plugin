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

		$entity_data = json_decode( $response->get_body(), true );

		if ( ! $entity_data ) {
			return new Invalid_Remote_Entity();
		}

		if ( ! array_key_exists( '@type', $entity_data ) ) {
			return new Invalid_Remote_Entity();
		}

		return new Valid_Remote_Entity(
			self::may_be_wrap_array( $entity_data['@type'] )
		);

	}

	private static function may_be_wrap_array( $el ) {
		if ( is_array( $el ) ) {
			return $el;
		}

		return array( $el );
	}


}