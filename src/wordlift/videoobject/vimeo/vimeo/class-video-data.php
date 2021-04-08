<?php
/**
 * This class stores single video data in the instance.
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Vimeo;

class Video_Data {

	/**
	 * @var string The title of the video.
	 */
	public $name;

	/**
	 * @var string The video description.
	 */
	public $description;

	/**
	 * @var array Thumbnail url.
	 */
	public $thumbnail_url;

	/**
	 * @var string Published date.
	 */
	public $upload_date;

	/**
	 * @var string Video url.
	 */
	public $content_url;

	/**
	 * @var string Video duration in IS08601 format.
	 */
	public $duration;

	/**
	 * @var string
	 */
	public $embed_url;

	/**
	 * Yt_Video_Data constructor.
	 *
	 * @param $api_response_data array Api response for single video.
	 * @param $post_id
	 */
	public function __construct( $api_response_data, $post_id ) {
		if ( ! $api_response_data ) {
			// If valid data not supplied dont init the object.
			return;
		}
		$this->name        = $api_response_data['name'];
		$this->description = $api_response_data['description'];

		// check if description is empty.
		if ( empty( $this->description ) ) {
			$this->description = $this->get_fallback_description( $post_id );
		}
		$this->content_url = $api_response_data['link'];
		$this->embed_url   = "https://player.vimeo.com/video/" . $this->get_id( $api_response_data );
		if ( is_numeric( $api_response_data['duration'] ) ) {
			$this->duration = "PT" . $api_response_data['duration'] . "S";
		}
		$this->upload_date = $api_response_data['release_time'];
		$this->set_thumbnail_urls( $api_response_data );

	}


	public function set_from_acf_field_data( $acf_field_data ) {
		$this->name          = $acf_field_data['name'];
		$this->description   = $acf_field_data['description'];
		$this->thumbnail_url = $acf_field_data['thumbnail_url'];
		$this->duration      = $acf_field_data['duration'];
		$this->upload_date   = $acf_field_data['upload_date'];
		$this->content_url   = $acf_field_data['content_url'];
		$this->embed_url     = $acf_field_data['embed_url'];
	}

	private function get_id( $api_response_data ) {
		return str_replace( "/videos/", "", $api_response_data['uri'] );
	}

	private function set_thumbnail_urls( $api_response_data ) {

		if ( ! array_key_exists( 'pictures', $api_response_data ) || ! array_key_exists( 'sizes',
				$api_response_data['pictures'] ) ) {
			return;
		}
		if ( ! is_array( $api_response_data['pictures']['sizes'] ) ) {
			return;
		}
		$pictures = $api_response_data['pictures']['sizes'];

		$this->thumbnail_url = array_map( function ( $picture_data ) {
			return array(
				'height' => (int) $picture_data['height'],
				'width'  => (int) $picture_data['width'],
				'url'    => $picture_data['link'],
			);

		},
			$pictures );

	}

	private function get_fallback_description( $post_id ) {

		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$seo_description = get_field( 'wpcf-seo', $post_id ) ?: '';

		$seo_bullets = get_field( 'seo_bullets', $post_id ) ?: '';

		return $seo_description . $seo_bullets;

	}

}
