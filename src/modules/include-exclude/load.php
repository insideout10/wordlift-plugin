<?php
/**
 * Module Name: Include Exclude Module
 * Description: Lets you exclude or include selective URLs from WordLift's JSON-LD
 * Experimental: Yes
 *
 * @since   1.0.0
 * @package wordlift
 */

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This module loads very early since it needs to tell WLP whether to load itself or not. Therefore we need to load the
 * feature settings by ourselves.
 */
$wl_features = get_option( '_wl_features', array() );
if ( ! apply_filters( 'wl_feature__enable__include-exclude', isset( $wl_features['include-exclude'] ) && $wl_features['include-exclude'] ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	return;
}

/**
 * Load Include Exclude Module.
 *
 * @return void
 */
function __wl_include_exclude__load() {

	// Autoloader for plugin itself.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	$enabled = $container_builder->get( 'Wordlift\Modules\Include_Exclude\Plugin_Enabled' );
	$enabled->register_hooks();

	if ( apply_filters( 'wl_is_enabled', true ) ) {
		$settings = $container_builder->get( 'Wordlift\Modules\Include_Exclude\Admin\Settings' );
		$settings->register_hooks();

		$api = $container_builder->get( 'Wordlift\Modules\Include_Exclude\API' );
		$api->register_hooks();

		$jsonld_interceptor = $container_builder->get( 'Wordlift\Modules\Include_Exclude\Jsonld_Interceptor' );
		$jsonld_interceptor->register_hooks();
	}

}

__wl_include_exclude__load();
