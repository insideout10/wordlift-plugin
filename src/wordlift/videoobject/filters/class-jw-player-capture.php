<?php
/**
 * This class hooks in to `wl_videoobject_embedded_videos` filters and add the jw player videos.
 * shortcodes.
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
		$post_meta_table_name = $wpdb->postmeta;
		$query_template = <<<EOF
SELECT meta_value FROM $post_meta_table_name WHERE meta_key LIKE '_jwppp-video-url-%' AND post_id=%d
EOF;
		$query                = $wpdb->prepare( $query_template, $post_id );
		$video_ids            = $wpdb->get_col( $query );
		if ( ! $video_ids ) {
			return $embedded_videos;
		}

		var_dump(get_post_meta( $post_id, '_jwppp-video-url-1'));

		$jw_player_videos = array_map( function ( $video_id ) {
			return new Default_Embedded_Video( 'https://cdn.jwplayer.com/v2/media/' . $video_id );
		}, $video_ids );

		return array_merge( $embedded_videos, $jw_player_videos );
	}


}