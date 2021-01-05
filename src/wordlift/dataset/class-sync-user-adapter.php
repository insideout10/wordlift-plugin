<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_User_Adapter extends Abstract_Sync_Object_Adapter {

	/**
	 * Sync_User_Adapter constructor.
	 *
	 * @param int $user_id
	 *
	 * @throws \Exception
	 */
	function __construct( $user_id ) {
		parent::__construct( Object_Type_Enum::USER, $user_id );
	}

	function is_published() {
		return true;
	}

	function is_public() {
		return true;
	}

}
