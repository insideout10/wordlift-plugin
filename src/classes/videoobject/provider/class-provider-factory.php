<?php

namespace Wordlift\Videoobject\Provider;

use Wordlift\Videoobject\Provider\Client\Client_Factory;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Provider_Factory {

	const YOUTUBE = 'youtube';

	const VIMEO = 'vimeo';

	const JWPLAYER = 'jwplayer';

	public static function get_provider( $provider_name ) {
		if ( self::YOUTUBE === $provider_name ) {
			return new Youtube( Client_Factory::get_client( Client_Factory::YOUTUBE ) );
		} elseif ( self::VIMEO === $provider_name ) {
			return new Vimeo( Client_Factory::get_client( Client_Factory::VIMEO ) );
		} elseif ( self::JWPLAYER === $provider_name ) {
			return new Jw_Player( Client_Factory::get_client( Client_Factory::JWPLAYER ) );
		}

	}

}
