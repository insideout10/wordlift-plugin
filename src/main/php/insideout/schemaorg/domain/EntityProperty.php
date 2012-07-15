<?php

class SchemaOrg_EntityProperty {

	private $schemaProperty;
	private $values;

	function __construct( $schemaProperty, $values ) {
		$this->schemaProperty = $schemaProperty;
		$this->values = $values;
	}

	public function getSchemaProperty() {
		return $this->schemaProperty;
	}

	public function getValues() {
		return $this->values;
	}

	public function getValue($index) {
		if (!($index > -1 && $index < count($this->values)))
			return NULL;

		return $this->values[$index]->getValue();
	}

	public function getCount() {
		return count($this->values);
	}

	public function hasReference($index) {		
		if (!($index > -1 && $index < count($this->values)))
			throw new Exception("The requested index [$index] is outside the allowed boundaries.");

		return (NULL !== $this->values[$index]->getReference());
	}

	public function getReference($index) {
		if (!($index > -1 && $index < count($this->values)))
			throw new Exception("The requested index [$index] is outside the allowed boundaries.");

		return $this->values[$index]->getReference();
	}

	# get the value of the first value.
	function __get($name) {

		if (NULL === $this->getReference(0)) {
			$name = $this->getSchemaProperty()->getName();
			$value = $this->getValue(0);

			throw new Exception("The property does not have a reference to another entity [name:$name][value:$value].");
		}
			
		return $this->getReference(0)->$name->getValue(0);
	}

}

?>