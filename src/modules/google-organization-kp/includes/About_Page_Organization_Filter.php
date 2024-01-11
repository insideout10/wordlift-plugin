<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	About_Page_Organization_Filter
 *
 * Hook into the
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
		add_filter( 'wl_post_jsonld', array( $this, '_wl_add_about_page_jsonld' ), 10, 3 );
	}

	public function _wl_add_about_page_jsonld( $jsonld, $post_id, $references ) {

		// @todo: Check if "About page" property is set
		if ( false || ( is_home( $post_id ) && is_front_page( $post_id ) ) ) {
			// @todo: Add organization JSON-LD
		}
	}
}