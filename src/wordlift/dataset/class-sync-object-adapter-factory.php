<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_Object_Adapter_Factory {

	/**
	 * @param $type
	 * @param $object_id
	 *
	 * @return Abstract_Sync_Object_Adapter
	 * @throws \Exception
	 */
	function create( $type, $object_id ) {

		switch ( $type ) {
			case Object_Type_Enum::POST:
				return new Sync_Post_Adapter( $object_id );
			case Object_Type_Enum::USER:
				return new Sync_User_Adapter( $object_id );
			default:
				throw new \Exception( "Unsupported type $type." );
		}

	}

}
