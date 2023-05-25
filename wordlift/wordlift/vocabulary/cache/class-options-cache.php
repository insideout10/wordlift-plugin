<?php

namespace Wordlift\Vocabulary\Cache;

class Options_Cache implements Cache {

	private $namespace;

	/**
	 * Options_Cache constructor.
	 *
	 * @param $namespace
	 */
	public function __construct( $namespace ) {
		$this->namespace = $namespace;
	}

	public function get( $cache_key ) {

		return get_option( $this->namespace . '__' . $cache_key, false );

	}

	public function put( $cache_key, $value ) {

		return update_option( $this->namespace . '__' . $cache_key, $value, false );

	}

	public function flush_all() {
		if ( '' !== $this->namespace ) {
			global $wpdb;
			$namespace_esc = $wpdb->esc_like( $this->namespace ) . '__%';
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", $namespace_esc ) );
		}
	}

}
