<?php

class PostHelper {

	/*
	 * given an id returns its post id.
	 */
	function get_post_id( $id ) {
		$post_id = wp_is_post_revision( $id );
		if (false == $post_id) $post_id = $id;

		return $post_id;
	}

	/*
	 * returns a post, given an id.
	 */
	function get_post( $id ) {
		return get_post( $this->get_post_id($id) );
	}

	function is_autosave() {
		return ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE );
	}

}

$post_helper = new PostHelper();

?>