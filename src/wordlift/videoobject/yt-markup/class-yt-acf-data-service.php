<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Yt_Markup;


class Yt_Acf_Data_Service extends Singleton {

	const VIDEO_FIELD_KEY = 'field_5f464265ade55';

	/**
	 * @return Yt_Acf_Data_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	/**
	 * Adds the video to acf fields if it is not present.
	 *
	 * @param $video_data Yt_Video_Data
	 * @param $post_id int
	 *
	 * @return bool
	 */
	public function add_video( $video_data, $post_id ) {
		// Return early if the acf is not loaded.
		if ( ! function_exists( 'add_row' ) ) {
			return false;
		}
		add_row(
			self::VIDEO_FIELD_KEY,
			$this->get_row_data( $video_data ),
			$post_id
		);
	}

	/**
	 * @param $video_data Yt_Video_Data
	 *
	 * @return array
	 */
	private function get_row_data( $video_data ) {
		return array(
			'name'          => $video_data->name,
			'description'   => $video_data->description,
			'upload_date'   => $video_data->upload_date,
			'embed_url'     => $video_data->embed_url,
			'content_url'   => $video_data->content_url,
			'thumbnail_url' => $video_data->thumbnail_url,
			'duration'      => $video_data->duration
		);
	}

	/**
	 * Return the Yt_Video_Data objects for post id
	 *
	 * @param $post_id int
	 *
	 * @return array<Yt_Video_Data>
	 */
	public function get_videos( $post_id ) {
		if ( ! function_exists('get_field') ) {
			return array();
		}
		$acf_videos = get_field( self::VIDEO_FIELD_KEY, $post_id );
		if ( ! is_array( $acf_videos ) ) {
			return array();
		}
		$video_data_list = array();
		foreach ( $acf_videos as $acf_video ) {
			$video_data = new Yt_Video_Data( array() );
			$video_data->set_from_acf_field_data( $acf_video );
			$video_data_list[] = $video_data;
		}

		return $video_data_list;
	}

	/**
	 * Remove the urls which are not present in post content but
	 * present in acf field.
	 *
	 * @param $diff_video_urls array<string>
	 * @param $post_id int
	 *
	 * @return bool
	 */
	public function remove_videos_by_url_list( $diff_video_urls, $post_id ) {
		if ( ! function_exists('get_field') ) {
			return false;
		}
		$acf_videos = get_field( self::VIDEO_FIELD_KEY, $post_id );
		if ( ! $acf_videos ) {
			return false;
		}
		foreach ( $acf_videos as $key => $value ) {
			if ( array_key_exists( 'content_url', $value ) &&
			     in_array( $value['content_url'], $diff_video_urls ) ) {
				unset( $acf_videos[ $key ] );
			}
		}
		update_field( self::VIDEO_FIELD_KEY, $acf_videos, $post_id );

		return true;
	}

}
