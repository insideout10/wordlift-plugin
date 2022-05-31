<?php
/**
 * Module Name: Plugin Installer
 * Description: Installs required plugins for WordLift.
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */


use Wordlift\Modules\Plugin_Installer\Installer;
use Wordlift\Modules\Plugin_Installer_Dependencies\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Plugin_Installer_Dependencies\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Plugin_Installer_Dependencies\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WL_PLUGIN_INSTALLER_FILE', __FILE__ );
define( 'WL_PLUGIN_INSTALLER_DIR_PATH', dirname( WL_PLUGIN_INSTALLER_FILE ) );

function __wl_plugin_installer_load() {

	// Autoloader for dependencies.
	if ( file_exists( WL_PLUGIN_INSTALLER_DIR_PATH . '/third-party/vendor/scoper-autoload.php' ) ) {
		require WL_PLUGIN_INSTALLER_DIR_PATH . '/third-party/vendor/scoper-autoload.php';
	}

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
	 * @var $installer Installer
	 */
	$installer = $container_builder->get( Installer::class );
	$install_and_activate = function ($new_value, $old_value)  use ($installer) {

		if ( ! $new_value ) {
			return;
		}
		$installer->install();
		$installer->activate();
	};

	$install_and_activate(true, true);
	add_action( "wl_feature__change__entity-types-professional", $install_and_activate, 10, 2 );
	add_action( "wl_feature__change__entity-types-business", $install_and_activate, 10, 2 );
	
}

add_action( 'plugins_loaded', '__wl_plugin_installer_load' );

