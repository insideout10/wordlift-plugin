<?php

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

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

// Register the Dataset JSON Endpoint. The `$api_service` variable must be defined in the calling file (wordlift.php).
if ( apply_filters( 'wl_feature__enable__dataset-ng', false ) ) {

    // Experimentl chnges for 1496 done by Nishit
    write_log( "In index.php of dataset Nishit: " );
    // Experimentl chnges ends

	$sync_object_adapter_factory = new Sync_Object_Adapter_Factory();
	$sync_service                = new Sync_Service( $api_service, $sync_object_adapter_factory, Jsonld_Service::get_instance(), Wordlift_Entity_Service::get_instance() );
	new Sync_Post_Hooks( $sync_service, $sync_object_adapter_factory );
	new Sync_User_Hooks( $sync_service );

    // Experimentl chnges for 1496 done by Nishit
    write_log( "After calling Sync_Post_Hooks & Sync_User_Hooks in index.php of dataset Nishit: " );
    // Experimentl chnges ends

	if ( apply_filters( 'wl_feature__enable__no-vocabulary-terms', false ) ) {
		new Sync_Term_Hooks( $sync_service, $sync_object_adapter_factory );
        // Experimentl chnges for 1496 done by Nishit
        write_log( "After calling Sync_Term_Hooks in index.php of dataset Nishit: " );
        // Experimentl chnges ends
	}
	/**
	 * @since 3.28.0
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1186
	 */
	new Sync_Hooks_Entity_Relation( Wordlift_Entity_Service::get_instance() );

    // Experimentl chnges for 1496 done by Nishit
    write_log( "After calling Sync_Hooks_Entity_Relation in index.php of dataset Nishit: " );
    // Experimentl chnges ends

	if ( apply_filters( 'wl_feature__enable__wordpress-ontology', false ) ) {
		new Sync_Hooks_Wordpress_Ontology();
        // Experimentl chnges for 1496 done by Nishit
        write_log( "After calling Sync_Hooks_Wordpress_Ontology in index.php of dataset Nishit: " );
        // Experimentl chnges ends
	}

	if ( apply_filters( 'wl_feature__enable__sync-background', false ) ) {
		// Set up the sync background process.
		$sync_background_process = new Sync_Background_Process( $sync_service, $sync_object_adapter_factory );
		new Sync_Background_Process_Wpjson_Endpoint( $sync_background_process );
		new Sync_Page();
        // Experimentl chnges for 1496 done by Nishit
        write_log( "After calling Sync_Page in index.php of dataset Nishit: " );
        // Experimentl chnges ends
	}

}
