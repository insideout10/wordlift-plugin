<?php

class GeoCoordinates extends Thing {
	
	/**
	 * @type Number or Text
	 * @description The elevation of a location.
	 */
	public $elevation;
	
	/**
	 * @type Number or Text
	 * @description The latitude of a location. For example 37.42242.
	 */
	public $latitude;
	
	/**
	 * @type Number or Text
	 * @description The longitude of a location. For example -122.08585.
	 */
	public $longitude;
}

?>