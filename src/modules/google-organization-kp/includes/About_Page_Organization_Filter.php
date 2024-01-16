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
		add_filter( 'wl_website_jsonld', array( $this, '_wl_website_jsonld__add_organization_jsonld' ), 10, 3 );
		add_filter( 'wl_after_get_jsonld', array( $this, '_wl_after_get_jsonld__add_organization_jsonld' ), 10, 3 );
	}

	public function add_organization_jsonld( $jsonld, $post_id ) {
		$is_about_us = false; // @todo: Check if this is the about us page.
		$is_homepage = is_home() || is_front_page();

		if ( $is_about_us || ! $is_about_us && $is_homepage ) {
			// @todo: Check if publisher exists in JSON-LD.
			// @todo: Add organization data.

			$in = true;
		}

		return $jsonld;
	}

	public function _wl_website_jsonld__add_organization_jsonld( $jsonld, $post_id ) {
		return $this->add_organization_jsonld( $jsonld, $post_id );
	}

	public function _wl_after_get_jsonld__add_organization_jsonld( $jsonld, $post_id, $context ) {
		return $this->add_organization_jsonld( $jsonld, $post_id );
	}
}