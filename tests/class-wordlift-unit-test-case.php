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
	 * The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.16.0
	 * @access protected
	 * @var \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	protected $entity_type_service;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.16.0
	 * @access protected
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	protected $configuration_service;

	/**
	 * The {@link WP_Post} id of the publisher.
	 *
	 * @since  3.16.0
	 * @access protected
	 * @var int $publisher_id The {@link WP_Post} id of the publisher.
	 */
	protected $publisher_id;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		delete_option( 'wl_db_version' );
		wl_core_update_db_check();

		// Default behaviour: push entities to the remote Linked Data store.
		self::turn_off_entity_push();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		$this->entity_factory = new Wordlift_UnitTest_Factory_For_Entity( $this->factory );

		$this->wordlift_test = new Wordlift_Test();

		$this->entity_type_service   = $this->wordlift_test->get_entity_type_service();
		$this->configuration_service = $this->wordlift_test->get_configuration_service();

		// Set up the publisher.
		$this->setup_publisher();

		add_filter( 'wp_doing_ajax', '__return_false' );

	}

	function tearDown() {

		remove_filter( 'wp_doing_ajax', '__return_false' );

		$this->teardown_publisher();

		parent::tearDown();
	}

	/**
	 * Set up the publisher.
	 *
	 * @since 3.16.0
	 */
	private function setup_publisher() {

		$this->publisher_id = wp_insert_post( array(
			'post_type'  => 'entity',
			'post_title' => 'Edgar Allan Poe',
		) );
		$this->entity_type_service->set( $this->publisher_id, 'http://schema.org/Person' );
		$this->configuration_service->set_publisher_id( $this->publisher_id );

	}

	private function teardown_publisher() {

		wp_delete_post( $this->publisher_id );
		$this->configuration_service->set_publisher_id( null );

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

	/**
	 * Utility method that resets permalinks and flushes rewrites.
	 *
	 * @since 3.16.0
	 *
	 * @global WP_Rewrite $wp_rewrite
	 *
	 * @param string      $structure Optional. Permalink structure to set. Default empty.
	 */
	public function set_permalink_structure( $structure = '' ) {
		global $wp_rewrite;

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( $structure );
		$wp_rewrite->flush_rules();
	}

}
