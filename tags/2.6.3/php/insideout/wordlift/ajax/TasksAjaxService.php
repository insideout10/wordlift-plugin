<?php

class WordLift_TasksAjaxService {

	public $logger;

	public function get( $state ) {

		$args = array(
			"posts_per_page"  => -1,
			"offset"          => 0,
			"orderby"         => "post_date",
			"order"           => "DESC",
			"meta_key"        => "_wordlift_job_status",
			"meta_value"      => $state,
			"post_type"       => "post",
			"post_status"     => "any",
			"suppress_filters" => true );

		$postsArray = get_posts( $args );

		$posts = array();
		foreach ( $postsArray as &$post ) :
			array_push( $posts, array(
				"id" => $post->ID,
				"title" => $post->post_title,
				"edit" => get_edit_post_link( $post->ID, "" )
			) );
		endforeach;

		return $posts;
	}

}

?>