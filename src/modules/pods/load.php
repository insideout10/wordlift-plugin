<?php
/**
 * Module Name: Pods
 * Description: Integrates pods framework for displaying schema.org fields
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Pods\Definition;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



function __wl_pods_load() {
	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	$field_definitions = $container_builder->get( Definition::class );
//	$field_definitions->register_fields();

	add_filter( 'pods_api_field_types', function ( $types ) {
		$types[] = 'wlentity';
		return $types;
	} );


}

add_action( 'plugins_loaded', '__wl_pods_load' );

add_action('plugins_loaded', function () {
	require_once __DIR__ . '/PodsField_WlEntity.php';
});
