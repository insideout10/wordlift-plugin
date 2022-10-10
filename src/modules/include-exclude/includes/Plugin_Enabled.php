<?php
/**
 * This file contains the core plugin functions.
 *
 * @package Wordlift
 */

namespace WordLift\Modules\Include_Exclude;

/**
 * Define the JSON-LD Enabled class.
 *
 * The Core class instance will hook the `wl_jsonld_enabled` filter.
 *
 * @package Wordlift
 */
class Plugin_Enabled {

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_filter( 'wl_is_enabled', array( $this, 'wl_is_enabled' ) );
		add_filter( 'wl_jsonld_term_html_output', array( $this, 'may_be_exclude_nitropack' ) );
		add_filter( 'wl_jsonld_post_html_output', array( $this, 'may_be_exclude_nitropack' ) );
	}

	/**
	 * Modify JSON-LD if Nitropack active.
	 *
	 * @param $html
	 *
	 * @return array|mixed|string|string[]
	 */
	public function may_be_exclude_nitropack( $html ) {
		if ( ! defined( 'NITROPACK_VERSION' ) ) {
			return $html;
		}
		$html = str_replace( 'id="wl-jsonld"', 'id="wl-jsonld" nitro-exclude', $html );
		$html = str_replace( 'id="wl-jsonld-term"', 'id="wl-jsonld-term" nitro-exclude', $html );

		return $html;
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

		$path        = strtok( (string) $_SERVER['REQUEST_URI'], '?' ); // phpcs:ignore
		$options     = get_option( 'wl_exclude_include_urls_settings' );
		$current_url = trailingslashit( home_url( $path ) );

		// Bail out if URLs are not set.
		if ( empty( $options['urls'] ) ) {
			return $enabled;
		}

		// Set a default state
		$default_state = $options['include_exclude'] === 'exclude' ? true : false;

		// Get URLs into an array from settings, trim them and make absolute if needed.
		$urls = array_map(
			function ( $url ) {
				$url = trim( $url );
				if ( substr( $url, 0, 4 ) !== 'http' ) {
					return trailingslashit( home_url( $url ) );
				}

				// Add a trailing slash and return the url
				return trailingslashit( $url );
			},
			explode( PHP_EOL, $options['urls'] )
		);

		foreach ( $urls as $url ) {
			if ( $url === $current_url ) {
				return ! $default_state;
			}
		}
		return $default_state;

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
