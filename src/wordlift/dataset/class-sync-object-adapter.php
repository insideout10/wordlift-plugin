<?php

namespace Wordlift\Dataset;

use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;

class Sync_Object_Adapter {

	const HASH = '_wl_jsonld_hash';

	private $object_id;

	private $type;

	private $jsonld_service;

	private $get_meta;
	private $update_meta;

	/**
	 * Sync_Object_Adapter constructor.
	 *
	 * @param int $type One of Object_Type_Enum.
	 * @param int $object_id A post or term id.
	 * @param Jsonld_Service
	 *
	 * @throws \Exception
	 */
	function __construct( $type, $object_id, $jsonld_service ) {

		$this->type = filter_var( $type, FILTER_VALIDATE_INT, array(
			'options' => array(
				'min_range' => 0,
				'max_range' => 1,
			),
		) );

		$this->object_id = filter_var( $object_id, FILTER_VALIDATE_INT );

		if ( null === $this->type ) {
			throw new \Exception( 'Invalid $type.' );
		}
		if ( null === $this->object_id ) {
			throw new \Exception( 'Invalid $object.' );
		}

		$this->jsonld_service = $jsonld_service;

		if ( Object_Type_Enum::POST === $this->type ) {
			$this->get_meta    = 'get_post_meta';
			$this->update_meta = 'update_post_meta';
		} else {
			$this->get_meta    = 'get_term_meta';
			$this->update_meta = 'update_term_meta';
		}
	}

	function is_changed() {

		$hash = call_user_func( $this->get_meta, $this->object_id, self::HASH, true );

		return empty( $hash ) || $hash !== $this->hash( $this->get_jsonld() );
	}

	function get_jsonld() {

		return apply_filters( 'wl_dataset__sync_service__sync_item__jsonld',
			$this->jsonld_service->get( $this->type, $this->object_id ), $this->type, $this->object_id );
	}

	function get_jsonld_and_update_hash() {
		$jsonld = $this->get_jsonld();

		call_user_func( $this->update_meta, $this->object_id, self::HASH, $this->hash( $jsonld ) );

		return $jsonld;
	}

	private function hash( $jsonld ) {
		return sha1( wp_json_encode( $jsonld ) );
	}

}
