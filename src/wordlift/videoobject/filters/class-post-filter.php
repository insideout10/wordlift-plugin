<?php

namespace Wordlift\Videoobject\Filters;

use Wordlift\Videoobject\Video_Processor;

/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Post_Filter {

	/**
	 * @var Video_Processor
	 */
	private $video_processor;

	public function __construct( $video_processor ) {
		$this->video_processor = $video_processor;
	}

	public function init() {
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * @param $post_id int
	 * @param $post \WP_Post
	 * @param $update bool
	 */
	public function save_post( $post_id, $post ) {

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$this->video_processor->process_video_urls( $post, $post_id );

	}

}
