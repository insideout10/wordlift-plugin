<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Data\Video_Storage;

class Meta_Storage implements Storage {

	const META_KEY = '_wl_video_object_videos';

	public function add_video( $post_id, $video ) {
		/**
		 * @since 3.31.0
		 * Fires when the video storage gets updated
		 */
		do_action( 'wordlift_videoobject_video_storage_updated' );
		add_post_meta( $post_id, self::META_KEY, $video );
	}

	public function get_all_videos( $post_id ) {

		return get_post_meta( $post_id, self::META_KEY );

	}

	public function remove_videos( $videos_to_be_removed, $post_id ) {
		/**
		 * @since 3.31.0
		 * Fires when the video storage gets updated
		 */
		do_action( 'wordlift_videoobject_video_storage_updated' );
		$videos_to_be_removed_ids = array_map(
			function ( $video ) {
				return $video->id;
			},
			$videos_to_be_removed
		);

		$present_videos = $this->get_all_videos( $post_id );

		$filtered_videos = array_filter(
			$present_videos,
			function ( $video ) use ( $videos_to_be_removed_ids ) {
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				return ! in_array( $video->id, $videos_to_be_removed_ids );
			}
		);

		// Remove all existing videos.
		$this->remove_all_videos( $post_id );

		// Save the remaining videos.
		foreach ( $filtered_videos as $video ) {
			$this->add_video( $post_id, $video );
		}

	}

	public function remove_all_videos( $post_id ) {
		delete_post_meta( $post_id, self::META_KEY );
	}
}
