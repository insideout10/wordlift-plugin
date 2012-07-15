<?php

class SchemaOrg_Schema implements SchemaOrg_ISchema {
	
	private $type;
	private $properties;

	function __construct($type, $properties = NULL) {
		$this->type = $type;
		$this->properties = $properties;
	}

	public function getType() {
		return $this->type;
	}

	public function hasProperty($name) {
		if (NULL == $this->properties)
			return false;

		foreach ($this->properties as $property) {
			if ($name === $property->getName())
				return true;
		}

		return false;
	}

	public function getProperty($name) {
		if (NULL === $this->properties)
			return false;

		if (false === is_array($this->properties))
			throw new Exception("Properties should be an array.");

		foreach ($this->properties as $property) {
			if ($name === $property->getName())
				return $property;
		}

		return NULL;
	}

}

?>