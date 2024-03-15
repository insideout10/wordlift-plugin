<?php

namespace Wordlift\Modules\Include_Exclude;

class Jsonld_Interceptor {

	/** @var Plugin_Enabled $plugin_enabled */
	private $plugin_enabled;

	public function __construct( $plugin_enabled ) {
		$this->plugin_enabled = $plugin_enabled;
	}

	public function register_hooks() {
		add_filter( 'wl_after_get_jsonld', array( $this, 'after_get_jsonld' ) );
	}

	public function after_get_jsonld( $jsonld_arr ) {
		header( 'X-Wordlift-IncludeExclude-Stage-0: Filter Called with default ' . $this->plugin_enabled->get_configuration()->get_default() );
		if ( ! is_array( $jsonld_arr ) || empty( $jsonld_arr ) || ! isset( $jsonld_arr[0]['url'] ) || null !== filter_input( INPUT_SERVER, 'HTTP_X_WORDLIFT_BYPASS_INCLUDE_EXCLUDE' ) ) {
			header( 'X-Wordlift-IncludeExclude-Stage-1: Condition Not Matched' );

			return $jsonld_arr;
		}

		header( 'X-Wordlift-IncludeExclude-Stage-1: Condition Matched for ' . $jsonld_arr[0]['url'] );
		header( 'X-Wordlift-IncludeExclude-Note: To bypass the Include/Exclude filter add a `X-Wordlift-Bypass-Include-Exclude` HTTP request header with any value.' );

		// If the URLs are included then publish them.
		if ( $this->plugin_enabled->are_urls_included( $jsonld_arr[0]['url'] ) ) {
			header( 'X-Wordlift-IncludeExclude-Stage-2: URL Included' );

			return $jsonld_arr;
		}

		header( 'X-Wordlift-IncludeExclude-Stage-2: URL Excluded' );

		return array();
	}

}
