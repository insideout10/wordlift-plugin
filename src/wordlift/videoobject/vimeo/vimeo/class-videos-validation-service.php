<?php

namespace Wordlift_Videoobject\Vimeo;

use Wordlift_Videoobject\Singleton;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Videos_Validation_Service extends Singleton {
	/**
	 * @return Videos_Validation_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	/**
	 * @var Acf_Data_Service
	 */
	private $acf_data_service;

	protected function __construct() {
		parent::__construct();
		$this->acf_data_service = Acf_Data_Service::get_instance();
	}


	public function register_videos_filter() {
		add_filter( 'wordlift_videoobject_video_urls',
			array( $this, 'filter_video_urls' ),
			10, 2 );
	}

	/**
	 * @param $video_urls
	 *
	 * @param $post_id
	 *
	 * @return array|string[]
	 */
	public function filter_video_urls( $video_urls, $post_id ) {
		$storage_videos = $this->acf_data_service->get_videos( $post_id );
		$storage_urls   = $this->get_storage_video_urls( $storage_videos );
		// also check if there are urls in the acf storage which does not
		// present in post content, if yes we need to remove it.
		$this->remove_videos_from_acf_if_not_present_in_post_content( $post_id, $video_urls, $storage_urls );

		return array_diff( $video_urls, $storage_urls );
	}

	private function get_storage_video_urls( $storage_videos ) {
		return array_map( function ( $item ) {
			/**
			 * @var $item Video_Data
			 */
			return $item->content_url;
		}, $storage_videos );
	}

	/**
	 * @param $post_id int post id.
	 * @param $video_urls array
	 * @param $storage_urls array
	 */
	private function remove_videos_from_acf_if_not_present_in_post_content(
		$post_id,
		$video_urls, $storage_urls
	) {
		$diff_video_urls = array_diff( $storage_urls, $video_urls );
		$this->acf_data_service->remove_videos_by_url_list( $diff_video_urls, $post_id );
	}


}
