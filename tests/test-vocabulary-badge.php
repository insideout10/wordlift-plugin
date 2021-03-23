<?php

use Wordlift\Vocabulary\Menu\Badge\Badge_Generator;

/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Badge_Test extends \Wordlift_Vocabulary_Unit_Test_Case {



	public function test_round_to_nearest_hundred_for_term_count() {

		$result = Badge_Generator::round_to_nearest_hundred(340);
		$this->assertEquals( 300, $result);
		$result = Badge_Generator::round_to_nearest_hundred(290);
		$this->assertEquals( 200, $result);
		$result = Badge_Generator::round_to_nearest_hundred(200);
		$this->assertEquals( 200, $result);
		$result = Badge_Generator::round_to_nearest_hundred(70);
		$this->assertEquals( 70, $result);

	}


	public function test_badge_generated_html() {
		$result = Badge_Generator::generate_html( 340 );
		$expected_html = "<span class=\"wl-admin-menu-badge\">300+</span>";
		$this->assertEquals( $result, $expected_html );
	}

	public function test_badge_generated_html_for_numbers_less_than_100_should_return_without_plus_sign() {
		$result = Badge_Generator::generate_html( 70 );
		$expected_html = "<span class=\"wl-admin-menu-badge\">70</span>";
		$this->assertEquals( $result, $expected_html );
	}



}