<?php

/**
 * The most generic kind of creative work, including books, movies, photographs, software programs, etc.
 * @schema http://schema.org/CreativeWork
 */
class CreativeWork extends Thing implements ISchema {
	
	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'Creative Work';
	}
	
	/**
	 * @description The subject matter of the content.
	 * @type Thing
	 */
	public $about;
	
	/**
	 * @description An embedded audio object.
	 * @type AudioObject
	 */
	public $audio;
	
	/**
	 * @description The location of the content.
	 * @type Place
	 */
	public $contentLocation;
	
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

	/**
	 * @description Genre of the creative work.
	 * @type Text
	 */
	public $genre;
	
	/**
	 * @description The language of the content. please use one of the language codes from the IETF BCP 47 standard. 
	 * @type Text
	 * @format bcp47
	 * @moreInfo http://tools.ietf.org/html/bcp47
	 */
	public $inLanguage;
	
	/**
	 * @description The keywords/tags used to describe this content.
	 * @type Text
	 */
	public $keywords;
	
	/**
	 * @description A thumbnail image relevant to the Thing.
	 * @type URL
	 */
	public $thumbnailUrl;
	
	/**
	 * @description An embedded video object.
	 * @type VideoObject
	 */
	public $video;
	
}

?>