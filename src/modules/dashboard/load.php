<?php
/**
 * Module Name: Dashboard
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Dashboard\Plugin_App;
use Wordlift\Modules\Dashboard\Synchronization\Rest_Controller;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_dashboard__load() {

	// Dashboard is available only for Food Kg and Gardening Kg atm
	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	if ( ! apply_filters( 'wl_feature__enable__food-kg', false ) && ! apply_filters( 'wl_feature__enable__gardening-kg', false ) ) {
		return;
	}

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/**
	 * @var $rest_controller Rest_Controller
	 */
	$rest_controller = $container_builder->get( 'Wordlift\Modules\Dashboard\Synchronization\Rest_Controller' );
	$rest_controller->register_hooks();

	/**
	 * @var $synchronization_service Synchronization_Service
	 */
	$synchronization_service = $container_builder->get( 'Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service' );
	add_action(
		'init',
		function () use ( $synchronization_service ) {
			$synchronization_service->register_hooks();
		}
	);

	/**
	 * @var $plugin_app Plugin_App
	 */
	$plugin_app = $container_builder->get( 'Wordlift\Modules\Dashboard\Plugin_App' );
	$plugin_app->register_hooks();

	/** Admin Menu */
	add_action(
		'admin_menu',
		function () {
			add_submenu_page( 'wl_admin_menu', __( 'Dashboard', 'wordlift' ), __( 'Dashboard', 'wordlift' ), 'manage_options', 'wl_admin_menu', '_wl_dashboard__main' );
		},
		9
	);

}

__wl_dashboard__load();
