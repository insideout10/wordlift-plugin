<?php
/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Entity\Remote_Entity;
use Wordlift\Entity\Url_To_Entity_Converter;

/**
 * Class Url_To_Entity_Converter_Test
 * @group entity
 */
class Url_To_Entity_Converter_Test extends Wordlift_Unit_Test_Case {


	public function test_given_a_dbpedia_url_should_convert_to_entity() {

		$converter = new Url_To_Entity_Converter();
		$entity = $converter->convert( 'https://dbpedia.org/test' );
		$this->assertTrue(
			$entity instanceof Remote_Entity
		);

	}


}
