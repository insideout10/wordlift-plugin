<?php

namespace Wordlift\Dataset;

use Wordlift\Jsonld\Jsonld_Service;

class Sync_Object_Adapter_Factory {
	/**
	 * @var Sync_Object_Adapter_Factory
	 */
	private static $instance;

	/**
	 * @var Jsonld_Service
	 */
	private $jsonld_service;

	/**
	 * Sync_Object_Adapter_Factory constructor.
	 *
	 * @param $jsonld_service
	 */
	function __construct( $jsonld_service ) {
		$this->jsonld_service = $jsonld_service;

		self::$instance = $this;
	}

	static function get_instance() {
		return self::$instance;
	}

	/**
	 * @param $type
	 * @param $object_id
	 *
	 * @return Sync_Object_Adapter
	 * @throws \Exception
	 */
	function create( $type, $object_id ) {
		return new Sync_Object_Adapter( $type, $object_id, $this->jsonld_service );
	}

}
