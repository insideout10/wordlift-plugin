<?php

class TypeService {
	
	public static function create($class_name) {
		global $logger;
		
		$type = new Type();
		
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
		
// 		echo var_export($properties,true).'<br/>';
		
		return $properties;
	}
	
	public static function get_types() {
		return array('Thing','Person');
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