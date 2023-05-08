<?php

/**
 * Module Name: App
 * Experimental: No
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\App\Plugin_App;
use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

if ( ! defined( 'WL_ANGULAR_APP_URL' ) ) {
	define( 'WL_ANGULAR_APP_URL', esc_url( plugin_dir_url( __DIR__ ) . 'app/app/iframe.html' ) );
}

if ( ! defined( 'WL_ANGULAR_APP_SCRIPT_HANDLE' ) ) {
	define( 'WL_ANGULAR_APP_SCRIPT_HANDLE', 'wl-angular-app' );
}

// Load the rest of the module only on admin calls.
if ( ! is_admin() ) {
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
 * @var $app Plugin_App
 */
$app = $container_builder->get( Plugin_App::class );

$app->register_handle();
