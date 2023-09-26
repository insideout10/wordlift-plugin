<?php

namespace Wordlift\Modules\Include_Exclude_Push_Config;

use Wordlift\Api\Api_Service;
use Wordlift\Modules\Include_Exclude\Configuration;
use Wordlift_Configuration_Service;

class Include_Exclude_Default_Config_Installer {

	/**
	 * @var Api_Service
	 */
	private $api_service;

	/**
	 * @param Api_Service $api_service
	 */
	public function __construct( $api_service ) {
		$this->api_service = $api_service;
	}

	public function register_hooks() {
		add_action( 'init', array( $this, 'handle_init' ) );
		add_action(
			'update_option_wl_exclude_include_urls_settings',
			array(
				$this,
				'handle_update_option_wl_exclude_include_urls_settings',
			),
			10,
			0
		);
	}

	public function handle_init() {
		// Send the default configuration only if it hasn't been sent already.
		if ( ! get_option( '_wl_include_exclude_default_sent', false ) ) {
			$response = $this->send();
			if ( $response->is_success() ) {
				update_option( '_wl_include_exclude_default_sent', true, true );
			}
		}
	}

	public function handle_update_option_wl_exclude_include_urls_settings() {
		// Delete the option and re-init. We prefer this way so that the _init can handle eventual retries.
		delete_option( '_wl_include_exclude_default_sent' );
		$this->handle_init();
	}

	public function send() {
		// I prefer to instantiate the `Wordlift_Configuration_Service` and `Configuration` here
		// in order to avoid preemptive unused instantiation.
		$key                        = rawurlencode( Wordlift_Configuration_Service::get_instance()->get_key() );
		$wp_admin                   = rawurlencode( untrailingslashit( get_admin_url() ) );
		$wp_json                    = rawurlencode( untrailingslashit( get_rest_url() ) );
		$wp_include_exclude_default = rawurlencode( Configuration::get_instance()->get_default() );

		return $this->api_service->request(
			'PUT',
			'/accounts/wordpress-configuration?'
			. "key=$key"
			. "&wpAdmin=$wp_admin"
			. "&wpJson=$wp_json"
			. "&wp_include_exclude_default=$wp_include_exclude_default"
		);
	}

}
