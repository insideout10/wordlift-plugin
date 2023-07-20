<?php

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_API;

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

/**
 * Include exclude get payload.
 *
 * @param $config
 *
 * @return array|array[]
 */
function __wl_include_exclude_get_payload( $config ) {
	// Set the default data.
	if ( ! is_array( $config ) || empty( $config ) || ! isset( $config['include_exclude'] ) || ! isset( $config['urls'] ) ) {
		$config = array(
			'include_exclude' => 'exclude',
			'urls'            => '',
		);
	}

	// Map the configuration to the payload.
	return array_map(
		function ( $item ) use ( $config ) {
			return array(
				'url'  => ( 1 === preg_match( '@^https?://.*$@', $item ) ? $item : get_home_url( null, $item ) ),
				'flag' => strtoupper( $config['include_exclude'] ),
			);
		},
		array_filter( preg_split( '/[\r\n]+/', $config['urls'] ) )
	);
}

/**
 * Include exclude load service.
 *
 * @return object|null
 *
 * @throws Exception If the application fails to load the services configuration file or if the URL cannot be processed.
 */
function __wl_include_exclude_load_service() {
	// Load the service.
	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	return $container_builder->get( 'Wordlift\Modules\Include_Exclude_Push_Config\Include_Exclude_API' );
}

/**
 * Save old config.
 */
function __wl_save_old_config() {
	// Get the current configuration.
	$config = get_option( 'wl_exclude_include_urls_settings', array() );

	// Save the current configuration to another option.
	update_option( 'wl_old_exclude_include_urls_settings', $config );
}

/**
 * Include exclude push config.
 *
 * @throws Exception If the application fails to load the services configuration file or if the URL cannot be processed.
 */
function __wl_include_exclude_push_config() {
	// Get the configuration.
	$config = get_option( 'wl_exclude_include_urls_settings', array() );

	$payload = __wl_include_exclude_get_payload( $config );

	/** @var Include_Exclude_API $api */
	$api = __wl_include_exclude_load_service();
	$api->update( $payload );
}

/**
 * Include exclude event update.
 *
 * @throws Exception If the application fails to load the services configuration file or if the URL cannot be processed.
 */
function __wl_include_exclude_event_update() {
	// Get the configurations.
	$config     = get_option( 'wl_exclude_include_urls_settings', array() );
	$old_config = get_option( 'wl_exclude_include_urls_settings_old', array() );

	// Get the payload for both new and old values:
	$payload_new = __wl_include_exclude_get_payload( $config );
	$payload_old = __wl_include_exclude_get_payload( $old_config );

	// Extract URLs from payloads.
	$urls_new = array_column( $payload_new, 'url' );
	$urls_old = array_column( $payload_old, 'url' );

	// Find added and removed URLs.
	$urls_added   = array_diff( $urls_new, $urls_old );
	$urls_removed = array_diff( $urls_old, $urls_new );

	$included = $urls_added;
	$excluded = $urls_removed;

	if ( 'excluded' === strtolower( $config['include_exclude'] ) ) {
		$included = $urls_removed;
		$excluded = $urls_added;
	}

	/** @var Include_Exclude_API $api */
	$api = __wl_include_exclude_load_service();

	// Call API method for each URL.
	foreach ( $included as $url ) {
		$api->send_event( $url, 'include' );
	}
	foreach ( $excluded as $url ) {
		$api->send_event( $url, 'exclude' );
	}
}

/**
 * Fires after the value of a specific option has been successfully updated.
 */
add_action( 'update_option_wl_exclude_include_urls_settings', '__wl_save_old_config', 5, 0 );
add_action( 'update_option_wl_exclude_include_urls_settings', '__wl_include_exclude_push_config', 10, 0 );
add_action( 'update_option_wl_exclude_include_urls_settings', '__wl_include_exclude_event_update', 15, 0 );

/**
 * Fires daily.
 */
add_action( 'wl_daily_cron', '__wl_include_exclude_push_config', 10, 0 );
