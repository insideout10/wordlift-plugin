<?php

/**
 * The most generic kind of creative work, including books, movies, photographs, software programs, etc.
 * @schema http://schema.org/CreativeWork
 */
class CreativeWork extends Thing {
	
	/**
	 * @description The date on which the CreativeWork was created.
	 * @type Date
	 */
	public $dateCreated;
	
	/**
	 * @description The date on which the CreativeWork was most recently modified.
	 * @type Date
	 */
	public $dateModified;
	
	/**
	 * @description Date of first broadcast/publication.
	 * @type Date
	 */
	public $datePublished;

}

?>