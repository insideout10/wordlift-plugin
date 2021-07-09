<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift\Videoobject\Provider;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Video\Video;

class Jw_Player extends Api_Provider {


	/**
	 * @param $videos array<Embedded_Video>
	 * @return array<Video>
	 */
	public function get_videos_data( $videos ) {
	}

}
