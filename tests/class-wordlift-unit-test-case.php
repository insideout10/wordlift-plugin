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
	 * Hold the existing screen, to switch between `in_admin` and not `in_admin` screens.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var null|WP_Screen The {@link WP_Screen} before being switched.
	 */
	private $screen;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		delete_option( 'wl_db_version' );

		Wordlift_Install_Service::get_instance()->install();

		// Default behaviour: push entities to the remote Linked Data store.
		self::turn_off_entity_push();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		$this->entity_factory = new Wordlift_UnitTest_Factory_For_Entity( $this->factory() );

		$this->wordlift_test = new Wordlift_Test();

		$this->entity_type_service   = $this->wordlift_test->get_entity_type_service();
		$this->configuration_service = $this->wordlift_test->get_configuration_service();

		// Set up the publisher.
		$this->setup_publisher();

		_wl_test_set_wp_die_handler();

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
	 * @return \Wordlift_Test The {@link Wordlift_Test} instance.
	 * @since 3.11.0
	 */
	public function get_wordlift_test() {

		return $this->wordlift_test;
	}

	/**
	 * Utility method that resets permalinks and flushes rewrites.
	 *
	 * @param string $structure Optional. Permalink structure to set. Default empty.
	 *
	 * @global WP_Rewrite $wp_rewrite
	 *
	 * @since 3.16.0
	 *
	 */
	public function set_permalink_structure( $structure = '' ) {
		global $wp_rewrite;

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( $structure );
		$wp_rewrite->flush_rules();
	}

	/**
	 * Change the current screen. Call `restore_current_screen` to restore the previous current screen.
	 *
	 * @see WordPress' own WP_Screen tests, file tests/phpunit/tests/admin/includesScreen.php
	 *
	 * @since 3.20.0
	 *
	 * @param string $hook_name The screen hook name.
	 */
	public function set_current_screen( $hook_name ) {

		$this->screen = get_current_screen( '' );

		set_current_screen( $hook_name );

	}

	/**
	 * Restore the current screen after a `set_current_screen` call.
	 *
	 * @since 3.20.0
	 */
	public function restore_current_screen() {

		$GLOBALS['current_screen'] = $this->screen;

	}

}
