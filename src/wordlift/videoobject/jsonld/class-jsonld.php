<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Jsonld;


use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Storage;

class Jsonld  {
	/**
	 * @var Storage
	 */
	private $video_storage;

	/**
	 * Jsonld constructor.
	 *
	 * @param $video_storage Storage
	 */
	public function __construct( $video_storage ) {
		add_action( 'wl_post_jsonld', array( $this, 'wl_post_jsonld' ), 10, 3 );
		$this->video_storage = $video_storage;
	}

	/**
	 * @param $existing_video_data string | array associative or sequential array.
	 * @param $new_video_data array Sequential array.
	 *
	 * @return array
	 */
	private function merge_video_data( $existing_video_data, $new_video_data ) {
		if ( ! is_array( $existing_video_data ) ) {
			$new_video_data[] = $existing_video_data;

			return $new_video_data;
		}

		if ( $this->is_associative_array( $existing_video_data ) ) {
			$new_video_data[] = $existing_video_data;

			return $new_video_data;
		}

		return array_merge( $existing_video_data, $new_video_data );
	}

	public function wl_post_jsonld( $jsonld, $post_id, $references ) {

		$video_jsonld = $this->get_jsonld( $post_id );
		if ( count( $video_jsonld ) === 0 ) {
			return $jsonld;
		}
		// Before adding the video jsonld check if the key
		// is present and additional data might be present,
		// if not present just add the data and return early.
		if ( ! array_key_exists( 'video', $jsonld ) ) {
			$jsonld['video'] = $video_jsonld;

			return $jsonld;
		}

		// since key exists, we need to merge the data based on type.
		$previous_video_data = $jsonld['video'];
		$jsonld['video']     = $this->merge_video_data( $previous_video_data, $video_jsonld );

		return $jsonld;
	}


	/**
	 * @param $post_id int Post id.
	 *
	 * @return array
	 */
	public function get_jsonld( $post_id ) {

		$videos = $this->video_storage->get_all_videos( $post_id );

		$jsonld = array();

		foreach ( $videos as $video ) {
			/**
			 * @var $video Video
			 */
			$jsonld[] = array(
				'@type'        => 'VideoObject',
				'name'         => $video->name,
				'description'  => $video->description,
				'contentUrl'   => $video->content_url,
				'embedUrl'     => $video->embed_url,
				'uploadDate'   => $video->upload_date,
				'thumbnailUrl' => $video->thumbnail_urls,
				'duration'     => $video->duration

			);
		}

		return $jsonld;
	}



	private function is_associative_array( $arr ) {
		if ( array() === $arr ) {
			return false;
		}

		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}


}
