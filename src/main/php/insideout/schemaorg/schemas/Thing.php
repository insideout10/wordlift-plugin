<?php

/**
 * @schema http://schema.org/Thing
 * @author david
 *
 */
class Thing implements ISchema {
		
	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'Thing';
	}
	
	/**
	 * @description A short description of the item.
	 * @type Text
	 * @multiline true
	 */
	public $description;
	
	/**
	 * @description URL of an image of the item.
	 * @type URL
	 */
	public $image;
	
	/**
	 * @description The name of the item.
	 * @type Text
	 */
	public $name;
	
	/**
	 * @description URL of the item.
	 * @type URL
	 */
	public $url;
	
}

?>