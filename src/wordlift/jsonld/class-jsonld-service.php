<?php

namespace Wordlift\Jsonld;

use Exception;
use Wordlift\Assertions;
use Wordlift\Object_Type_Enum;
use Wordlift_Jsonld_Service;
use Wordlift_Term_JsonLd_Adapter;

class Jsonld_Service {

	/**
	 * @var Jsonld_Service
	 */
	private static $instance;

	/**
	 * @var Wordlift_Jsonld_Service
	 */
	private $legacy_jsonld_service;

	/**
	 * @var Wordlift_Term_JsonLd_Adapter
	 */
	private $term_jsonld_service;

	/**
	 * Jsonld_Service constructor.
	 *
	 * @param Wordlift_Jsonld_Service $legacy_jsonld_service
	 * @param Wordlift_Term_JsonLd_Adapter $term_jsonld_adapter
	 *
	 * @throws Exception
	 */
	public function __construct( $legacy_jsonld_service, $term_jsonld_adapter ) {

		Assertions::assert_of_type( $legacy_jsonld_service, 'Wordlift_Jsonld_Service' );
		Assertions::assert_of_type( $term_jsonld_adapter, 'Wordlift_Term_JsonLd_Adapter' );

		$this->legacy_jsonld_service = $legacy_jsonld_service;
		$this->term_jsonld_service   = $term_jsonld_adapter;

		self::$instance = $this;
	}

	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Get the JSON-LD structure for the specified type and id.
	 *
	 * @param int $type The requested type, one of 'HOMEPAGE', 'POST' or 'TERM'. Default 'POST'.
	 * @param int|null $id The id. Default `null`.
	 *
	 * @return array The JSON-LD structure.
	 * @throws Exception Throws an exception if the type isn't recognized.
	 */
	public function get( $type = Object_Type_Enum::POST, $id = null ) {

		switch ( $type ) {
			case Object_Type_Enum::HOMEPAGE:
				return $this->legacy_jsonld_service->get_jsonld( true, $id );
			case Object_Type_Enum::POST:
				return $this->legacy_jsonld_service->get_jsonld( false, $id );
			case Object_Type_Enum::TERM:
				return $this->term_jsonld_service->get( $id );
			default:
				throw new Exception( "Unknown type $type. Allowed types: 'HOMEPAGE', 'POST', 'TERM'." );
		}

	}

}
