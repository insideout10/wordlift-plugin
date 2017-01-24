<?php

/**
 * Define the {@link Wordlift_Unit_Test_Case} class.
 *
 * @since   3.0.0
 * @package Wordlift
 */
abstract class Wordlift_Unit_Test_Case extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_UnitTest_Factory_For_Entity} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_UnitTest_Factory_For_Entity $entity_factory The {@link Wordlift_UnitTest_Factory_For_Entity} instance.
	 */
	protected $entity_factory;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		$this->entity_factory = new Wordlift_UnitTest_Factory_For_Entity( $this->factory );

	}

	/**
	 * Turn off pushing entities to the cloud using SPARQL.
	 *
	 * @since 3.10.0
	 */
	protected function turn_off_entity_push() {

		if ( ! defined( 'DISABLE_ENTITY_PUSH' ) ) {
			define( 'DISABLE_ENTITY_PUSH', true );
		}

	}

}
