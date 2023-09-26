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

	/**
	 * Remove the Video Object.
	 *
	 * @param $videos_to_be_removed array<Video>
	 *
	 * @param $post_id int
	 *
	 * @return bool return true if removed or false
	 */
	public function remove_videos( $videos_to_be_removed, $post_id );

	/**
	 * Remove all videos present for post id
	 *
	 * @param $post_id
	 *
	 * @return true if removed else false.
	 */
	public function remove_all_videos( $post_id );

}
