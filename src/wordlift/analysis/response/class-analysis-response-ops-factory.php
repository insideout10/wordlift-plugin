<?php
/**
 * Create instances of {@link \Wordlift\Analysis\Response\Analysis_Response_Ops}.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Analysis\Response
 */

namespace Wordlift\Analysis\Response;

use Wordlift\Entity\Entity_Helper;
use Wordlift_Entity_Uri_Service;

class Analysis_Response_Ops_Factory {
	/**
	 * @var Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;

	/**
	 * @var Entity_Helper
	 */
	private $entity_helper;

	/**
	 * Analysis_Response_Ops constructor.
	 *
	 * @param Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service}.
	 * @param Entity_Helper               $entity_helper The {@link Entity_Helper}.
	 *
	 * @since 3.25.1
	 */
	protected function __construct( $entity_uri_service, $entity_helper ) {

		$this->entity_uri_service = $entity_uri_service;
		$this->entity_helper      = $entity_helper;

	}

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( Wordlift_Entity_Uri_Service::get_instance(), Entity_Helper::get_instance() );
		}

		return self::$instance;
	}

	public function create( $json, $post_id ) {
		return new Analysis_Response_Ops(
			$this->entity_uri_service,
			$this->entity_helper,
			$json,
			$post_id
		);
	}

	/**
	 * Create an Analysis_Response_Ops instance given the provided http response.
	 *
	 * @param array $response {
	 *
	 * @type string $body The response body.
	 * }
	 *
	 * @return Analysis_Response_Ops A new Analysis_Response_Ops instance.
	 * @throws \Exception if the provided response doesn't contain a `body` element.
	 */
	public function create_with_response( $response, $post_id ) {

		if ( ! isset( $response['body'] ) ) {
			throw new \Exception( '`body` is required in response.' );
		}

		return $this->create( json_decode( $response['body'] ), $post_id );
	}

}
