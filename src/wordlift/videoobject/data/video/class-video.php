<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Data\Video;

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
	 * @var array Thumbnail urls.
	 */
	public $thumbnail_urls;

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

	/**
	 * @var string An unique identifier, usually the video url.
	 */
	public $id;

	/**
	 * @var int The number of views for the video.
	 */
	public $views;

	public function get_data() {

		return array(
			'@type'        => 'VideoObject',
			'name'         => $this->name,
			'description'  => $this->description,
			'contentUrl'   => $this->content_url,
			'embedUrl'     => $this->embed_url,
			'uploadDate'   => $this->upload_date,
			'thumbnailUrl' => $this->thumbnail_urls,
			'duration'     => $this->duration
		);

	}

}