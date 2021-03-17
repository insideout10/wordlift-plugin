<?php
/**
 * Plugin Name:     WordLift CafeMedia Knowledge Graph
 * Plugin URI:      https://wordlift.io
 * Description:     WordLift CafeMedia Knowledge Graph.
 * Author:          WordLift
 * Author URI:      https://wordlift.io
 * Text Domain:     wordlift-longtail
 * Domain Path:     /languages
 * Version:         1.2.0
 *
 * @package         Wordlift_Cafemedia_Knowledge_Graph
 */

use Cafemedia_Knowledge_Graph\Analysis_Background_Service;
use Cafemedia_Knowledge_Graph\Analysis_Service;
use Cafemedia_Knowledge_Graph\Api\Background_Analysis_Endpoint;
use Cafemedia_Knowledge_Graph\Api\Entity_Rest_Endpoint;
use Cafemedia_Knowledge_Graph\Api\Reconcile_Progress_Endpoint;
use Cafemedia_Knowledge_Graph\Api\Tag_Rest_Endpoint;
use Cafemedia_Knowledge_Graph\Hooks\Tag_Created_Hook;
use Cafemedia_Knowledge_Graph\Options_Cache;
use Cafemedia_Knowledge_Graph\Pages\Reconcile;
use Cafemedia_Knowledge_Graph\Jsonld\Post_Jsonld;
use Wordlift\Api\Default_Api_Service;
use Wordlift\Api\User_Agent;
use Wordlift\Cache\Ttl_Cache;


spl_autoload_register( function ( $class_name ) {

	// Bail out if these are not our classes.
	if ( 0 !== strpos( $class_name, 'Cafemedia_Knowledge_Graph\\' ) ) {
		return false;
	}

	$class_name_lc = strtolower( str_replace( '_', '-', $class_name ) );

	preg_match( '|^cafemedia-knowledge-graph\\\\(?:(.*)\\\\)?(.+?)$|', $class_name_lc, $matches );

	$path = str_replace( '\\', DIRECTORY_SEPARATOR, $matches[1] );
	$file = 'class-' . $matches[2] . '.php';

	$full_path = plugin_dir_path( __FILE__ )
	             . 'classes'
	             . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file;

	if ( ! file_exists( $full_path ) ) {
		//echo( "Class $class_name not found at $full_path." );

		return false;
	}

	require_once $full_path;

	return true;
} );

function cafemedia_knowledge_graph_init() {


	$configuration_service = \Wordlift_Configuration_Service::get_instance();

	$api_service = new Default_Api_Service(
		apply_filters( 'wl_api_base_url', 'https://api.wordlift.io' ),
		60,
		User_Agent::get_user_agent(),
		$configuration_service->get_key()
	);
	/**
	 * @todo:
	 * this cache should not be cleared in short interval
	 */
	$cache_service     = new Options_Cache( "wordlift-cmkg" );
	$analysis_service  = new Analysis_Service( $api_service, $cache_service );
	$tag_rest_endpoint = new Tag_Rest_Endpoint( $analysis_service );
	$tag_rest_endpoint->register_routes();

	$entity_rest_endpoint = new Entity_Rest_Endpoint();
	$entity_rest_endpoint->register_routes();

	$post_jsonld = new Post_Jsonld();
	$post_jsonld->enhance_post_jsonld();

	new Reconcile();

	$analysis_background_service = new Analysis_Background_Service( $analysis_service );

	new Tag_Created_Hook( $analysis_background_service );

	new Background_Analysis_Endpoint( $analysis_background_service, $cache_service );

	$reconcile_progress_endpoint = new Reconcile_Progress_Endpoint();
	$reconcile_progress_endpoint->register_routes();

}

add_action( 'plugins_loaded',
	function () {

		// Check that WordLift 3.27.5 is available.
		if ( ! class_exists( 'Wordlift' )
		     || 1 === version_compare( '3.27.5', Wordlift::get_instance()->get_version() ) ) {

			add_action( 'admin_notices',
				function () {
					$message = sprintf( __( 'Cafemedia Knowledge Graph requires at least WordLift version 3.27.5.',
						'wordlift-longtail' ) );
					printf( '<div class="notice is-dismissible error"><p>%s</p></div>', $message );
				} );

			return;
		}

		cafemedia_knowledge_graph_init();


	} );
