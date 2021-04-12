<?php

namespace Wordlift\Videoobject\Filters;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Video\Video;
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

		$videos = $this->get_data_for_videos( $embedded_videos );


		foreach ( $videos as $video ) {
			$storage->add_video( $post_id, $video );
		}


	}


	private function get_data_for_videos( $embedded_videos ) {
		$youtube_provider = Provider_Factory::get_provider( Provider_Factory::YOUTUBE );

		return $youtube_provider->get_videos_data( $embedded_videos );

	}

}