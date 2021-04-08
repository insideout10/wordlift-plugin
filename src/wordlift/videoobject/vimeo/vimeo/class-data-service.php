<?php

namespace Wordlift_Videoobject\Vimeo;

use Vimeo\Vimeo;
use Wordlift_Videoobject\Singleton;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Data_Service extends Singleton {

	/**
	 * Vimeo client id
	 * @var string
	 */
	private $client_id;

	/**
	 * Vimeo client secret
	 * @var string
	 */
	private $client_secret;

	/**
	 * Api token
	 * @var string
	 */
	private $client_api_token;
	/**
	 * @var Vimeo
	 */
	private $vimeo_client;

	public function __construct() {
		$this->client_id        = get_option( Settings_Page::CLIENT_ID_FIELD_NAME, false );
		$this->client_secret    = get_option( Settings_Page::CLIENT_SECRET_FIELD_NAME, false );
		$this->client_api_token = get_option( Settings_Page::API_KEY_FIELD_NAME, false );
		$this->vimeo_client     = new Vimeo( $this->client_id, $this->client_secret, $this->client_api_token );
		parent::__construct();
	}


	/**
	 * @param $video_uris array<string>
	 *
	 * @param $post_id
	 *
	 * @return array<Video_Data> Video_Data
	 */
	public function get_videos_data( $video_uris, $post_id ) {

		// if any one of the credentials are not present, return empty.
		if ( ! $this->client_id || ! $this->client_secret || ! $this->client_api_token ) {
			return array();
		}


		try {
			$response = $this->vimeo_client->request( '/videos',
				array(
					'uris'   => join( ',', $video_uris ),
					'fields' => 'name,description,link,uri,duration,release_time,pictures'
				) );

			// we need to parse the body.
			if ( ! array_key_exists( 'body', $response ) ||
			     ! array_key_exists( 'data', $response['body'] ) ) {
				return array();
			}

			$video_list = $response['body']['data'];

			if ( ! is_array( $video_list ) ) {
				// Return if we cant parse the response.
				return array();
			}

			return $this->create_video_objects_from_response( $video_list, $post_id );

		} catch ( \Exception $e ) {
			return array();
		}


	}

	/**
	 * @param array $video_list
	 *
	 * @param $post_id
	 *
	 * @return Video_Data[]
	 */
	private function create_video_objects_from_response( $video_list, $post_id ) {
		return array_map( function ( $single_video_data ) use ( $post_id ) {
			return new Video_Data( $single_video_data, $post_id );
		}, $video_list );
	}

}
