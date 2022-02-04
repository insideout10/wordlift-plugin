<?php
/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Class Entity_Remote_Entity_Importer_Test
 * @group entity
 */
class Entity_Remote_Entity_Importer_Test extends Wordlift_Unit_Test_Case {


	public function test_given_invalid_entity_should_return_invalid_importer() {
		$importer = Remote_Entity_Importer_Factory::from_entity(
			new \Wordlift\Entity\Remote_Entity\Invalid_Remote_Entity()
		);
		$this->assertTrue( $importer instanceof Invalid_Remote_Entity_Importer );
	}



}
