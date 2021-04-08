<?php

namespace Wordlift\Videoobject\Data\Video_Storage;

use Wordlift\Videoobject\Data\Video\Video;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
interface Storage {

	/**
	 * @param $post_id int Post id to add video.
	 * @param $video Video The video object which needs to be saved.
	 *
	 * @return bool true if video is added, false otherwise.
	 */
	public function add_video( $post_id, $video );

	/**
	 * Return all video objects for the post
	 *
	 * @param $post_id int Post id to get all videos.
	 *
	 * @return array<Video>
	 */
	public function get_all_videos( $post_id );

}