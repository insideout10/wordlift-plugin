<?php
/**
 * Tests: Include Exclude Module Test.
 *
 * @package Wordlift
 * @subpackage Wordlift/tests
 */
include dirname( __FILE__ ) . '/../src/modules/include-exclude/includes/Plugin_Enabled.php';

/**
 * Define the Wordlift_Include_Exclude_Test class.
 *
 * @group module
 */
class Wordlift_Include_Exclude_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Plugin_Enabled} instance to test.
	 *
	 * @var \WordLift\Modules\Include_Exclude\Plugin_Enabled $plugin_enabled The {@link Plugin_Enabled} instance.
	 */
	private $include_exclude_enabled;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->include_exclude_enabled = new WordLift\Modules\Include_Exclude\Plugin_Enabled();
	}

	public function test_include_exclude_urls() {
		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'exclude',
				'urls'            => "https://example.org/hello-world \n http://example.org/ \n https://example.org/3",
			)
		);

		$this->assertFalse( $this->include_exclude_enabled->wl_is_enabled( true ) );
	}
}
