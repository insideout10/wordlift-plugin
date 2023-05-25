<?php
/**
 * Module Name: Plugin Installer
 * Description: Installs required plugins for WordLift.
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Acf4so\Notices;
use Wordlift\Modules\Common\Installer;
use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WL_ACF4SO_DIR_NAME', __DIR__ );

function __wl_acf4so_load() {
	// Autoloader for plugin itself.
	if ( file_exists( WL_ACF4SO_DIR_NAME . '/vendor/autoload.php' ) ) {
		require WL_ACF4SO_DIR_NAME . '/vendor/autoload.php';
	}

	if ( ! file_exists( ABSPATH . 'wp-admin/includes/plugin-install.php' ) ||
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
	 * @var $installer \Wordlift\Modules\Acf4so\Installer
	 */
	$installer = $container_builder->get( Installer::class );
	$installer->register_hooks();

	/**
	 * @var $notices \Wordlift\Modules\Acf4so\Notices
	 */
	$notices = $container_builder->get( Notices::class );
	$notices->register_hooks();

}

add_action( 'plugins_loaded', '__wl_acf4so_load' );
