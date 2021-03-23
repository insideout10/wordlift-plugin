<?php
/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Badge_Test extends \Wordlift_Vocabulary_Unit_Test_Case {



	public function test_round_to_nearest_hundred_for_term_count() {

		$result = Vocabulary_Badge_Generator::round_to_nearest_hundred(340);
		$this->assertEquals( 300, $result);
		$result = Vocabulary_Badge_Generator::round_to_nearest_hundred(290);
		$this->assertEquals( 200, $result);
		$result = Vocabulary_Badge_Generator::round_to_nearest_hundred(200);
		$this->assertEquals( 200, $result);

	}



}