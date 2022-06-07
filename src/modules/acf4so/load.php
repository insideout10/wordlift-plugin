<?php
/**
 * Module Name: Plugin Installer
 * Description: Installs required plugins for WordLift.
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */


use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Plugin_Installer\Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WL_PLUGIN_INSTALLER_DIR_PATH', dirname( __FILE__ ) );

function __wl_plugin_installer_load() {
	// Autoloader for plugin itself.
	if ( file_exists( WL_PLUGIN_INSTALLER_DIR_PATH . '/includes/vendor/autoload.php' ) ) {
		require WL_PLUGIN_INSTALLER_DIR_PATH . '/includes/vendor/autoload.php';
	}

	if (  ! file_exists(ABSPATH . 'wp-admin/includes/plugin-install.php') ||
	      ! file_exists( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );

	$container_builder->compile();


	/**
	 * @var $module Module
	 */
	$module = $container_builder->get( Module::class );
	$module->register_hooks();
	
}

add_action( 'plugins_loaded', '__wl_plugin_installer_load' );

