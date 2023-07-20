<?php

namespace Wordlift\Modules\Events\Options_Entity;

use Exception;
use Wordlift\Api\Default_Api_Service;

/**
 * Class Events_Options_Entity_Include_Exclude
 *
 * @package Wordlift\Modules\Events\Options_Entity
 */
class Events_Options_Entity_Include_Exclude {
	/**
	 * The {@link Api_Service} used to communicate with the remote APIs.
	 *
	 * @access private
	 * @var Default_Api_Service
	 */
	private $api_service;

	/**
	 * @param Default_Api_Service $api_service
	 */
	public function __construct( Default_Api_Service $api_service ) {
		$this->api_service = $api_service;
	}

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_action( 'update_option_wl_exclude_include_urls_settings', array( $this, 'event_update' ), 15, 0 );
		add_action( 'update_option_wl_exclude_include_urls_settings', array( $this, 'save_old_config' ), 99, 0 );
	}


	/**
	 * Include exclude event update.
	 *
	 * @throws Exception If the application fails to load the services configuration file or if the URL cannot be processed.
	 */
	public function event_update() {
		// Get the configurations.
		$config     = get_option( 'wl_exclude_include_urls_settings', array() );
		$old_config = get_option( 'wl_exclude_include_urls_settings_old', array() );

		// Get included and excluded URLs.
		$urls = $this->get_urls( $config, $old_config );

		// Call API method for each URL.
		foreach ( $urls['included'] as $url ) {
			$this->send_event( $url, 'include' );
		}
		foreach ( $urls['excluded'] as $url ) {
			$this->send_event( $url, 'exclude' );
		}
	}

	/**
	 * Save old config.
	 */
	public function save_old_config() {
		// Get the current configuration.
		$config = get_option( 'wl_exclude_include_urls_settings', array() );

		// Save the current configuration to another option.
		update_option( 'wl_exclude_include_urls_settings_old', $config );
	}


	/**
	 * Get included and excluded urls.
	 *
	 * @param $config
	 * @param $old_config
	 *
	 * @return array
	 */
	private function get_urls( $config, $old_config ) {
		// Get the payload for both new and old values:
		$payload_new = $this->get_payload( $config );
		$payload_old = $this->get_payload( $old_config );

		// Extract URLs from payloads.
		$urls_new = array_column( $payload_new, 'url' );
		$urls_old = array_column( $payload_old, 'url' );

		// If both $urls_new and $urls_old are empty, there is no URL to process.
		if ( empty( $urls_new ) && empty( $urls_old ) ) {
			return [
				'included' => [],
				'excluded' => [],
			];
		}

		// Find added and removed URLs.
		$urls_added   = array_diff( $urls_new, $urls_old );
		$urls_removed = array_diff( $urls_old, $urls_new );

		// Determine included and excluded URLs.
		$included = ( 'include' === strtolower( $config['include_exclude'] ) ) ? $urls_added : $urls_removed;
		$excluded = ( 'include' === strtolower( $config['include_exclude'] ) ) ? $urls_removed : $urls_added;

		// Check if filter type has changed.
		$filter_changed = strtolower( $config['include_exclude'] ) !== strtolower( $old_config['include_exclude'] );

		if ( $filter_changed ) {
			// Filter type changed, so we reverse the logic of adding URLs to included and excluded arrays.
			$included = ( 'include' === strtolower( $config['include_exclude'] ) ) ? $urls_new : $urls_removed;
			$excluded = ( 'include' === strtolower( $config['include_exclude'] ) ) ? $urls_removed : $urls_new;
		}

		return [
			'included' => $included,
			'excluded' => $excluded,
		];
	}

	/**
	 * Get payload.
	 *
	 * @param $config
	 *
	 * @return array|array[]
	 */
	private function get_payload( $config ) {
		// Set the default data.
		if ( ! is_array( $config ) || empty( $config ) || ! isset( $config['include_exclude'] ) || ! isset( $config['urls'] ) ) {
			$config = array(
				'include_exclude' => 'exclude',
				'urls'            => '',
			);
		}

		// Map the configuration to the payload.
		return array_map(
			function ( $item ) use ( $config ) {
				return array(
					'url'  => ( 1 === preg_match( '@^https?://.*$@', $item ) ? $item : get_home_url( null, $item ) ),
					'flag' => strtoupper( $config['include_exclude'] ),
				);
			},
			array_filter( preg_split( '/[\r\n]+/', $config['urls'] ) )
		);
	}

	/**
	 * Send event.
	 *
	 * @param $url
	 * @param $value
	 */
	private function send_event( $url, $value ) {
		$this->api_service->request(
			'POST',
			'/plugin/events',
			array( 'content-type' => 'application/json' ),
			wp_json_encode(
				array(
					'source' => 'include-exclude',
					'args'   => array(
						array( 'value' => $value ),
					),
					'url'    => $url,
				)
			),
			0.001,
			null,
			array( 'blocking' => false )
		);
	}
}
