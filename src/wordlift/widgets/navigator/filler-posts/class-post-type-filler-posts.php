<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Post_Type_Filler_Posts extends Filler_Posts {

	/**
	 * @var array<string>
	 */
	private $post_types;

	public function __construct( $post_id, $filler_count, $post_ids_to_be_excluded, $post_types ) {
		parent::__construct( $post_id, $filler_count, $post_ids_to_be_excluded );
		$this->post_types = $post_types;
	}

	function get_posts() {
		return get_posts( array( 'post_type' => $this->post_types ) + $this->get_posts_config() );
	}
}