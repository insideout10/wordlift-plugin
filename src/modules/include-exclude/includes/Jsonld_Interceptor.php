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
		if ( ! is_array( $jsonld_arr ) || empty( $jsonld_arr ) || ! isset( $jsonld_arr[0]['url'] ) ) {
			return $jsonld_arr;
		}

		// If the URLs are included then publish them.
		if ( $this->plugin_enabled->are_urls_included( $jsonld_arr[0]['url'] ) ) {
			return $jsonld_arr;
		}

		return array();
	}

}
