<?php
/**
 * Tests: Install 1.0.0 Test.
 *
 * Test the {@link Wordlift_Install_1_0_0} class.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Install_1_0_0_Test class.
 *
 * @since 3.20.0
 * @group install
 */
class Wordlift_Install_1_0_0_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Install_1_0_0} instance to test.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Install_1_0_0 $install The {@link Wordlift_Install_1_0_0} instance.
	 */
	private $install;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->install = new Wordlift_Install_1_0_0();

	}

	/**
	 * Test the `install` function.
	 *
	 * @since 3.20.0
	 */
	public function test_install() {

		if ( empty( $this->configuration_service->get_key() ) ) {
			$this->markTestSkipped( 'The env WORDLIFT_KEY must be set for this test to work.' );
		}

		$this->assertNotEmpty( $this->configuration_service->get_key(), '`key` must be set.' );

		// Expect the dataset URI to be set to the existing value.
		$expected_dataset_uri = $this->configuration_service->get_dataset_uri();
		$this->configuration_service->set_dataset_uri( '' );

		$this->install->install();

		$this->assertEquals( $expected_dataset_uri, $this->configuration_service->get_dataset_uri(), '`dataset_uri` must match existing value.' );

	}

	/**
	 * Expect `must_install` to always return false.
	 *
	 * @since 3.20.0
	 */
	public function test_must_install() {

		$this->assertFalse( $this->install->must_install() );

	}

}
