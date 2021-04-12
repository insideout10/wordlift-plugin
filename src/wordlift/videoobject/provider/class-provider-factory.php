<?php

namespace Wordlift\Videoobject\Provider;
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Provider_Factory {

	const YOUTUBE = 'youtube';

	const VIMEO = 'vimeo';


	public static function get_provider( $provider_name ) {

		if ( $provider_name === self::YOUTUBE ) {
			return new Youtube();
		}
		else if ( $provider_name === self::VIMEO ) {
			return new Vimeo();
		}

	}

}