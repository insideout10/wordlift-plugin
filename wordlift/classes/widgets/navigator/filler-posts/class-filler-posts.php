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
	public function __construct( $post_id, $alternate_post_type = null ) {

		$this->post_id             = $post_id;
		$this->alternate_post_type = $alternate_post_type;

	}

	protected function get_posts_config( $filler_count, $post_ids_to_be_excluded ) {

		return array(
			'meta_query'          => array(
				array(
					'key' => '_thumbnail_id',
				),
			),
			'numberposts'         => $filler_count,
			'post__not_in'        => $post_ids_to_be_excluded,
			'ignore_sticky_posts' => 1,
		);

	}

	/**
	 * @param $filler_count
	 * @param $post_ids_to_be_excluded
	 *
	 * @return array<\WP_Post>
	 */
	abstract public function get_posts( $filler_count, $post_ids_to_be_excluded );

}
