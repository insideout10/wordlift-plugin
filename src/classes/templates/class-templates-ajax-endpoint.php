<?php
/**
 * This file provides an AJAX end-point to retrieve AngularJS templates (Html files).
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/834
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.24.4
 * @package Wordlift\Templates
 */

namespace Wordlift\Templates;

/**
 * Class Templates_Ajax_Endpoint
 *
 * @package Wordlift\Templates
 */
class Templates_Ajax_Endpoint {

	/**
	 * Templates_Ajax_Endpoint constructor.
	 *
	 * Hook to `wl_templates` Ajax action.
	 */
	public function __construct() {

		add_action( 'wp_ajax_wl_templates', array( $this, 'template' ) );

	}

	/**
	 * Display the requested template. The template is searched in wp-content/wordlift/templates/wordlift-widget-be/
	 *
	 * Non alphanumeric names (including `-`) are considered invalid.
	 */
	public function template() {

		$name = filter_input( INPUT_GET, 'name' );

		if ( 1 !== preg_match( '|^[a-z0-9\-]+$|', $name ) ) {
			return wp_send_json_error( 'Invalid name.' );
		}

		require dirname( dirname( __DIR__ ) ) . "/templates/wordlift-widget-be/$name.html";

		die();
	}

}
