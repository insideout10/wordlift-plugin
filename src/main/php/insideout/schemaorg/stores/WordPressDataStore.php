<?php

class SchemaOrg_WordPressDataStore implements SchemaOrg_IDataStore {

    public $logger;

	public $fieldPrefix;
    public $fieldType;
    public $schemaService;

	public function getSchema( &$properties) {
		$id = $properties;

        if ( NULL === $id || "" === $id)
            throw new Exception( "An ID is required. None has been provided." );

		$type = get_post_meta($id, $this->fieldType, true);

        $this->logger->trace( "Looking for schema [$type] of post ID [$id] using meta [" . $this->fieldType . "]." );
		return $this->schemaService->getSchema($type);
	}

	public function getProperty( &$properties, SchemaOrg_SchemaProperty $schemaProperty ) {
		global $wpdb;

		$name = $schemaProperty->getName();

        if ( is_array( $properties ) )
            $fields = $properties[ "fields" ];

        if ( is_numeric( $properties ) ) {
            $postID = $properties;
            $post = get_post($postID);

            if (NULL === $post)
                throw new Exception("Cannot find a WordPress post with [postID:$postID].");

            # see http://codex.wordpress.org/Function_Reference/get_post_custom
            $fields = get_post_custom($postID);

            $properties = array(
                "ID" => $postID,
                "fields" => $fields
            );
        }

        $field = $this->getField($fields, $name);

		// $field = implode(",", $field);
		// $field = explode(",", $field);
		
		# create an array of values.
		$values = array();

		# encapsulate each value in the EntityPropertyValue.
		foreach ($field as $value) {
			$value = trim($value);
			$reference = NULL;
			# TODO: get the type from the actual post instance.
			$schema = $this->schemaService->getSchema($schemaProperty->getType());

            $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '" . esc_sql( $value ) . "'");
			if (NULL !== $id)
				$reference = new SchemaOrg_Entity( $id, $schema, $this );

			$values[] = new SchemaOrg_EntityPropertyValue($value, $reference);
		}

//        $this->logger->trace( "Found " . count($values) . " value(s) for property [$name] of Entity Post ID [$postID]." );

        $property = new SchemaOrg_EntityProperty(
				$schemaProperty,
				$values
			);

		return $property;
	}


	private function getField($fields, $name) {
		$name = $this->fieldPrefix . $name;
		$values = array();

		foreach ($fields as $key => $value)
			if (strtolower($name) === strtolower($key))
				$values = array_merge( $values, $value);

		return $values;
	}	
}

?>