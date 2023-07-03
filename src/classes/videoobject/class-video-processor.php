<?php

namespace Wordlift\Videoobject;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Video_Storage\Storage;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Parser\Parser_Factory;
use Wordlift\Videoobject\Provider\Client\Client;
use Wordlift\Videoobject\Provider\Client\Client_Factory;
use Wordlift\Videoobject\Provider\Provider_Factory;

class Video_Processor {

	/**
	 * @var array<Client>
	 */
	private $api_clients;

	public function __construct() {
		$this->api_clients = array(
			Client_Factory::get_client( Client_Factory::YOUTUBE ),
			Client_Factory::get_client( Client_Factory::VIMEO ),
			Client_Factory::get_client( Client_Factory::JWPLAYER ),
		);
	}

	private function get_data_for_videos( $embedded_videos ) {

		$youtube_videos   = $this->get_youtube_videos( $embedded_videos );
		$youtube_provider = Provider_Factory::get_provider( Provider_Factory::YOUTUBE );
		$youtube_videos   = $youtube_provider->get_videos_data( $youtube_videos );

		$vimeo_videos   = $this->get_vimeo_videos( $embedded_videos );
		$vimeo_provider = Provider_Factory::get_provider( Provider_Factory::VIMEO );
		$vimeo_videos   = $vimeo_provider->get_videos_data( $vimeo_videos );

		$jwplayer_videos   = $this->get_jw_player_videos( $embedded_videos );
		$jwplayer_provider = Provider_Factory::get_provider( Provider_Factory::JWPLAYER );
		$jwplayer_videos   = $jwplayer_provider->get_videos_data( $jwplayer_videos );

		return array_merge( $youtube_videos, $vimeo_videos, $jwplayer_videos );

	}

	private function get_video_ids( $video_urls ) {
		$clients   = $this->api_clients;
		$video_ids = array();
		foreach ( $clients as $client ) {
			$ids = $client->get_video_ids( $video_urls );
			if ( $ids ) {
				$video_ids = array_merge( $video_ids, $ids );
			}
		}

		return array_unique( $video_ids );
	}

	/**
	 * @param $storage Storage
	 * @param $post_id int
	 * @param $embedded_videos array<Embedded_Video>
	 */
	private function remove_videos_from_store_if_not_present_in_content( $storage, $post_id, $embedded_videos ) {

		$videos_to_be_deleted = $this->get_videos_to_be_deleted( $storage, $post_id, $embedded_videos );
		$storage->remove_videos( $videos_to_be_deleted, $post_id );

	}

	/**
	 * @param Storage $storage
	 * @param $post_id
	 * @param array   $embedded_videos
	 *
	 * @return array An array of videos which exist on storage, not on post content.
	 */
	private function get_videos_to_be_deleted( Storage $storage, $post_id, array $embedded_videos ) {
		$videos_in_store     = $storage->get_all_videos( $post_id );
		$embedded_video_urls = array_map(
			function ( $embedded_video ) {
				/**
				 * @var $embedded_video Embedded_Video
				 */
				return $embedded_video->get_url();
			},
			$embedded_videos
		);

		/**
		 * Previously we are checking if the captured url is content_url to delete it from the storage
		 * but this might not work well if we want to support multiple URL formats, we extract the video
		 * ids for embedded URLs.
		 */
		$embedded_video_ids = $this->get_video_ids( $embedded_video_urls );

		$that = $this;

		return array_filter(
			$videos_in_store,
			function ( $video ) use ( $embedded_video_ids, $that ) {
				/**
				 * If the video id doesn't exist on the content then we need to return it
				 * in order to delete that video.
				 */
				return count(
					array_intersect(
						$that->get_video_ids( array( $video->id ) ),
						$embedded_video_ids
					)
				) === 0;

			}
		);
	}

