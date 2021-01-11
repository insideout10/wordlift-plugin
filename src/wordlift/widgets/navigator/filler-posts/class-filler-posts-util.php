<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Filler_Posts_Util {

	/**
	 * @var array<Filler_Posts>
	 */
	private $sources = array();

	public function __construct( $post_id, $post_types ) {
		$this->sources = array(
			new Post_Type_Filler_Posts( $post_id, $post_types ),
			new Same_Category_Filler_Posts( $post_id ),
			new Default_Filler_Posts( $post_id )
		);
	}


	public function get_filler_posts() {


	}


}
