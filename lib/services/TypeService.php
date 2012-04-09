<?php

/**
 * Provides reflection access to the class properties.
 */
class TypeService {
	
	public static function create($class_name) {
		global $logger;
		
		if (NULL == $class_name || '' == $class_name)
			$class_name = 'Thing';
		
		$logger->debug('Creating a type for class ['.$class_name.'].');
		
		$type = new Type();
		$type->name = $class_name;
		
		try {
			$class = new ReflectionClass( $class_name );
		} catch (Exception $e) {
			$logger->error('Class '.$class_name.' raised an exception: '.$e);
		}
		
		$parent_class = $class->getParentClass();
		
		while (NULL != $parent_class) {
			self::add_properties($type->properties,self::get_properties($parent_class));
			$parent_class = $parent_class->getParentClass();
		}

		self::add_properties($type->properties,self::get_properties($class));
				
		return $type;
	}
	
	private static function get_properties(&$class) {
		$reference_properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
		$properties = array();
		
		foreach ($reference_properties as $reference_property) {
			$properties[] = PropertyService::create($reference_property);
		}
		
		return $properties;
	}
	
	private static function add_properties(&$properties,&$properties_to_add) {
		foreach ($properties_to_add as $property_to_add) {
			if (false == in_array($property_to_add,$properties)) {
				$properties[] = $property_to_add;
			}
		}
	}
}

?>