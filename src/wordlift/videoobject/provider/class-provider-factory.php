<?php

namespace Wordlift\Videoobject\Provider;
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Provider_Factory {

	const YOUTUBE = 'youtube';

	const VIMEO = 'vimeo';


	public function get_provider( $provider_name ) {

		if ( $provider_name === self::YOUTUBE ) {

		}

	}

}