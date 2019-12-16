<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Mapping_REST_Controller_Test class.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_DBO_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Mapping_REST_Controller} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_DBO $dbo_instance The {@link Wordlift_Mapping_DBO} instance to test.
	 */
	private $dbo_instance;

	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();	
		$this->dbo_instance = new Wordlift_Mapping_DBO();
	}

	/**
	 * Testing if instance is not null, check to determine this class is
	 * included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->dbo_instance );
	}

}
