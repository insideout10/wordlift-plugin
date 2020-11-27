<?php

use Wordlift\Dataset\Sync_Hooks_Entity_Relation;
use Wordlift\Dataset\Sync_Page;
use Wordlift\Dataset\Sync_Service;
use Wordlift\Dataset\Sync_Wpjson_Endpoint;
use Wordlift\Jsonld\Jsonld_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register the Dataset JSON Endpoint. The `$api_service` variable must be defined in the calling file (wordlift.php).
if ( apply_filters( 'wl_feature__enable__dataset-ng', false ) ) {
	/*
	 * Add Composer Autoload with Mozart support.
	 *
	 * @since 3.27.6
	 */
//	require dirname( dirname( dirname( __FILE__ ) ) ) . '/vendor/autoload.php';

	$sync_service = new Sync_Service( $api_service, Jsonld_Service::get_instance() );
	new Sync_Wpjson_Endpoint( $sync_service );
	new Sync_Page();

	/**
	 * @since 3.28.0
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1186
	 */
	new Sync_Hooks_Entity_Relation( Wordlift_Entity_Service::get_instance() );

}
