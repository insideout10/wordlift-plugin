<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Object_Adapter_Factory {

	/**
	 * @param int $type One of Object_Type_Enum::POST, Object_Type_Enum::USER, ...
	 * @param int $object_id The object id.
	 *
	 * @return Sync_Object_Adapter
	 * @throws \Exception when an error occurs.
	 */
	public function create( $type, $object_id ) {

		switch ( $type ) {
			case Object_Type_Enum::POST:
				return new Sync_Post_Adapter( $object_id );
			case Object_Type_Enum::USER:
				return new Sync_User_Adapter( $object_id );
			case Object_Type_Enum::TERM:
				return new Sync_Term_Adapter( $object_id );
			default:
				throw new \Exception( "Unsupported type $type." );
		}

	}

	public function create_many( $type, $object_ids ) {
		$that = $this;

		return array_map(
			function ( $item ) use ( $type, $that ) {
				return $that->create( $type, $item );
			},
			(array) $object_ids
		);
	}

}
