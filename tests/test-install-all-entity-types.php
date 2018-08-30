<?php
/**
 * Tests: Install All Entity Types Test.
 *
 * Test the {@link Wordlift_Install_All_Entity_Types} class.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Install_All_Entity_Types_Test class.
 *
 * @since 3.20.0
 */
class Wordlift_Install_All_Entity_Types_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Install_All_Entity_Types} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Install_All_Entity_Types $install The {@link Wordlift_Install_All_Entity_Types} instance.
	 */
	private $install;

	/**
	 * The existing option value, used to restore the option after the test is run.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var string $option_value The existing option value.
	 */
	private $option_value;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		$this->install      = Wordlift_Install_All_Entity_Types::get_instance();
		$this->option_value = get_option( Wordlift_Install_All_Entity_Types::OPTION_NAME );
	}

	/**
	 * {@inheritdoc}
	 */
	function tearDown() {

		// Reset the option to its original value.
		update_option( Wordlift_Install_All_Entity_Types::OPTION_NAME, $this->option_value );

		parent::tearDown();
	}

	/**
	 * Test the `install` function.
	 *
	 * @since 3.20.0
	 */
	public function test_install() {

		delete_option( Wordlift_Install_All_Entity_Types::OPTION_NAME );

		$this->install->install();

		$value_1 = get_option( Wordlift_Install_All_Entity_Types::OPTION_NAME );

		$this->assertEquals( '1.0.0', $value_1, 'Expect the value to be `1.0.0` after install.' );

	}

	/**
	 * Test the `must_install` function.
	 *
	 * @since 3.20.0
	 */
	public function test_must_install() {

		delete_option( Wordlift_Install_All_Entity_Types::OPTION_NAME );

		$this->assertTrue( $this->install->must_install(), 'Must be `true`.' );

		update_option( Wordlift_Install_All_Entity_Types::OPTION_NAME, '0.0.0' );

		$this->assertTrue( $this->install->must_install(), 'Must be `true`.' );

		update_option( Wordlift_Install_All_Entity_Types::OPTION_NAME, '1.0.0' );

		$this->assertFalse( $this->install->must_install(), 'Must be `false`.' );

		update_option( Wordlift_Install_All_Entity_Types::OPTION_NAME, '2.0.0' );

		$this->assertFalse( $this->install->must_install(), 'Must be `false`.' );

	}

}
