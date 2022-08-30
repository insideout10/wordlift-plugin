<?php

namespace Wordlift\Api;

use Wordlift;

class User_Agent {

	public static function get_user_agent() {

		// Get WL version.
		$wl_version = Wordlift::get_instance()->get_version();

		// Get the WP version.
		$wp_version = get_bloginfo( 'version' );

		// Get the home url.
		$home_url = home_url( '/' );

		// Get the locale flag.
		$locale = apply_filters( 'core_version_check_locale', get_locale() );

		// Get the multisite flag.
		$multisite = is_multisite() ? 'yes' : 'no';

		// Get the PHP version.
		$php_version = phpversion();

		/** @var string $wp_version The variable is defined in `version.php`. */
		return "WordLift/$wl_version WordPress/$wp_version (multisite:$multisite, url:$home_url, locale:$locale) PHP/$php_version";
	}

}
