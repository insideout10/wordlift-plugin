<?php

use Wordlift\Modules\Common\Symfony\Component\Config\FileLocator;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wordlift\Modules\No_Editor_Analysis\Analyzer_Request_Data_Filter;
use Wordlift\Modules\No_Editor_Analysis\V2_Analysis_Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail out if the feature isn't enabled.
if ( ! apply_filters( 'wl_feature__enable__no-editor-analysis', true ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	return;
}

function __wl_no_editor_analysis__load() {

	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	// Load the service.
	$container_builder = new ContainerBuilder();
	$loader            = new YamlFileLoader( $container_builder, new FileLocator( __DIR__ ) );
	$loader->load( 'services.yml' );
	$container_builder->compile();

	/** @var Analyzer_Request_Data_Filter $request_data_Filter */
	$request_data_Filter = $container_builder->get( 'Wordlift\Modules\No_Editor_Analysis\Analyzer_Request_Data_Filter' );
	$request_data_Filter->register_hooks();

	/** @var V2_Analysis_Client $v2_analysis_client */
	$v2_analysis_client = $container_builder->get( 'Wordlift\Modules\No_Editor_Analysis\V2_Analysis_Client' );
	$v2_analysis_client->register_hooks();

}

__wl_no_editor_analysis__load();

