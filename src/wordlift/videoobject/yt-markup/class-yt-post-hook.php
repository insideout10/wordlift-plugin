<?php
/**
 * @since 2.50.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Yt_Markup;


class Yt_Post_Hook extends Singleton {

	const VIDEO_EMBED_SHORT_CODE_PATTERN = '/(?:https?:\/\/)?(?:youtu\.be\/|(?:www\.|m\.)?youtube\.com\/(?:watch|v|embed)(?:\.php)?(?:\?.*v=|\/))([a-zA-Z0-9\_-]+)/m';
	/**
	 * @var Yt_Data_Service
	 */
	private $yt_data_service;
	/**
	 * @var Yt_Acf_Data_Service
	 */
	private $yt_acf_data_service;
	/**
	 * Count of requests sent by the post hook.
	 * @var int
	 */
	private $requests_sent;

	public function __construct() {
		parent::__construct();
		$this->yt_data_service     = Yt_Data_Service::get_instance();
		$this->yt_acf_data_service = Yt_Acf_Data_Service::get_instance();
		$this->requests_sent       = 0;
	}

	/**
	 * @return Yt_Post_Hook
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	public function register_post_save_hook() {
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
	}

	/**
	 * @param $post_id int post id
	 * @param $post \WP_Post Post instance
	 * @param $update  bool Whether this is an existing post being updated.
	 *
	 * @return bool
	 */
	public function save_post( $post_id, $post, $update ) {

		if ( wp_is_post_revision( $post_id ) ) {
			// Dont run if it inside post revision.
			return false;
		}

		$post_type = get_post_type( $post );

		if ( ! in_array( $post_type, Yt_Config::get_supported_post_types() ) ) {
			return false;
		}

		// check if there is any embedded videos in the saved post.
		$post_content = $post->post_content;
		$matches      = array();
		// search for all the embedded tag
		preg_match_all( self::VIDEO_EMBED_SHORT_CODE_PATTERN,
			$post_content,
			$matches, PREG_SET_ORDER );
		$video_urls = $this->get_video_urls_from_matches( $matches );
		/**
		 * @return array<string> Should return the filtered list.
		 * @var $videos array<string> List of youtube video urls found on the content.
		 * @var $post_id int The post id.
		 * @since 2.50.0
		 */
		$video_urls = array_unique( $video_urls );

		$video_urls = apply_filters( 'wordlift_videoobject_youtube_urls', $video_urls, $post_id, $post );

		$video_urls = array_unique( $video_urls );

		if ( ! $video_urls ) {
			// return early, if the url is not valid.
			return false;
		}

		$videos = $this->yt_data_service->get_data( $video_urls );

		// Update the request sent ( used for testing).
		$this->requests_sent += 1;

		foreach ( $videos as $video ) {
			/**
			 * @var $video Yt_Video_Data
			 */
			$this->yt_acf_data_service->add_video( $video, $post_id );
		}
	}

	private function get_video_urls_from_matches( $matches ) {
		return array_map( function ( $item ) {
			return array_key_exists( 0, $item ) ? $item[0] : false;
		}, $matches );

	}

	/**
	 * Reset the requests sent by the post hook.
	 */
	public function reset_requests_sent() {
		$this->requests_sent = 0;
	}

	public function get_requests_sent() {
		return $this->requests_sent;
	}
}
