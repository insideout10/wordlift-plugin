<?php

class SchemaOrg_EntityPropertyValue {
	
	private $value;
	private $ref;

	function __construct($value, $ref = NULL) {
		$this->value = $value;
		$this->ref = $ref;
	}

	public function getValue() {
		return $this->value;
	}

	public function getReference() {
		return $this->ref;
	}
	
}

?>