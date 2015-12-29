<?php

class WordLift_JavaScriptPostIdFilter {

	public function get( $content ) {
		$postId = get_the_ID();
		return $content . "<input type=\"hidden\" name=\"postId\" value=\"$postId\" />";
	}

}

?>