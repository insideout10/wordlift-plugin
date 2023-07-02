<?php
/**
 * Pages: Sync Mappings page.
 *
 * Display the sync mappings page.
 *
 * @since 3.24.0
 * @package Wordlift
 * @subpackage Wordlift\Mappings\Pages
 */

namespace Wordlift\Mappings\Pages;

use Wordlift\Mappings\Mappings_REST_Controller;
use Wordlift\Scripts\Scripts_Helper;
use Wordlift_Admin_Page;

/**
 * Define the Wordlift_Admin_Sync_Mappings_Page class.
 *
 * @since 3.24.0
 */
class Admin_Mappings_Page extends Wordlift_Admin_Page {

	/**
	 * Provides script and js global values used by react component.
	 */
	public static function provide_ui_dependencies() {
		// Create ui settings array to be used by js client.
		$mapping_settings                          = array();
		$mapping_settings['rest_url']              = get_rest_url(
			null,
			WL_REST_ROUTE_DEFAULT_NAMESPACE . Mappings_REST_Controller::MAPPINGS_NAMESPACE
		);
		$mapping_settings['wl_mapping_nonce']      = wp_create_nonce( 'wp_rest' );
		$mapping_settings['wl_edit_mapping_nonce'] = wp_create_nonce( 'wl-edit-mapping-nonce' );
		wp_localize_script( 'wl-mappings-admin', 'wlMappingsConfig', $mapping_settings );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_page_title() {

		return __( 'Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_title() {

		return __( 'Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_slug() {

		return 'wl_mappings_admin';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_partial_name() {

		return 'wordlift-admin-mappings-admin.php';
	}

	public function enqueue_scripts() {

		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-mappings-admin',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/mappings',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			true
		);

		wp_enqueue_style(
			'wl-mappings-admin',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/mappings.css',
			array(),
			WORDLIFT_VERSION
		);
		self::provide_ui_dependencies();
	}

}
