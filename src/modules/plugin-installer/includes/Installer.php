<?php

namespace Wordlift\Modules\Plugin_Installer;


class Installer {

	/**
	 * @var Plugin
	 */
	private $plugin;
	/**
	 * @var \Plugin_Upgrader
	 */
	private $upgrader;

	/**
	 * @param $plugin Plugin
	 * @param $upgrader \Plugin_Upgrader
	 */
	function __construct( \Plugin_Upgrader $upgrader, Plugin $plugin ) {
		$this->upgrader = $upgrader;
		$this->plugin   = $plugin;
	}

	function install() {
		try {
			$this->upgrader->install( $this->plugin->get_zip_url() );
		} catch ( \Exception $e ) {
			error_log( "Error caught when installing plugin " . $this->plugin->get_slug() . " error: " . $e->getMessage() );
		}
	}

	function activate() {
		var_dump( activate_plugin( $this->plugin->get_slug() ) );
	}


}