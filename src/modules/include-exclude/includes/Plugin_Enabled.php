<?php
/**
 * This file contains the core plugin functions.
 *
 * @package Wordlift
 */

namespace Wordlift\Modules\Include_Exclude;

/**
 * Define the JSON-LD Enabled class.
 *
 * The Core class instance will hook the `wl_jsonld_enabled` filter.
 *
 * @package Wordlift
 */
class Plugin_Enabled {

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

	public function __construct( $configuration ) {
		$this->configuration = $configuration;
	}

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_filter( 'wl_is_enabled', array( $this, 'wl_is_enabled' ) );
	}

	/**
	 * Enable/Disable WordLift Plugin.
	 *
	 * @param $enabled
	 *
	 * @return bool|mixed
	 */
	public function wl_is_enabled( $enabled ) {

		// Always enable wordlift on admin and rest api pages.
		if ( is_admin() || $this->is_rest_request() ) {
			return $enabled;
		}

		$path    = strtok( (string) $_SERVER['REQUEST_URI'], '?' ); // phpcs:ignore
		$options = get_option( 'wl_exclude_include_urls_settings' );

		// Bail out if URLs are not set.
		if ( empty( $options['urls'] ) ) {
			return $enabled;
		}

		$current_url = trailingslashit( home_url( $path ) );

		return $this->are_urls_included( $current_url );
	}

	public function are_urls_included( $urls ) {
		// Ensure we deal with an array. We `trailingslashit` all URLs to avoid issues with missing slashes.
		$urls = array_map( 'trailingslashit', (array) $urls );

		// Set a default state.
		$include_by_default = ( $this->configuration->get_default() === 'include' );

		// Get URLs into an array from settings, trim them and make absolute if needed.
		$configured_urls = array_map(
			function ( $url ) {
				$url = trim( $url );
				if ( substr( $url, 0, 4 ) !== 'http' ) {
					return trailingslashit( home_url( $url ) );
				}

				// Add a trailing slash and return the url
				return trailingslashit( $url );
			},
			explode( PHP_EOL, $this->configuration->get_urls() )
		);

		// Check if any of the provided URLs is in the configured URLs.
		$intersection = array_intersect( $urls, $configured_urls );
		if ( ! empty( $intersection ) ) {
			return ! $include_by_default;
		}

		return $include_by_default;
	}

	public function get_configuration() {
		return $this->configuration;
	}

	/**
	 * We cant rely on WP_REST_REQUEST constant here since it is loaded after init hook
	 *
	 * @return bool
	 */
	protected function is_rest_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			// Probably a CLI request.
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		$path        = strtok( (string) $_SERVER['REQUEST_URI'], '?' ); // phpcs:ignore

		return strpos( $path, $rest_prefix ) !== false;
	}

}
