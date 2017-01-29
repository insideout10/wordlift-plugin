<?php

/**
 */
class Wordlift_Entity_Type_Service_Test extends Wordlift_Unit_Test_Case {

	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		$this->turn_off_entity_push();

	}

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

	/**
	 * @since 3.10.0
	 */
	public function test_posts() {
		// @todo
	}

	/**
	 * @since 3.10.0
	 */
	public function test_pages() {
		// @todo
	}

	/**
	 * @since 3.10.0
	 */
	public function test_anaything_else() {
		// @todo
	}

}
