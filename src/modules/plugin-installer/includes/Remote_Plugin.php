<?php

namespace Wordlift\Modules\Plugin_Installer;

use Wordlift\Modules\Plugin_Installer_Dependencies\Symfony\Component\Config\Definition\Exception\Exception;

class Remote_Plugin implements Plugin {

	private $slug;

	private $url;

	public function __construct( $slug, $url ) {
		$this->slug = $slug;
		$this->url  = $url;
	}

	function get_slug() {
		return $this->slug;
	}

	function get_zip_url() {
		// @TODO change static url.
		return 'https://wordlift.io/wp-content/uploads/advanced-custom-fields-for-schema-org.zip?v=1.8.0';
		$result = wp_remote_get( $this->url );
		$data   = json_decode( wp_remote_retrieve_body( $result ), true );
		if ( $result instanceof \WP_Error || ! is_array( $data )
		     || ! array_key_exists( 'package', $data )
		     || ! is_string( $data['package'] ) ) {
			throw new Exception( "Unable to get zip url" );
		}
		return $data['package'];
	}

	function is_plugin_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		return array_key_exists( $this->slug, $all_plugins );
	}

	function is_plugin_activated() {
		return is_plugin_active( $this->slug );
	}
}