<?php

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Plugin_Diagnostics\Plugin_Diagnostics_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail out if the feature isn't enabled.
if ( ! apply_filters( 'wl_feature__enable__wordpress-plugin-diagnostics', false ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	return;
}

// Autoloader for dependencies.
if ( file_exists( __DIR__ . '/third-party/vendor/scoper-autoload.php' ) ) {
	require __DIR__ . '/third-party/vendor/scoper-autoload.php';
}

// Autoloader for plugin itself.
if ( file_exists( __DIR__ . '/includes/vendor/autoload.php' ) ) {
	require __DIR__ . '/includes/vendor/autoload.php';
}

function __wl_plugin_diagnostics_push_config() {

	// Get all plugins
	$all_plugins = get_plugins();

	// Get active plugins
	$active_plugins = get_option( 'active_plugins' );

	$payload = array_map(
		function ( $path, $item ) use ( $active_plugins ) {
			return array(
				'name'    => $item['Name'],
				'version' => $item['Version'],
				'active'  => in_array( $path, $active_plugins, true ),
			);
		},
		array_keys( $all_plugins ),
		$all_plugins
	);

	// Load the service.
	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/** @var Plugin_Diagnostics_API $api */
	$api = $container_builder->get( 'Wordlift\Modules\Plugin_Diagnostics\Plugin_Diagnostics_API' );
	$api->update( $payload );
}

/**
 * Fires daily.
 */
add_action( 'wl_daily_cron', '__wl_plugin_diagnostics_push_config', 10, 0 );
