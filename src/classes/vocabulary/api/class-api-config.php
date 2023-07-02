<?php

namespace Wordlift\Vocabulary\Api;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Api_Config {

	const REST_NAMESPACE = 'cafemediakg/v1';

	public static function get_api_config() {
		// Create ui settings array to be used by js client.
		$settings            = array();
		$settings['restUrl'] = get_rest_url(
			null,
			self::REST_NAMESPACE . '/tags'
		);
		$settings['baseUrl'] = get_rest_url( null, self::REST_NAMESPACE );
		$settings['nonce']   = wp_create_nonce( 'wp_rest' );

		return $settings;
	}

}
