<?php

use Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_API;
use Wordlift\Modules\Include_Exclude_Push_Config\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Include_Exclude_Push_Config\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Include_Exclude_Push_Config\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail out if the feature isn't enabled.
if ( ! apply_filters( 'wl_feature__enable__include-exclude', false ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	return;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

function __wl_include_exclude_push_config() {
	// Get the configuration and bail out if empty.
	$config = get_option( 'wl_exclude_include_urls_settings', array() );
	if ( ! is_array( $config ) || empty( $config ) || ! isset( $config['include_exclude'] ) || ! isset( $config['urls'] ) ) {
		return;
	}

	// Map the configuration to the payload.
	$payload = array_map( function ( $item ) use ( $config ) {
		return array(
			'url'  => $item,
			'flag' => strtoupper( $config['include_exclude'] )
		);
	}, $config['url'] );

	// Load the service.
	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/** @var Include_Exclude_API $api */
	$api = $container_builder->get( 'Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_API' );
	$api->update( $payload );

}

/**
 * Fires after the value of a specific option has been successfully updated.
 */
add_action( 'update_option_wl_exclude_include_urls_settings', '__wl_include_exclude_push_config', 10, 0 );

/**
 * Fires daily.
 */
add_action( 'wl_daily_cron', '__wl_include_exclude_push_config', 10, 0 );
