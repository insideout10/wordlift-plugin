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
	 * Hook to `rocket_exclude_defer_js` filter.
	 *
	 * @since 3.23.0 add the Cloud js.
	 * @since 3.20.0 hook to `rocket_exclude_js`.
	 * @since 3.19.4
	 *
	 * @param array $js_files The preset excluded files.
	 *
	 * @return array The updated excluded files array.
	 */
	public function exclude_js( $js_files = array() ) {

		// Exclude our own public JS.
		$js_files[] = Wordlift_Public::get_public_js_url();
		$js_files[] = Wordlift_Public::get_cloud_js_url();

		return $js_files;
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

		return $pattern;
	}

}
