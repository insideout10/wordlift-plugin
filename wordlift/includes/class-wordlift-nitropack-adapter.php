<?php
/**
 * Adapters: NitroPack Adapter.
 *
 * The NitroPack Adapter makes it compatible with WL structured data.
 *
 * @since   3.39.0
 *
 * @package Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_NitroPack_Adapter} class.
 *
 * @since   3.39.0
 */
class Wordlift_NitroPack_Adapter {

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_filter( 'wl_jsonld_term_html_output', array( $this, 'maybe_exclude_nitropack' ) );
		add_filter( 'wl_jsonld_post_html_output', array( $this, 'maybe_exclude_nitropack' ) );
	}

	/**
	 * Modify JSON-LD if NitroPack active.
	 *
	 * @param $html
	 *
	 * @return string
	 */
	public function maybe_exclude_nitropack( $html ) {
		if ( ! defined( 'NITROPACK_VERSION' ) ) {
			return $html;
		}

		return preg_replace( '@id="wl-jsonld(-term)?"@', 'id="wl-jsonld$1" nitro-exclude', $html );
	}

}
