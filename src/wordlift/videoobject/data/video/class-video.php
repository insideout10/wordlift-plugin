<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift_Videoobject\Videoobject\Video\Jsonld;

class Video {
	/**
	 * @var string The title of the video.
	 */
	public $name;

	/**
	 * @var string The video description.
	 */
	public $description;

	/**
	 * @var array Thumbnail url.
	 */
	public $thumbnail_url;

	/**
	 * @var string Published date.
	 */
	public $upload_date;

	/**
	 * @var string Video url.
	 */
	public $content_url;

	/**
	 * @var string Video duration in IS08601 format.
	 */
	public $duration;

	/**
	 * @var string
	 */
	public $embed_url;
}