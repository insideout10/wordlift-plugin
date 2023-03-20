<?php
/**
 * Module Name: Gardening Kg
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Gardening_Kg\Gardening_Kg_Scheduler;
use Wordlift\Modules\Gardening_Kg\Rest\Gardening_Kg_Controller;

// if ( ! defined( 'ABSPATH' ) ) {
// exit;
// }

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_gardening_kg__load() {

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/**
	 * @var $controller Gardening_Kg_Controller
	 */
	$controller = $container_builder->get( 'Wordlift\Modules\Gardening_Kg\Rest\Gardening_Kg_Controller' );
	$controller->register_hooks();

	/**
	 * @var $scheduler Gardening_Kg_Scheduler
	 */
	$scheduler = $container_builder->get( 'Wordlift\Modules\Gardening_Kg\Gardening_Kg_Scheduler' );
	$scheduler->hook();

}

__wl_gardening_kg__load();
