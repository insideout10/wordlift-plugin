<?php

namespace Wordlift\Content\Wordpress;

use Wordlift\Content\Content_Service;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_Content_Service {

	protected function __construct() {
	}

	private static $instance = null;

	/**
	 * The singleton instance.
	 *
	 * @return Content_Service
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {

			if ( apply_filters( 'wl_feature__enable__dataset', true ) ) {
				self::$instance = Wordpress_Dataset_Content_Service::get_instance();
			} else {
				self::$instance = Wordpress_Permalink_Content_Service::get_instance();
			}
		}

		return self::$instance;
	}

}
