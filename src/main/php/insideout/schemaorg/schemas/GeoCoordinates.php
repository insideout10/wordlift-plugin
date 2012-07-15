<?php

class GeoCoordinates extends Thing implements ISchema {
	
	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'GeoCoordinates';
	}
	
	
	/**
	 * @type Text
	 * @description The elevation of a location.
	 */
	public $elevation;
	
	/**
	 * @type Text
	 * @description The latitude of a location. For example 37.42242.
	 */
	public $latitude;
	
	/**
	 * @type Text
	 * @description The longitude of a location. For example -122.08585.
	 */
	public $longitude;
}

?>