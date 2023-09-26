<?php
/**
 * Module Name: Redeem Code
 * Experimental: Yes
 *
 * @since   3.46.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Redeem_Code\Rest_Controller;

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_redeem_code() {
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
	$rest_controller = $container_builder->get( 'Wordlift\Modules\Redeem_Code\Rest_Controller' );
	$rest_controller->register_hooks();
}

__wl_redeem_code();
