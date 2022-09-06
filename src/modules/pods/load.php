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

	pods_register_related_object( 'wlentity', 'WordLift Entity', array() );
}

//add_filter( 'pods_field_pick_data_ajax', function ( $arg1, $field_name, $arg2, $field, $pod, $id) {
//
//	var_dump($field_name);
//	var_dump($pod);
//	var_dump($field);
//
//} , 10, 6 );


add_filter('pods_form_ui_field_pick_ajax', function ( $item, $name, $value, $field_options ) {
	return isset( $field_options['pick_object'] ) && is_string( $field_options['pick_object'] ) &&
	       'wlentity' === $field_options['pick_object'];
}, 10, 4);

add_action( 'plugins_loaded', '__wl_pods_load' );


