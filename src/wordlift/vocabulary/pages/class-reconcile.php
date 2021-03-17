<?php

namespace Wordlift\Vocabulary\Pages;

use Cafemedia_Knowledge_Graph\Api\Api_Config;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Reconcile {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu', ) );

	}

	public function admin_menu() {
		add_submenu_page(
			'wl_admin_menu',
			__( 'Reconcile Tags', 'wordlift-cmkg' ),
			__( 'Reconcile Tags', 'wordlift-cmkg' ),
			'manage_options',
			'wl-cmkg-reconcile-tags-new',
			array( $this, 'submenu_page_callback' )
		);
	}


	public function get_settings() {
		// Create ui settings array to be used by js client.
		$settings            = array();
		$settings['restUrl'] = get_rest_url(
			null,
			Api_Config::REST_NAMESPACE . '/tags'
		);
		$settings['baseUrl'] = get_rest_url(null, Api_Config::REST_NAMESPACE);
		$settings['nonce']   = wp_create_nonce( 'wp_rest' );

		return $settings;
	}


	public function submenu_page_callback() {

		wp_enqueue_script( 'wl-cmkg-reconcile-script',
			plugin_dir_url( dirname( __DIR__ ) ) . "js/dist/bundle.full.js" );
		wp_enqueue_style( 'wl-cmkg-reconcile-script',
			plugin_dir_url( dirname( __DIR__ ) ) . "js/dist/bundle.full.css" );
		wp_localize_script( 'wl-cmkg-reconcile-script', '_wlCmKgConfig', $this->get_settings() );
		echo "<div id='wl_cmkg_reconcile_progress' class='wrap'></div>";
		echo "<div id='wl_cmkg_table' class='wrap'></div>";
	}

}