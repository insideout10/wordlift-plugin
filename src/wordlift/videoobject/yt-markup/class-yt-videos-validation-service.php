<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Yt_Markup;


/**
 * This class helps to validate the video urls on post content, check the acf data
 * source, if the video exists, then it removes the video, it also removes the
 * video from acf data if it is not present in post content.
 *
 * Class Yt_Videos_Validation_Service
 */
class Yt_Videos_Validation_Service extends Singleton {

	/**
	 * @var Yt_Acf_Data_Service
	 */
	private $yt_acf_data_service;

	protected function __construct() {
		parent::__construct();
		$this->yt_acf_data_service = Yt_Acf_Data_Service::get_instance();
	}

	/**
	 * @return Yt_Videos_Validation_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	public function register_videos_filter() {
		add_filter( 'wordlift_videoobject_youtube_urls',
			array( $this, 'filter_video_urls' ),
			PHP_INT_MAX, 2 );
	}

	/**
	 * @param $video_urls
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	public function filter_video_urls( $video_urls, $post_id ) {

		$storage_videos    = $this->yt_acf_data_service->get_videos( $post_id );
		$storage_urls      = $this->get_storage_video_urls( $storage_videos );
		$storage_video_ids = Yt_Data_Service::get_video_ids( $storage_urls );
		$post_video_ids    = Yt_Data_Service::get_video_ids( $video_urls );
		/**
		 * The order of the array_diff matters,
		 * the `remove_videos_from_acf_if_not_present_in_post_content` method needs the ids present on acf store not on post
		 * content, where as we need to return the ids which are not present in acf store from the
		 * current method.
		 */
		$filter_diff_video_ids  = array_diff( $post_video_ids, $storage_video_ids );
		$filter_diff_video_urls = array_values( array_map( function ( $video_id ) {
			return "https://www.youtube.com/watch?v=${video_id}";
		}, $filter_diff_video_ids ) );


		$acf_store_video_ids = array_diff( $storage_video_ids, $post_video_ids );
		$acf_store_video_urls = array_values( array_map( function ( $video_id ) {
			return "https://www.youtube.com/watch?v=${video_id}";
		}, $acf_store_video_ids ) );

		// also check if there are urls in the acf storage which does not
		// present in post content, if yes we need to remove it.
		$this->remove_videos_from_acf_if_not_present_in_post_content( $post_id, $acf_store_video_urls );

		return $filter_diff_video_urls;
	}

	private function get_storage_video_urls( $storage_videos ) {
		return array_map( function ( $item ) {
			/**
			 * @var $item Yt_Video_Data
			 */
			return $item->content_url;
		}, $storage_videos );
	}

	/**
	 * @param $post_id int post id.
	 * @param $diff_video_urls array
	 */
	private function remove_videos_from_acf_if_not_present_in_post_content(
		$post_id,
		$diff_video_urls
	) {
		$this->yt_acf_data_service->remove_videos_by_url_list( $diff_video_urls, $post_id );
	}

}
