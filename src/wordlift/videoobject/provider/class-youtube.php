<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Provider;

use Wordlift\Videoobject\Data\Video\Video;

class Youtube extends Api_Provider {

	const API_URL = 'https://www.googleapis.com/youtube/v3/videos';

	const YT_API_FIELD_NAME = '__wl_video_object_youtube_api_key';


	public function get_videos_data( $videos ) {


		$urls = array_map( function ( $video ) {
			/**
			 * @param $video Video
			 */
			return $video->get_url();
		}, $videos );

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
		$video_ids = $this->get_video_ids_as_string( $video_urls );

		// If the video ids are empty Or api key not set, then dont send the request.
		if ( ! $video_ids || ! $this->get_api_key() ) {
			return array();
		}

		$url = add_query_arg( array(
			'part' => 'snippet,contentDetails,statistics',
			'id'   => $video_ids,
			'key'  => $this->get_api_key()
		), self::API_URL );

		$response      = wp_remote_get( $url );
		$response_body = wp_remote_retrieve_body( $response );

		return $this->parse_youtube_video_data_from_response( $response_body );
	}

	/**
	 * @param $video_urls array An array of youtube video urls.
	 *
	 * @return string
	 */
	public function get_video_ids_as_string( $video_urls ) {
		// validate the urls.
		$video_urls = array_filter( $video_urls, function ( $url ) {
			return filter_var( $url, FILTER_VALIDATE_URL );
		} );

		// extract the video ids.
		return join( ",", self::get_video_ids( $video_urls ) );
	}


	public static function get_video_ids( $video_urls ) {

		$video_ids = array_map( function ( $url ) {

			$parsed_url_data = parse_url( $url );

			if ( ( ! is_array( $parsed_url_data ) || ! array_key_exists( 'query', $parsed_url_data ) )
			     && strpos( $url, "youtu.be" ) === false ) {
				return false;
			}
			// For youtu.be urls, the video id is present in path.
			if ( strpos( $url, "youtu.be" ) !== false ) {
				$parsed_url_data['query'] = "v=" . substr( $parsed_url_data['path'], 1 );
			}

			$query_data_result = array();
			parse_str( $parsed_url_data['query'], $query_data_result );

			if ( ! array_key_exists( 'v', $query_data_result ) ) {
				return false;
			}

			return $query_data_result['v'];

		}, $video_urls );

		return array_values( array_filter( $video_ids ) );

	}

	private function get_api_key() {
		return get_option( self::YT_API_FIELD_NAME, false );
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
		 * @since 1.0.1
		 * Use title as fallback if description is not present.
		 */
		if ( ! $video->description ) {
			$video->description = $video->name;
		}

		$video->upload_date = $video_data['snippet']['publishedAt'];

		if ( array_key_exists( 'thumbnails', $video_data['snippet'] ) ) {
			$video->thumbnail_urls = array_values( $video_data['snippet']['thumbnails'] );
		}

		return $video;

	}


}