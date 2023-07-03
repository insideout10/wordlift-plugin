<?php

namespace Wordlift\Videoobject\Data\Embedded_Video;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Embedded_Video_Factory {

	public static function get_embedded_video( $video_url ) {
		return new Default_Embedded_Video( $video_url );
	}

}
