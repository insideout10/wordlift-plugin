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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_dashboard__load() {

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/**
	 * @var $controller \Wordlift\Modules\Dashboard\Bootstrap\Controller
	 */
	$controller = $container_builder->get( 'Wordlift\Modules\Dashboard\Bootstrap\Controller' );
	$controller->register_hooks();

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
		}
	);

}

__wl_dashboard__load();
