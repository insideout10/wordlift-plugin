<?php

namespace Wordlift\Vocabulary\Pages;

use Wordlift\Scripts\Scripts_Helper;
use Wordlift\Vocabulary\Api\Api_Config;

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
			__( 'Match Terms', 'wordlift' ),
			__( 'Match Terms', 'wordlift' ),
			'manage_options',
			'wl-vocabulary-match-terms',
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
		$settings['baseUrl'] = get_rest_url( null, Api_Config::REST_NAMESPACE );
		$settings['nonce']   = wp_create_nonce( 'wp_rest' );

		return $settings;
	}


	public function submenu_page_callback() {

		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-vocabulary-reconcile-script',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			true
		);


		wp_enqueue_style( 'wl-vocabulary-reconcile-script',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . "js/dist/vocabulary.full.css" );
		wp_localize_script( 'wl-vocabulary-reconcile-script', '_wlCmKgConfig', $this->get_settings() );
		echo "<div id='wl_cmkg_reconcile_progress' class='wrap'></div>";
		echo "<div id='wl_cmkg_table' class='wrap'></div>";
	}

}