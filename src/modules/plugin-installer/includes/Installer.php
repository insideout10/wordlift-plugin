<?php

namespace Wordlift\Modules\Plugin_Installer;


use Plugin_Upgrader;

class Installer {

	/**
	 * @var Plugin
	 */
	private $plugin;
	/**
	 * @var Plugin_Upgrader
	 */
	private $upgrader;

	/**
	 * @param $plugin Plugin
	 * @param $upgrader Plugin_Upgrader
	 */
	function __construct( Plugin_Upgrader $upgrader, Plugin $plugin ) {
		$this->upgrader = $upgrader;
		$this->plugin   = $plugin;
	}

	function install() {

		if ( $this->plugin->is_plugin_installed() ) {
			return;
		}

		try {
			$this->upgrader->install( $this->plugin->get_zip_url() );
		} catch ( \Exception $e ) {
			error_log( "Error caught when installing plugin " . $this->plugin->get_slug() . " error: " . $e->getMessage() );
		}
	}

	function activate() {
		if ( $this->plugin->is_plugin_activated() ) {
			return;
		}
		activate_plugin( $this->plugin->get_slug() );
	}

}
