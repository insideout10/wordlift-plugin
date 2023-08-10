<?php

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_API;
use Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_Default_Config_Installer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail out if the feature isn't enabled.
if ( ! apply_filters( 'wl_feature__enable__include-exclude', false ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
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

function __wl_include_exclude_push_config() {
	// Get the configuration.
	$config = get_option( 'wl_exclude_include_urls_settings', array() );

	// Set the default data.
	if ( ! is_array( $config ) || empty( $config ) || ! isset( $config['include_exclude'] ) || ! isset( $config['urls'] ) ) {
		$config = get_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'exclude',
				'urls'            => '',
			)
		);
	}

	// Map the configuration to the payload.
	$payload = array_map(
		function ( $item ) use ( $config ) {
			return array(
				'url'  =>
					( 1 === preg_match( '@^https?://.*$@', $item ) ? $item : get_home_url( null, $item ) ),
				'flag' => strtoupper( $config['include_exclude'] ),
			);
		},
		array_filter( preg_split( '/[\r\n]+/', $config['urls'] ) )
	);

	// Load the service.
	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/** @var Include_Exclude_API $api */
	$api = $container_builder->get( 'Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_API' );
	$api->update( $payload );
}

function __wl_include_exclude_push_config__plugins_loaded() {
	// Load the service.
	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/** @var Include_Exclude_Default_Config_Installer $installer */
	$installer = $container_builder->get( 'Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_Default_Config_Installer' );
	$installer->register_hooks();
}

/**
 * Fires after the value of a specific option has been successfully updated.
 */
add_action( 'update_option_wl_exclude_include_urls_settings', '__wl_include_exclude_push_config', 10, 0 );

/**
 * Fires daily.
 */
add_action( 'wl_daily_cron', '__wl_include_exclude_push_config', 10, 0 );

add_action( 'plugins_loaded', '__wl_include_exclude_push_config__plugins_loaded', 10, 0 );
