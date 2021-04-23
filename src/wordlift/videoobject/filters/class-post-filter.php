<?php

namespace Wordlift\Videoobject\Filters;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Meta_Storage;
use Wordlift\Videoobject\Data\Video_Storage\Storage;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Parser\Parser_Factory;
use Wordlift\Videoobject\Provider\Provider_Factory;

/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Post_Filter {

	public function init() {
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
	}

	/**
	 * @param $post_id int
	 * @param $post \WP_Post
	 * @param $update bool
	 */
	public function save_post( $post_id, $post, $update ) {

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$this->process_video_urls( $post, $post_id );

	}


	private function get_data_for_videos( $embedded_videos ) {

		$youtube_videos = $this->get_youtube_videos( $embedded_videos );

		$youtube_provider = Provider_Factory::get_provider( Provider_Factory::YOUTUBE );
		$youtube_videos   = $youtube_provider->get_videos_data( $youtube_videos );

		$vimeo_videos = $this->get_vimeo_videos( $embedded_videos );

		$vimeo_provider = Provider_Factory::get_provider( Provider_Factory::VIMEO );
		$vimeo_videos   = $vimeo_provider->get_videos_data( $vimeo_videos );

		return array_merge( $youtube_videos, $vimeo_videos );

	}

	/**
	 * @param $storage Storage
	 * @param $post_id int
	 * @param $embedded_videos array<Embedded_Video>
	 *
	 * @return int Return the count of the videos which got deleted.
	 */
	private function remove_videos_from_store_if_not_present_in_content( $storage, $post_id, $embedded_videos ) {

		$videos_to_be_deleted = $this->get_videos_to_be_deleted( $storage, $post_id, $embedded_videos );
		$storage->remove_videos( $videos_to_be_deleted, $post_id );
		return count($videos_to_be_deleted);
	}

	/**
	 * @param Storage $storage
	 * @param $post_id
	 * @param array $embedded_videos
	 *
	 * @return array
	 */
	private function get_videos_to_be_deleted( Storage $storage, $post_id, array $embedded_videos ) {
		$videos_in_store     = $storage->get_all_videos( $post_id );
		$embedded_video_urls = array_map( function ( $embedded_video ) {
			/**
			 * @var $embedded_video Embedded_Video
			 */
			return $embedded_video->get_url();
		}, $embedded_videos );

		return array_filter( $videos_in_store, function ( $video ) use ( $embedded_video_urls ) {
			/**
			 * @var $video Video
			 */
			return ! in_array( $video->id, $embedded_video_urls );
		} );
	}

	/**
	 * @param \WP_Post $post
	 * @param $post_id
	 */
	private function process_video_urls( \WP_Post $post, $post_id ) {

		$parser = Parser_Factory::get_parser_from_content( $post->post_content );

		$embedded_videos = $parser->get_videos( $post_id );

		$storage = Video_Storage_Factory::get_storage();

		// Before sending api requests we need to check if there are any videos in
		// store which is not present on post content, remove them if there are
		// any
		$deleted_videos_count = $this->remove_videos_from_store_if_not_present_in_content( $storage, $post_id, $embedded_videos );

		if ( $deleted_videos_count ) {
			/**
			 * @since 3.31.0
			 * Fires when the videos are deleted from the post.
			 */
			do_action('wordlift_videoobject_videos_deleted', $post_id);
		}

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

		/**
		 * @since 3.31.0
		 * Fires when the videos are added to the post.
		 */
		do_action('wordlift_videoobject_videos_added', $post_id);
	}

	/**
	 * @param $storage Storage
	 * @param $post_id int
	 * @param $embedded_videos array<Embedded_Video>
	 *
	 * @return array<Embedded_Video> Return array of embedded videos which are not in store.
	 */
	private function get_videos_without_existing_data( $storage, $post_id, $embedded_videos ) {
		$videos_in_store    = $storage->get_all_videos( $post_id );
		$video_ids_in_store = array_map( function ( $video ) {
			return $video->id;
		}, $videos_in_store );

		return array_filter( $embedded_videos, function ( $embedded_video ) use ( $video_ids_in_store ) {
			return ! in_array( $embedded_video->get_url(), $video_ids_in_store );
		} );
	}

	/**
	 * @param $embedded_videos
	 *
	 * @return array
	 */
	private function get_youtube_videos( $embedded_videos ) {
		return array_filter( $embedded_videos, function ( $embedded_video ) {
			/**
			 * it should have youtube.com or youtu.be in the url
			 *
			 * @param $embedded_video Embedded_Video
			 */
			$video_url = $embedded_video->get_url();

			return strpos( $video_url, "youtube.com" ) !== false ||
			       strpos( $video_url, "youtu.be" ) !== false;
		} );
	}

	/**
	 * @param $embedded_videos
	 *
	 * @return array
	 */
	private function get_vimeo_videos( $embedded_videos ) {
		return array_filter( $embedded_videos, function ( $embedded_video ) {
			/**
			 * it should have vimeo.com in the url
			 *
			 * @param $embedded_video Embedded_Video
			 */
			$video_url = $embedded_video->get_url();

			return strpos( $video_url, "vimeo.com" ) !== false;
		} );
	}

}