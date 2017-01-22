<?php

/**
 */
class Wordlift_Entity_Type_Service_Test extends WP_UnitTestCase {

	/**
	 * Check that the specified slug is used.
	 */
	public function test_vocabulary_slug() {

		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', 'vocabulary' );

		$this->assertEquals( 'vocabulary', $entity_type_service->get_slug() );

	}

	/**
	 * Check that, when an empty slug is used, the default entity post type name is used.
	 */
	public function test_empty_slug() {

		$entity_type_service = new Wordlift_Entity_Post_Type_Service( 'entity', '' );

		$this->assertEquals( 'entity', $entity_type_service->get_slug() );

	}

}
