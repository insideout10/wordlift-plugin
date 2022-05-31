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
	function __construct( $upgrader, $plugin ) {
		$this->upgrader = $upgrader;
		$this->plugin   = $plugin;
	}

	function install() {
		try {
			$this->upgrader->install( $this->plugin->get_zip_url() );
		} catch ( \Exception $e ) {

		}
	}

	function activate() {
		activate_plugin( $this->plugin->get_slug() );
	}


}