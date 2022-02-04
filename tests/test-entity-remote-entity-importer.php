<?php
/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Entity\Remote_Entity\Invalid_Remote_Entity;
use Wordlift\Entity\Remote_Entity\Valid_Remote_Entity;
use Wordlift\Entity\Remote_Entity_Importer\Invalid_Remote_Entity_Importer;
use Wordlift\Entity\Remote_Entity_Importer\Remote_Entity_Importer_Factory;
use Wordlift\Entity\Remote_Entity_Importer\Valid_Remote_Entity_Importer;

/**
 * Class Entity_Remote_Entity_Importer_Test
 * @group entity
 */
class Entity_Remote_Entity_Importer_Test extends Wordlift_Unit_Test_Case {


	public function test_given_invalid_entity_should_return_invalid_importer() {
		$importer = Remote_Entity_Importer_Factory::from_entity(
			new Invalid_Remote_Entity()
		);
		$this->assertTrue( $importer instanceof Invalid_Remote_Entity_Importer );
		$this->assertFalse( $importer->import(), 'Cant import invalid entities, So it should return false' );
	}


	public function test_given_valid_entity_should_return_valid_importer() {
		$importer = Remote_Entity_Importer_Factory::from_entity(
			new Valid_Remote_Entity(
				array( 'Thing' ),
				'name',
				'description',
				array( 'https://schema.org' )
			)
		);
		$this->assertTrue( $importer instanceof Valid_Remote_Entity_Importer );
	}

	public function test_given_valid_entity_should_import_to_db_correctly() {

		$valid_remote_entity = new Valid_Remote_Entity(
			array( 'Thing' ),
			'name',
			'description',
			array( 'https://schema.org' )
		);

		$importer = Remote_Entity_Importer_Factory::from_entity( $valid_remote_entity );

		$importer->import();

		// Check if import is correct.

	}


}
