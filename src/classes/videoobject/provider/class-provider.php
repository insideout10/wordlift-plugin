<?php
namespace Wordlift\Videoobject\Provider;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Video\Video;

/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This acts an interface for different data sources like youtube, vimeo
 */
interface Provider {

	/**
	 * @param $videos array<Embedded_Video>
	 * @return array<Video>
	 */
	public function get_videos_data( $videos );

}
