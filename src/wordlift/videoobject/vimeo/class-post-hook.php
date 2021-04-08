<?php

namespace Wordlift_Videoobject;

use Wordlift_Videoobject\Vimeo\Acf_Data_Service;
use Wordlift_Videoobject\Vimeo\Data_Service;
use Wordlift_Videoobject\Vimeo\Video_Url_Service;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Post_Hook extends Singleton {

	/**
	 * The number of requests sent to vimeo api.
	 * @var int
	 */
	public $request_count = 0;

	/**
	 * @var Data_Service
	 */
	private $api_service;

	/**
	 * @var Video_Url_Service
	 */
	private $video_url_service;
	/**
	 * @var Acf_Data_Service
	 */
	private $acf_data_service;


	public function __construct() {
		parent::__construct();
		$this->api_service       = Data_Service::get_instance();
		$this->video_url_service = Video_Url_Service::get_instance();
		$this->acf_data_service  = Acf_Data_Service::get_instance();
	}


	public function register_save_post_hook() {
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

		if ( ! in_array( get_post_type($post_id), Config::SUPPORTED_POST_TYPES ) ) {
			// Dont add jsonld to unsupported post types.
			return false;
		}

		$video_urls = $this->video_url_service->get_vimeo_video_urls( $post_id );

		$video_urls = apply_filters( 'wordlift_videoobject_video_urls', $video_urls, $post_id );


		$video_ids = $this->video_url_service->get_video_ids_for_api( $video_urls );

		if ( $video_urls ) {
			// send the request to api.
			$videos = $this->api_service->get_videos_data( $video_ids, $post_id );
			foreach ( $videos as $video ) {
				$this->acf_data_service->add_video( $video, $post_id );
			}
			$this->request_count += 1;
		}
	}

}