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

		check_ajax_referer( $this->get_nonce_action() );

		// Installing needs `install_plugins`, which nobody has with DISALLOW_FILE_MODS or on a multisite subsite, so only
		// require it when the plugin isn't installed yet.
		$can_install = $this->plugin->is_plugin_installed() || current_user_can( 'install_plugins' );
		if ( ! $can_install || ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( null, 403 );
		}

		$this->install_and_activate();

		if ( $this->plugin->is_plugin_installed() && $this->plugin->is_plugin_activated() ) {
			wp_send_json_success( null, 200 );
		}

		wp_send_json_error( null, 400 );
	}

	/**
	 * The nonce action expected by the `wl_install_and_activate_{name}` AJAX call.
	 *
	 * @return string
	 */
	public function get_nonce_action() {
		return "wl_install_and_activate_{$this->plugin->get_name()}";
	}

	public function register_hooks() {
		add_action( 'wl_feature__change__entity-types-professional', array( $this, 'install_and_activate_on_entity_type_change' ), 10 );
		add_action( 'wl_feature__change__entity-types-business', array( $this, 'install_and_activate_on_entity_type_change' ), 10 );
		add_action( "wp_ajax_{$this->get_nonce_action()}", array( $this, 'admin_ajax_install_and_activate' ) );
		add_action( "wl_install_and_activate_{$this->plugin->get_name()}", array( $this, 'install_and_activate' ) );
	}

}
