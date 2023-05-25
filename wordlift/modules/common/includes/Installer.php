<?php

namespace Wordlift\Modules\Common;

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
	 * @param $upgrader Plugin_Upgrader
	 * @param $plugin Plugin
	 */
	public function __construct( Plugin_Upgrader $upgrader, Plugin $plugin ) {
		$this->upgrader = $upgrader;
		$this->plugin   = $plugin;
	}

	public function install() {

		if ( $this->plugin->is_plugin_installed() ) {
			return;
		}

		try {
			wp_cache_flush();
			$this->upgrader->install( $this->plugin->get_zip_url() );
		} catch ( \Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Error caught when installing plugin ' . $this->plugin->get_slug() . ' error: ' . $e->getMessage() );
		}
	}

	public function activate() {
		if ( $this->plugin->is_plugin_activated() ) {
			return;
		}
		activate_plugin( $this->plugin->get_slug() );
	}

	public function install_and_activate_on_entity_type_change( $new_value ) {
		if ( ! $new_value ) {
			return;
		}
		$this->install_and_activate();
	}

	public function install_and_activate() {
		ob_start();
		$this->install();
		$this->activate();
		ob_end_clean();
	}

	public function admin_ajax_install_and_activate() {

		$this->install_and_activate();

		if ( $this->plugin->is_plugin_installed() && $this->plugin->is_plugin_activated() ) {
			wp_send_json_success( null, 200 );
		}

		wp_send_json_error( null, 400 );
	}

	public function register_hooks() {
		add_action( 'wl_feature__change__entity-types-professional', array( $this, 'install_and_activate_on_entity_type_change' ), 10 );
		add_action( 'wl_feature__change__entity-types-business', array( $this, 'install_and_activate_on_entity_type_change' ), 10 );
		add_action( "wp_ajax_wl_install_and_activate_{$this->plugin->get_name()}", array( $this, 'admin_ajax_install_and_activate' ) );
		add_action( "wl_install_and_activate_{$this->plugin->get_name()}", array( $this, 'install_and_activate' ) );
	}

}
