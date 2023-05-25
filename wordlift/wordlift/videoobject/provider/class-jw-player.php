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
	 *
	 * @return array<Video>
	 */
	public function get_videos_data( $videos ) {

		$video_urls = array_map(
			function ( $video ) {
				return $video->get_url();
			},
			$videos
		);

		$videos_data = $this->api_client->get_data( $video_urls );

		return array_filter(
			array_map(
				function ( $video_data ) {

					if ( ! array_key_exists( 'playlist', $video_data )

					|| ! is_array( $video_data['playlist'] )

					|| count( $video_data['playlist'] ) === 0 ) {

						  return false;

					}

					$video                 = new Video();
					$video->id             = $video_data['id'];
					$playlist_item_data    = $video_data['playlist'][0];
					$video->name           = $playlist_item_data['title'];
					$video->description    = $playlist_item_data['description'];
					$video->thumbnail_urls = array_filter(
						array_map(
							function ( $item ) {
								return $item['src'];
							},
							$playlist_item_data['images']
						)
					);

					$video->duration    = 'PT' . (int) $playlist_item_data['duration'] . 'S';
					$video->upload_date = gmdate( 'c', (int) $playlist_item_data['pubdate'] );

					$video_content_urls_data = array_filter(
						$playlist_item_data['sources'],
						function ( $item ) {
							return strpos( $item['type'], 'video/' ) !== false;
						}
					);

					$video_content_urls = array_map(
						function ( $item ) {
							return $item['file'];
						},
						$video_content_urls_data
					);

					// Content url is a single field, so we pick the video with
					// high resolution.
					$video->content_url = array_pop( $video_content_urls );

					// We dont have embed url for JW Player.
					$video->embed_url = '';

					return $video;

				},
				$videos_data
			)
		);

	}

}
