<?php

/**
 * A movie.
 * @schema http://schema.org/Movie
 */
class Movie extends CreativeWork {
	
	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'Movie';
	}
	
	
	/**
	 * @description The director of the movie, TV episode, or series.
	 * @type Person
	 */
	public $director;
	
}

?>