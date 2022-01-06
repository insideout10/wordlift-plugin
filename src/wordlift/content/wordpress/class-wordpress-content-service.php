<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Content\Content_Service;

class Wordpress_Content_Service {

	private static $instance = null;

	protected function __constructor() {
	}

	/**
	 * The singleton instance.
	 *
	 * @return Content_Service
	 * @throws Exception
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {

			if ( apply_filters( 'wl_features__enable__dataset', true ) ) {
				self::$instance = Wordpress_Dataset_Content_Service::get_instance();
			} else {
				self::$instance = Wordpress_Permalink_Content_Service::get_instance();
			}

		}

		return self::$instance;
	}

}
