<?php

class SchemaOrg_SchemaService {

    public $logger;
    public $xRayService;

	public function getSchema($name) {
		$schemaClass = $this->xRayService->scan($name);

		if (NULL === $schemaClass) {
            $this->logger->trace( "A schema [$name] is not found. Will use the Null schema." );
            return new SchemaOrg_Schema($name);
        }

		$properties = $schemaClass[$name][SchemaOrg_XRayService::PROPERTIES];

		$schemaProperties = array();

//        $this->logger->trace( "Found schema [$name] with " . count($properties) . " properties." );

        foreach ($properties as $key => $value) {
			$descriptors = &$value[SchemaOrg_XRayService::DESCRIPTORS];
			$type = $descriptors["type"][0][SchemaOrg_XRayService::VALUE];
			$schemaProperties[] = new SchemaOrg_SchemaProperty($key, $type);
		}

		return new SchemaOrg_Schema( $name, $schemaProperties );
	}

}

?>