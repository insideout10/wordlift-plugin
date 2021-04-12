<?php

namespace Wordlift\Videoobject\Filters;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Parser\Parser_Factory;

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

		$videos = array_map( array( $this, 'get_video' ), $embedded_videos );


	}

	/**
	 * @param $embedded_video Embedded_Video
	 *
	 * @return Video
	 */
	public function get_video( $embedded_video ) {

	}

}