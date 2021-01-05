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

class Analysis_Response_Ops_Factory {
	/**
	 * @var \Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;
	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;
	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;
	/**
	 * @var \Wordlift_Post_Image_Storage
	 */
	private $post_image_storage;
	/**
	 * @var Entity_Helper
	 */
	private $entity_helper;

	private static $instance;

	/**
	 * Analysis_Response_Ops constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service}.
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service}.
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service}.
	 * @param \Wordlift_Post_Image_Storage $post_image_storage A {@link Wordlift_Post_Image_Storage} instance.
	 * @param Entity_Helper $entity_helper The {@link Entity_Helper}.
	 *
	 * @since 3.25.1
	 */
	public function __construct( $entity_uri_service, $entity_service, $entity_type_service, $post_image_storage, $entity_helper ) {

		$this->entity_uri_service  = $entity_uri_service;
		$this->entity_service      = $entity_service;
		$this->entity_type_service = $entity_type_service;
		$this->post_image_storage  = $post_image_storage;
		$this->entity_helper       = $entity_helper;

		self::$instance = $this;

	}

	public static function get_instance() {

		return self::$instance;
	}

	public function create( $json ) {

		return new Analysis_Response_Ops(
			$this->entity_uri_service,
			$this->entity_service,
			$this->entity_type_service,
			$this->post_image_storage,
			$this->entity_helper,
			$json );
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
	public function create_with_response( $response ) {

		if ( ! isset( $response['body'] ) ) {
			throw new \Exception( "`body` is required in response." );
		}

		return $this->create( json_decode( $response['body'] ) );
	}

}