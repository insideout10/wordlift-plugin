<?php

use Wordlift\Api\Default_Api_Service;

use Wordlift\Dataset\Background\Sync_Background_Process;
use Wordlift\Dataset\Background\Sync_Background_Process_Wpjson_Endpoint;
use Wordlift\Dataset\Sync_Hooks_Entity_Relation;
use Wordlift\Dataset\Sync_Hooks_Wordpress_Ontology;
use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Page;
use Wordlift\Dataset\Sync_Post_Hooks;
use Wordlift\Dataset\Sync_Service;
use Wordlift\Dataset\Sync_Term_Hooks;
use Wordlift\Dataset\Sync_User_Hooks;
use Wordlift\Jsonld\Jsonld_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register the Dataset JSON Endpoint.
// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
if ( apply_filters( 'wl_feature__enable__dataset-ng', false ) ) {

	$sync_object_adapter_factory = new Sync_Object_Adapter_Factory();
	$sync_service                = new Sync_Service( Default_Api_Service::get_instance(), $sync_object_adapter_factory, Jsonld_Service::get_instance(), Wordlift_Entity_Service::get_instance() );
	new Sync_Post_Hooks( $sync_service, $sync_object_adapter_factory );
	new Sync_User_Hooks( $sync_service );

	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	if ( apply_filters( 'wl_feature__enable__no-vocabulary-terms', false ) ) {
		new Sync_Term_Hooks( $sync_service, $sync_object_adapter_factory );
	}
	/**
	 * @since 3.28.0
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1186
	 */
	new Sync_Hooks_Entity_Relation( Wordlift_Entity_Service::get_instance() );

	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	if ( apply_filters( 'wl_feature__enable__wordpress-ontology', false ) ) {
		new Sync_Hooks_Wordpress_Ontology();
	}

	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	if ( apply_filters( 'wl_feature__enable__sync-background', false ) ) {
		// Set up the sync background process.
		$sync_background_process = new Sync_Background_Process( $sync_service, $sync_object_adapter_factory );
		new Sync_Background_Process_Wpjson_Endpoint( $sync_background_process );
		new Sync_Page();
	}
}
