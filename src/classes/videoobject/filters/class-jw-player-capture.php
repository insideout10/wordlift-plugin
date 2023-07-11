<?php
/**
 * This class hooks in to `wl_videoobject_embedded_videos` filters and add the jw player videos.
 * shortcodes.
 *
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Filters;

use Wordlift\Videoobject\Data\Embedded_Video\Default_Embedded_Video;

class Jw_Player_Capture {

	public function init() {
		add_filter( 'wl_videoobject_embedded_videos', array( $this, 'wl_videoobject_embedded_videos' ), 10, 2 );
	}

	public function wl_videoobject_embedded_videos( $embedded_videos, $post_id ) {
		// we cant reliably determine count for external plugin without
		// this method.
		global $wpdb;
		$video_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND post_id=%d",
				'_jwppp-video-url-%',
				$post_id
			)
		);
		if ( ! $video_ids ) {
			return $embedded_videos;
		}

		$jw_player_videos = array_map(
			function ( $video_id ) {
				return new Default_Embedded_Video( 'https://cdn.jwplayer.com/v2/media/' . $video_id );
			},
			$video_ids
		);

		return array_merge( $embedded_videos, $jw_player_videos );
	}

}
