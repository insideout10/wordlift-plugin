<?php

/**
 * Define the {@link Wordlift_Unit_Test_Case} class.
 *
 * @since               3.0.0
 * @package             Wordlift
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
	 * The {@link Wordlift_Test} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Test $wordlift_test The {@link Wordlift_Test} instance.
	 */
	private $wordlift_test;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		delete_option( 'wl_db_version' );

		// Default behaviour: push entities to the remote Linked Data store.
		self::turn_off_entity_push();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		$this->entity_factory = new Wordlift_UnitTest_Factory_For_Entity( $this->factory );

		$this->wordlift_test = new Wordlift_Test();

	}

	/**
	 * Turn off pushing entities to the cloud using SPARQL.
	 *
	 * @since 3.10.0
	 */
	public static function turn_off_entity_push() {

		set_transient( 'DISABLE_ENTITY_PUSH', true );

	}

	/**
	 * Turn on pushing entities to the cloud using SPARQL.
	 *
	 * @since 3.10.0
	 */
	public static function turn_on_entity_push() {

		set_transient( 'DISABLE_ENTITY_PUSH', false );

	}

	/**
	 * Get the {@link Wordlift_Test} instance.
	 *
	 * @since 3.11.0
	 * @return \Wordlift_Test The {@link Wordlift_Test} instance.
	 */
	public function get_wordlift_test() {

		return $this->wordlift_test;
	}

}
