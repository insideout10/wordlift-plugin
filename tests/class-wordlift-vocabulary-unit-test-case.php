<?php
abstract class Wordlift_Vocabulary_Unit_Test_Case  extends Wordlift_Unit_Test_Case {

	function setUp() {
		parent::setUp();
		if ( ! taxonomy_exists('post_tag') ) {
			register_taxonomy('post_tag', 'post');
		}
	}
}