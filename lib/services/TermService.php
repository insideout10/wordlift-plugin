<?php

class TermService {

	private $logger;
	
	function get_terms_slugs( &$terms ) {

		$slugs = array();
		foreach ($terms as $term) {
			$slugs[] = $term->slug;
		}

		return $slugs;
	}

}

?>