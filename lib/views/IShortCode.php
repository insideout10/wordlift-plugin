<?php

/**
 * This interface is implemented by all class that provide a ShortCode. 
 */
interface IShortCode {
	
	/**
	 * Returns the ShortCode for the View.
	 */
	public static function getShortCode();
	
	/**
	 * This method is called when the short-code needs to be processed.
	 */
	public static function doShortCode($atts, $content=null, $tag=null);
}

?>