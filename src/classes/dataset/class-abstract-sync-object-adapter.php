<?php

namespace Wordlift\Dataset;

use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;

abstract class Abstract_Sync_Object_Adapter implements Sync_Object_Adapter {

	private $object_id;

	private $type;

	private static $meta_name = array(
		Object_Type_Enum::POST => 'post',
		Object_Type_Enum::TERM => 'term',
		Object_Type_Enum::USER => 'user',
	);

	/**
	 * Sync_Object_Adapter constructor.
	 *
	 * @param int            $type One of Object_Type_Enum.
	 * @param int            $object_id A post or term id.
	 * @param Jsonld_Service
	 *
	 * @throws \Exception when an error occurs.
	 */
	public function __construct( $type, $object_id ) {

		$this->type      = filter_var( $type, FILTER_VALIDATE_INT );
		$this->object_id = filter_var( $object_id, FILTER_VALIDATE_INT );

		if ( null === $this->type || ! isset( self::$meta_name[ $this->type ] ) ) {
			throw new \Exception( 'Invalid $type.' );
		}
		if ( null === $this->object_id ) {
			throw new \Exception( 'Invalid $object.' );
		}

	}

	public function get_type() {
		return $this->type;
	}

	public function get_object_id() {
		return $this->object_id;
	}

}
