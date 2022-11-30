<?php
/**
 * This file contains the settings class.
 *
 * @package Wordlift
 */

namespace Wordlift\Modules\Include_Exclude\Admin;

/**
 * Define the Settings class.
 *
 * @package Wordlift
 */
class Settings {

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		if ( is_admin() ) {
			add_action(
				'plugins_loaded',
				function () {
					add_action( 'admin_menu', array( $this, 'register_sub_menu' ) );
					add_action( 'admin_init', array( $this, 'register_setting' ) );
				}
			);
		}
	}

	/**
	 * Register submenu.
	 */
	public function register_sub_menu() {
		add_submenu_page(
			'wl_admin_menu',
			__( 'Exclude Include URLs', 'wordlift' ),
			__( 'Exclude Include URLs', 'wordlift' ),
			'manage_options',
			'wl_exclude_include_urls_menu',
			array( $this, 'submenu_page_callback' )
		);
	}

	/**
	 * Register settings.
	 */
	public function register_setting() {
		register_setting( 'wl_exclude_include_urls_settings_group', 'wl_exclude_include_urls_settings' );
		add_settings_section(
			'wl_exclude_include_urls_settings_section_0',
			__( 'URL filter type, list of URLs', 'wordlift' ),
			array( $this, 'settings_section_callback' ),
			'wl_exclude_include_urls_settings_page'
		);

		add_settings_field(
			'wl_exclude_include_urls_field_0',
			__( 'URLs filter type', 'wordlift' ),
			array( $this, 'settings_field_0_render' ),
			'wl_exclude_include_urls_settings_page',
			'wl_exclude_include_urls_settings_section_0'
		);

		add_settings_field(
			'wl_exclude_include_urls_field_1',
			__( 'List of URLs', 'wordlift' ),
			array( $this, 'settings_field_1_render' ),
			'wl_exclude_include_urls_settings_page',
			'wl_exclude_include_urls_settings_section_0'
		);
	}

	public function settings_section_callback() {
		echo esc_html__( "Choose a filter type, and add list of URLs to selective active from WordLift's JSON-LD", 'wordlift' );
	}

	/**
	 * Callback function for settings field rendering.
	 */
	public function settings_field_0_render() {
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$options = get_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'exclude',
				'urls'            => '',
		) ); // phpcs:ignore
		include_once plugin_dir_path( __FILE__ ) . 'partials/field-0.php';
	}

	/**
	 * Callback function for settings field rendering.
	 */
	public function settings_field_1_render() {
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$options = get_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => true,
				'urls'            => '',
		) ); // phpcs:ignore
		include_once plugin_dir_path( __FILE__ ) . 'partials/field-1.php';
	}

	/**
	 * Callback function for Exclude Include URLs submenu page.
	 */
	public function submenu_page_callback() {
		include_once plugin_dir_path( __FILE__ ) . 'partials/exclude-include-urls-page.php';
	}

}
