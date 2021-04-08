<?php
/**
 * This class stores single video data in the instance.
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Yt_Markup;

class Yt_Video_Data {

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
	 */
	public function __construct( $api_response_data ) {

		$this->set_snippet_properties( $api_response_data );

		if ( array_key_exists( 'contentDetails', $api_response_data ) ) {
			$this->duration = $api_response_data['contentDetails']['duration'];
		}

		if ( array_key_exists( 'id', $api_response_data ) ) {
			$video_id          = $api_response_data['id'];
			$this->embed_url   = "https://www.youtube.com/embed/${video_id}";
			$this->content_url = "https://www.youtube.com/watch?v=${video_id}";
		}

	}

	/**
	 * This method should set name, description, upload_date
	 */
	public function set_snippet_properties( $api_response_data ) {
		if ( ! array_key_exists( 'snippet', $api_response_data ) ) {
			return false;
		}

		$this->name        = $api_response_data['snippet']['title'];
		$this->description = $api_response_data['snippet']['description'];

		/**
		 * @since 1.0.1
		 * Use title as fallback if description is not present.
		 */
		if ( ! $this->description ) {
			 $this->description = $this->name;
		}

		$this->upload_date = $api_response_data['snippet']['publishedAt'];

		if ( array_key_exists( 'thumbnails', $api_response_data['snippet'] ) ) {
			$this->thumbnail_url = array_values( $api_response_data['snippet']['thumbnails'] );
		}

		return true;
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

}
