<?php

namespace Wordlift\Google_Addon_Integration;

use Wordlift\Entity\Remote_Entity\Url_To_Remote_Entity_Converter;
use Wordlift\Entity\Remote_Entity_Importer\Remote_Entity_Importer_Factory;

class Rest_Endpoint {


	public function init() {
		// PHP 5.3 compatibility.
		$that = $this;

		add_action( 'rest_api_init', function () use ( $that ) {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/gaddon/import-entity', array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $that, 'import_entity' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_string( $param );
						}
					),
				)
			) );
		} );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return bool[]
	 */
	public function import_entity( $request ) {
		$entity_id = $request['entity_id'];

		$remote_entity = Url_To_Remote_Entity_Converter::convert( $entity_id );
		$importer      = Remote_Entity_Importer_Factory::from_entity( $remote_entity );
		$result        = $importer->import();

		return array(
			'import_status' => $result ? true : false
		);
	}


}