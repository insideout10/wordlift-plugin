<?php
/**
 * Adapters: NewRelic Adapter.
 *
 * The NewRelic Adapter tunes the Apdex index for Wordlift.
 *
 * @since   3.11.3
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_NewRelic_Adapter} class.
 *
 * @since   3.11.3
 * @package Wordlift
 */
class Wordlift_NewRelic_Adapter {

	/**
	 * Tell NewRelic to ignore this "transaction" for the Apdex.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/521
	 *
	 * @since 3.11.3
	 */
	public static function ignore_apdex() {

		// Ensure PHP agent and the function are available.
		if ( extension_loaded( 'newrelic' ) && function_exists( 'newrelic_ignore_apdex' ) ) {
			newrelic_ignore_apdex();
		}

	}

}
