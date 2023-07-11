<?php
namespace Wordlift\Videoobject\Parser;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
interface Parser {

	/**
	 * @param int $post_id Post id.
	 *
	 * @return array<Embedded_Video>
	 */
	public function get_videos( $post_id );

}
