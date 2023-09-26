<?php
/**
 * Adapters: WP-Rocket Adapter.
 *
 * The WP-Rocket Adapter tunes the Apdex index for Wordlift.
 *
 * @since   3.19.4
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/842.
 * @see https://github.com/wp-media/wp-rocket-helpers/blob/master/static-files/wp-rocket-static-exclude-defer-js/wp-rocket-static-exclude-defer-js.php.
 *
 * @package Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_WP-Rocket_Adapter} class.
 *
 * @since   3.19.4
 */
class Wordlift_WpRocket_Adapter {

	/**
	 * Create a {@link Wordlift_WpRocket_Adapter} instance.
	 *
	 * @since 3.19.4
	 */
	public function __construct() {

		add_filter( 'rocket_exclude_js', array( $this, 'exclude_js' ) );
		add_filter( 'rocket_excluded_inline_js_content', array( $this, 'excluded_inline_js_content' ) );

	}

	/**
	 * Get the absolute path for the specified URL.
	 *
	 * @param string $url The full URL.
	 *
	 * @return string The absolute path.
	 */
	private function get_absolute_path( $url ) {

		if ( 1 !== preg_match( '|https?://[^/]+(/.*)$|', $url, $matches ) ) {
			return $url;
		}

		return $matches[1];
	}

	/**
	 * Hook to `rocket_exclude_defer_js` filter.
	 *
	 * @param array $excluded_js The preset excluded files.
	 *
	 * @return array The updated excluded files array.
	 *
	 * @since 3.25.0 We realized that WP Rocket has an issue with these path: while it seems to expect a path relative
	 *               to home_url, it is actually looking for a path relative to the root. We leave both exclusions so
	 *               that we'll be compatible in case WP Rocket fixes its implementation.
	 * @since 3.23.0 add the Cloud js.
	 * @since 3.20.0 hook to `rocket_exclude_js`.
	 * @since 3.19.4
	 */
	public function exclude_js( $excluded_js = array() ) {

		// Exclude our own public JS.
		$excluded_js[] = $this->get_absolute_path( Wordlift_Public::get_public_js_url() );
		$excluded_js[] = $this->get_absolute_path( Wordlift_Public::get_cloud_js_url() );

		$excluded_js[] = str_replace( home_url(), '', plugin_dir_url( __DIR__ ) . '/js/dist/bundle.js' );
		$excluded_js[] = str_replace( home_url(), '', plugin_dir_url( __DIR__ ) . '/js/dist/wordlift-cloud.js' );

		return $excluded_js;
	}

	/**
	 * Filters inline JS excluded from being combined
	 *
	 * @link https://github.com/insideout10/wordlift-plugin/issues/868
	 *
	 * @since 3.20.0
	 *
	 * @param array $pattern Patterns to match.
	 *
	 * @return array Patterns to match.
	 */
	public function excluded_inline_js_content( $pattern = array() ) {

		$pattern[] = 'wlSettings';
		$pattern[] = 'wlNavigators';
		$pattern[] = '_wlCloudSettings';
		$pattern[] = 'wlProductsNavigators';
		$pattern[] = 'wlFaceteds';

		return $pattern;
	}

}
