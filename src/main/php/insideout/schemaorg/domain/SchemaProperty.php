<?php

class SchemaOrg_SchemaProperty {
	
	private $name;
	private $type;

	function __construct($name, $type) {
		$this->name = $name;
		$this->type = $type;
	}

	public function getName() {
		return $this->name;
	}

	public function getType() {
		return $this->type;
	}

}

?>