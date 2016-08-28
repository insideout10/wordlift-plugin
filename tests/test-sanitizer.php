<?php

/**
 */
class Wordlift_Sanitizer_Test extends WP_UnitTestCase {

	public function test() {


		$this->assertNotNull( Wordlift_Sanitizer::sanitize_url( 'http://dbpedia.org/resource/Huttau' ) );

		$this->assertNotNull( Wordlift_Sanitizer::sanitize_url( 'http://dbpedia.org/resource/Hüttau' ) );


	}

}