	/**
	 * @param \WP_Post $post
	 * @param $post_id
	 */
	public function process_video_urls( \WP_Post $post, $post_id ) {

		$parser = Parser_Factory::get_parser_from_content( $post->post_content );

		$embedded_videos = $parser->get_videos( $post_id );
		/**
		 * Filters the embedded videos on post contet, custom plugins can add their video urls
		 * by constructing \Default_Embedded_Video or implement Embedded_Video class
		 *
		 * @param $embedded_videos array<Embedded_Video>
		 * @param $post_id int The post id for the videoobject is processed.
		 *
		 * @return array<Embedded_Video>
		 * @since 3.31.4
		 * Filter name : wl_videoobject_embedded_videos
		 */
		$embedded_videos = apply_filters( 'wl_videoobject_embedded_videos', $embedded_videos, $post_id );

		$storage = Video_Storage_Factory::get_storage();

		// Before sending api requests we need to check if there are any videos in
		// store which is not present on post content, remove them if there are
		// any
		$this->remove_videos_from_store_if_not_present_in_content( $storage, $post_id, $embedded_videos );

		$embedded_videos = $this->get_videos_without_existing_data( $storage, $post_id, $embedded_videos );

		// Return early after removing all the videos.
		if ( ! $embedded_videos ) {
			return;
		}

		$videos = $this->get_data_for_videos( $embedded_videos );

		if ( ! $videos ) {
			return;
		}

		foreach ( $videos as $video ) {
			$storage->add_video( $post_id, $video );
		}
	}

	/**
	 * @param $storage Storage
	 * @param $post_id int
	 * @param $embedded_videos array<Embedded_Video>
	 *
	 * @return array<Embedded_Video> Return array of embedded videos which are not in store.
	 */
	private function get_videos_without_existing_data( $storage, $post_id, $embedded_videos ) {
		$videos_in_store     = $storage->get_all_videos( $post_id );
		$video_urls_in_store = array_map(
			function ( $video ) {
				return $video->id;
			},
			$videos_in_store
		);

		$video_ids_in_store = $this->get_video_ids( $video_urls_in_store );

		$that = $this;

		return array_filter(
			$embedded_videos,
			function ( $embedded_video ) use ( $video_ids_in_store, $that ) {
				/**
				 * If the video id exist on content, not on storage then
				 * we need to fetch the data.
				 */
				return count(
					array_intersect(
						$that->get_video_ids( array( $embedded_video->get_url() ) ),
						$video_ids_in_store
					)
				) === 0;
			}
		);
	}

	/**
	 * @param $embedded_videos
	 *
	 * @return array
	 */
	private function get_youtube_videos( $embedded_videos ) {
		return array_filter(
			$embedded_videos,
			function ( $embedded_video ) {
				/**
				 * it should have youtube.com or youtu.be in the url
				 *
				 * @param $embedded_video Embedded_Video
				 */
				$video_url = $embedded_video->get_url();

				return strpos( $video_url, 'youtube.com' ) !== false ||
				   strpos( $video_url, 'youtu.be' ) !== false;
			}
		);
	}

	/**
	 * @param $embedded_videos
	 *
	 * @return array
	 */
	private function get_vimeo_videos( $embedded_videos ) {
		return array_filter(
			$embedded_videos,
			function ( $embedded_video ) {
				/**
				 * it should have vimeo.com in the url
				 *
				 * @param $embedded_video Embedded_Video
				 */
				$video_url = $embedded_video->get_url();

				return strpos( $video_url, 'vimeo.com' ) !== false;
			}
		);
	}

	private function get_jw_player_videos( $embedded_videos ) {
		return array_filter(
			$embedded_videos,
			function ( $embedded_video ) {
				/**
				 * it should have vimeo.com in the url
				 *
				 * @param $embedded_video Embedded_Video
				 */
				$video_url = $embedded_video->get_url();

				return strpos( $video_url, 'https://cdn.jwplayer.com/v2/media/', 0 ) !== false;
			}
		);
	}

}
