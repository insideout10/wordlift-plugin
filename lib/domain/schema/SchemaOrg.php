<?php

/**
 * This class provides an enumeration of supported schema.org types.
 */
class SchemaOrg {
	
	public static function getSupportedTypes() {
		return Array(
			'Thing' 		=> Thing,
			'Creative Work' => CreativeWork,
			'Organization'	=> Organization,
			'Person'		=> Person,
			'Place' 		=> Place,
			'Product'  		=> Product
		);
	}
	
}

?>