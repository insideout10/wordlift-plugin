<?php
/**
 * Context Cards Service
 *
 * @since      3.22.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

class Wordlift_Context_Cards_Service {

	function __construct() {

		$this->endpoint = '/jsonld';

		add_action( 'rest_api_init', function () {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, $this->endpoint, array(
				'methods' => 'GET',
				'callback' => array($this, 'context_data')
			) );
		} );

		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script') );

	}

	function context_data( $request ) {

		$entity_uri = $request->get_param( 'entity_url' );
		$entity_id = url_to_postid( $entity_uri );
		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $entity_id );
		return $jsonld;

	}

	function enqueue_script() {
		wp_enqueue_script( 'wordlift-cloud', plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/wordlift-cloud.js', array(), false, true );
		wp_add_inline_script( 'wordlift-cloud', "wordliftCloud.contextCards('a.wl-entity-page-link', '".get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE . $this->endpoint."')" );
	}

}
