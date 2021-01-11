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
	protected $filler_count;

	/**
	 * @var array<int>
	 */
	protected $post_ids_to_be_excluded;

	/**
	 * @var $post_id int
	 */
	protected $post_id;

	/**
	 * Filler_Posts constructor.
	 *
	 * @param $post_id
	 * @param $filler_count int
	 * @param $post_ids_to_be_excluded array
	 */
	public function __construct( $post_id, $filler_count, $post_ids_to_be_excluded ) {

		$this->post_id = $post_id;

		$this->filler_count = $filler_count;

		$this->post_ids_to_be_excluded = $post_ids_to_be_excluded;

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

	abstract function get_posts();


}
