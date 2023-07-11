<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Provider;

use DateInterval;
use Wordlift\Videoobject\Data\Video\Video;

class Youtube extends Api_Provider {

	const YT_API_FIELD_NAME = '__wl_video_object_youtube_api_key';

	private static function get_thumbnails( $api_thumbnail_data ) {
		return array_map(
			function ( $item ) {
				return $item['url'];

			},
			$api_thumbnail_data
		);
	}

	public function get_videos_data( $videos ) {
		$urls = array_map(
			function ( $video ) {
				/**
				 * @param $video Video
				 */
				return $video->get_url();
			},
			$videos
		);

		return $this->get_data( $urls );

	}

	/**
	 * @param $video_urls
	 *
	 * @return bool | array<Video>
	 */
	public function get_data( $video_urls ) {
		// extract ids from the url list.
		if ( ! is_array( $video_urls ) ) {
			return array();
		}
		$response_body = $this->api_client->get_data( $video_urls );

		return $this->parse_youtube_video_data_from_response( $response_body );
	}

	/**
	 * @param $response_body string
	 *
	 * @return array<Video>
	 */
	private function parse_youtube_video_data_from_response( $response_body ) {
		$result = json_decode( $response_body, true );
		if ( ! is_array( $result ) ) {
			// Return empty array since the response body is invalid.
			return array();
		}
		if ( ! array_key_exists( 'items', $result ) ) {
			return array();
		}
		$videos_json_data = $result['items'];
		$videos           = array();
		foreach ( $videos_json_data as $single_video_json_data ) {
			$videos[] = self::create_video_from_youtube_data( $single_video_json_data );
		}

		return $videos;
	}

	public static function create_video_from_youtube_data( $video_data ) {

		$video = new Video();
		if ( array_key_exists( 'contentDetails', $video_data ) ) {
			$video->duration = $video_data['contentDetails']['duration'];
		}

		if ( array_key_exists( 'id', $video_data ) ) {
			$video_id           = $video_data['id'];
			$video->embed_url   = "https://www.youtube.com/embed/${video_id}";
			$video->content_url = "https://www.youtube.com/watch?v=${video_id}";
		}
		if ( ! array_key_exists( 'snippet', $video_data ) ) {
			return false;
		}

		$video->name        = $video_data['snippet']['title'];
		$video->description = $video_data['snippet']['description'];

		/**
		 * @since 3.30.0
		 * Use title as fallback if description is not present.
		 */
		if ( ! $video->description ) {
			$video->description = $video->name;
		}

		$video->upload_date = $video_data['snippet']['publishedAt'];

		if ( array_key_exists( 'thumbnails', $video_data['snippet'] ) ) {
			$api_thumbnail_data    = array_values( $video_data['snippet']['thumbnails'] );
			$video->thumbnail_urls = self::get_thumbnails( $api_thumbnail_data );
		}

		if ( array_key_exists( 'statistics', $video_data )
			 && array_key_exists( 'viewCount', $video_data['statistics'] ) ) {
			$video->views = $video_data['statistics']['viewCount'];
		}

		if ( array_key_exists( 'liveStreamingDetails', $video_data ) &&
			 array_key_exists( 'scheduledStartTime', $video_data['liveStreamingDetails'] ) ) {
			$video->is_live_video         = true;
			$video->live_video_start_date = $video_data['liveStreamingDetails']['scheduledStartTime'];
			try {
				$end_date = new \DateTime( $video->live_video_start_date );
				/**
				 * the google doc says :
				 * It is required to provide the endDate once the video has finished and is no longer live.
				 * If the expected endDate is unknown prior to the livestream starting, we recommend providing an approximate endDate.
				 */
				// we add 1 day to start date
				$end_date->add( new DateInterval( 'P1D' ) );
				$video->live_video_end_date = $end_date->format( 'c' );
			// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			} catch ( \Exception $e ) {
				// No need to do anything here.
			}
		}

		$video->id = $video->content_url;

		return $video;

	}

}
