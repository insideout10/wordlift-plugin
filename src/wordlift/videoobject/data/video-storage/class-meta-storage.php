<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Data\Video_Storage;


class Meta_Storage implements Storage {

	const META_KEY = '_wl_video_object_videos';

	public function add_video( $post_id, $video ) {

		add_post_meta( $post_id, self::META_KEY, $video );
	}


	public function get_all_videos( $post_id ) {

		return get_post_meta( $post_id, self::META_KEY );

	}


}