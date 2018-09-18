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

		add_filter( 'rocket_exclude_defer_js', array( $this, 'exclude_defer_js' ) );

	}

	/**
	 * Hook to `rocket_exclude_defer_js` filter.
	 *
	 * @since 3.19.4
	 *
	 * @param array $excluded_files The preset excluded files.
	 *
	 * @return array The updated excluded files array.
	 */
	public function exclude_defer_js( $excluded_files = array() ) {

		// Exclude our own public JS.
		return array_merge( $excluded_files, array( Wordlift_Public::get_public_js_url(), ) );
	}

}
