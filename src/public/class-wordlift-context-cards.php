<?php
/**
 * Context Cards Service
 *
 * @since      3.22.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

class Wordlift_Context_Cards_Service {

	protected $entity_uri_service;

	function __construct($entity_uri_service) {

		$this->entity_uri_service = $entity_uri_service;

		add_action( 'rest_api_init', function () {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/context', array(
				'methods' => 'GET',
				'callback' => array($this, 'context_data')
			) );
		} );
	}

	function context_data( $request ) {

		$entity_uri = $request->get_param( 'entity_url' );
		// $entity_id = $this->entity_uri_service->get_entity( $entity_uri );
		$entity_id = url_to_postid( $entity_uri );
		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $entity_id );
		return $jsonld;

	}

}
