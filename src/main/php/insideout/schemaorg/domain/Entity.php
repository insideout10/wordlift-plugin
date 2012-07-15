<?php

/*
 * 
 */
class SchemaOrg_Entity {

	private $schema;
	private $properties;

	function __construct( $properties, SchemaOrg_ISchema $schema = NULL, $dataStore = NULL ) {
		$this->schema = $schema;
		$this->properties = $properties;
		$this->dataStore = $dataStore;

		# last chance to get a schema.
		if (NULL === $this->schema && NULL !== $this->dataStore) {
			$this->schema = $this->dataStore->getSchema($this->properties);
            if (NULL === $this->schema)
                throw new Exception( "Could not load a schema for an entity." );
        }
	}

	function __get($name) {

		if (NULL === $this->schema)
			throw new Exception("This entity does not have a schema.");

		$schemaProperty = $this->schema->getProperty($name);

		if (NULL === $schemaProperty)
			throw new Exception("This entity does not support a property with name \"$name\".");
		
		if (NULL === $this->dataStore)
			throw new Exception("This entity does not have a dataStore to read properties.");	
		
		return $this->dataStore->getProperty( $this->properties, $schemaProperty );
	}

	public function getSchema() {
		return $this->schema;
	}

	function __toString() {
		$type = $this->schema->getType();
		return "[type:$type]";
	}
}

?>