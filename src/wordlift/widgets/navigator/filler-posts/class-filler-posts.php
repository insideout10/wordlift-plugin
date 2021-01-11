<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
abstract class Filler_Posts {

	/**
	 * @var int
	 */
	public $filler_count;

	/**
	 * @var array<int>
	 */
	public $post_ids_to_be_excluded;

	/**
	 * @var $post_id int
	 */
	protected $post_id;

	/**
	 * Filler_Posts constructor.
	 *
	 * @param $post_id
	 */
	public function __construct( $post_id ) {

		$this->post_id = $post_id;

	}

	protected function get_posts_config() {

		return array(
			'meta_query'          => array(
				array(
					'key' => '_thumbnail_id'
				)
			),
			'numberposts'         => $this->filler_count,
			'post__not_in'        => $this->post_ids_to_be_excluded,
			'ignore_sticky_posts' => 1
		);

	}

	/**
	 * @return array<\WP_Post>
	 */
	abstract function get_posts();


}
