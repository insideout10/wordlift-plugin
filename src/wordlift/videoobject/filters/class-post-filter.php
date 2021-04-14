<?php

namespace Wordlift\Videoobject\Filters;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Video\Video;
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

		$parser = Parser_Factory::get_parser_from_content( $post->post_content );

		$embedded_videos = $parser->get_videos( $post_id );

		$storage = Video_Storage_Factory::get_storage();

		// Before sending api requests we need to check if there are any videos in
		// store which is not present on post content, remove them if there are
		// any
		$this->remove_videos_from_store_if_not_present_in_content( $storage, $post_id, $embedded_videos );


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


	private function get_data_for_videos( $embedded_videos ) {

		$youtube_provider = Provider_Factory::get_provider( Provider_Factory::YOUTUBE );
		$youtube_videos   = $youtube_provider->get_videos_data( $embedded_videos );
		$vimeo_provider   = Provider_Factory::get_provider( Provider_Factory::VIMEO );
		$vimeo_videos     = $vimeo_provider->get_videos_data( $embedded_videos );

		return array_merge( $youtube_videos, $vimeo_videos );

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
	 * @param array $embedded_videos
	 *
	 * @return array
	 */
	private function get_videos_to_be_deleted( Storage $storage, $post_id, array $embedded_videos ) {
		$videos_in_store = $storage->get_all_videos( $post_id );


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

}