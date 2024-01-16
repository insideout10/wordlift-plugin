<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	About_Page_Organization_Filter
 *
 * @since
 * @package Wordlift/modules/google-organization-kp
 */

namespace Wordlift\Modules\Google_Organization_Kp;

class About_Page_Organization_Filter {
	/**
	 * Hook into wl_post_jsonld
	 *
	 * @since
	 */
	public function init() {
		add_filter( 'wl_post_jsonld', array( $this, '_wl_post_jsonld__expand_publisher' ), 10, 3 );
		add_filter( 'wl_after_get_jsonld', array( $this, '_wl_after_get_jsonld__add_organization' ), 10, 3 );
	}

	public function _wl_post_jsonld__expand_publisher( $jsonld, $post_id, $references ) {



		return $jsonld;
	}

	public function _wl_after_get_jsonld__add_organization( $jsonld, $post_id, $references ) {

		$is_homepage = is_home() || is_front_page();

		// @todo: Check if "About page" property is set
		if ( false || $is_homepage ) {
			// @todo: Add organization JSON-LD
		}

		return $jsonld;
	}
}