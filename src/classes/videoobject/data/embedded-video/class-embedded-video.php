<?php
namespace Wordlift\Videoobject\Data\Embedded_Video;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Represents a video embedded on the post content
 * Interface Embedded_Video
 */
interface Embedded_Video {

	public function get_api_provider();

	public function get_url();

}
