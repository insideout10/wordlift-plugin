<?php

namespace Wordlift\Modules\Common;

class Remote_Plugin implements Plugin {

	private $slug;

	private $url;

	private $name;

	public function __construct( $slug, $name, $url ) {
		$this->slug = $slug;
		$this->url  = $url;
		$this->name = $name;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_zip_url() {
		return $this->url;
	}

	public function is_plugin_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		return array_key_exists( $this->slug, $all_plugins );
	}

	public function is_plugin_activated() {
		return is_plugin_active( $this->slug );
	}

	public function get_name() {
		return $this->name;
	}
}
