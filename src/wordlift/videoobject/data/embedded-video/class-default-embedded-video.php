<?php
namespace Wordlift\Videoobject\Data\Embedded_Video;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class Default_Embedded_Video implements Embedded_Video {

	/**
	 * @var string The url of the video which got embedded.
	 */
	private $video_url;

	public function __construct( $video_url ) {
		$this->video_url = $video_url;
	}

	public function get_api_provider() {
		// TODO: Implement get_api_provider() method.
	}

	public function get_url() {
		return $this->video_url;
	}
}
