<?php

namespace Wordlift\Modules\Pods;

class Schema_Field_Group {

	private $schema_type;

	private $custom_fields;

	public function __construct( $schema_type, $custom_fields ) {
		$this->schema_type   = $schema_type;
		$this->custom_fields = $custom_fields;
	}

	/**
	 * @return mixed
	 */
	public function get_schema_type() {
		return $this->schema_type;
	}

	/**
	 * @return mixed
	 */
	public function get_custom_fields() {
		return $this->custom_fields;
	}

}
