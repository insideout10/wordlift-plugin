<?php

namespace Wordlift\Modules\Include_Exclude;

class Jsonld_Interceptor {

	/** @var Plugin_Enabled $plugin_enabled */
	private $plugin_enabled;

	public function __construct( $plugin_enabled ) {
		$this->plugin_enabled = $plugin_enabled;
	}

	public function register_hooks() {
		add_action( 'wl_before_get_jsonld', array( $this, 'before_get_jsonld' ), 10, 2 );
		add_filter( 'wl_after_get_jsonld', array( $this, 'after_get_jsonld' ) );
	}

	public function before_get_jsonld( $is_homepage = false, $post_id = null ) {
		if ( null !== filter_input( INPUT_SERVER, 'HTTP_X_WORDLIFT_BYPASS_INCLUDE_EXCLUDE' ) ) {
			clean_post_cache( $post_id );
		}
	}

	public function after_get_jsonld( $jsonld_arr ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'X-Wordlift-IncludeExclude-Stage-0: Filter Called with default ' . $this->plugin_enabled->get_configuration()->get_default() );
		if ( ! is_array( $jsonld_arr ) || empty( $jsonld_arr ) || ! isset( $jsonld_arr[0]['url'] ) || null !== filter_input( INPUT_SERVER, 'HTTP_X_WORDLIFT_BYPASS_INCLUDE_EXCLUDE' ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@header( 'X-Wordlift-IncludeExclude-Stage-1: Condition Not Matched' );

			return $jsonld_arr;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'X-Wordlift-IncludeExclude-Stage-1: Condition Matched for ' . $jsonld_arr[0]['url'] );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'X-Wordlift-IncludeExclude-Note: To bypass the Include/Exclude filter add a `x-wordlift-bypass-include-exclude` HTTP request header with any value.' );

		// If the URLs are included then publish them.
		if ( $this->plugin_enabled->are_urls_included( $jsonld_arr[0]['url'] ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@header( 'X-Wordlift-IncludeExclude-Stage-2: URL Included' );

			return $jsonld_arr;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'X-Wordlift-IncludeExclude-Stage-2: URL Excluded' );

		return array();
	}

}
