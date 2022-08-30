<?php

namespace Wordlift\Dataset;

use Wordlift\Object_Type_Enum;

class Sync_User_Adapter extends Abstract_Sync_Object_Adapter {

	/**
	 * Sync_User_Adapter constructor.
	 *
	 * @param int $user_id
	 *
	 * @throws \Exception when an error occurs.
	 */
	public function __construct( $user_id ) {
		parent::__construct( Object_Type_Enum::USER, $user_id );
	}

	public function is_published() {
		return true;
	}

	public function is_public() {
		return true;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function set_values( $arr ) {
		// @@todo
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function get_value( $key ) {
		// @@todo
	}

}
