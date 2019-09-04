<?php


namespace Wordlift\Analysis\Response;


class Analysis_Response_Ops {

	private $json;

	public function __construct( $json ) {

		$this->json = $json;

	}

	public function make_entities_local() {

		if ( ! isset( $this->json->entities ) ) {
			return $this;
		}

		$configuration_service = \Wordlift_Configuration_Service::get_instance();

		foreach ( $this->json->entities as $key => $value ) {
			if ( 0 !== strpos( $key, $configuration_service->get_dataset_uri() ) ) {
				unset( $this->json->entities->{$key} );
			}
		}

		return $this;
	}

	public function to_string() {

		return wp_json_encode( $this->json, JSON_UNESCAPED_UNICODE );

	}

	public static function create_with_response( $response ) {

		if ( ! isset( $response['body'] ) ) {
			throw new \Exception( "`body` is required in response." );
		}

		return new static( json_decode( $response['body'] ) );
	}

}
