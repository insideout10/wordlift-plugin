<?php
/**
 * Tests: Include Exclude Module Test.
 *
 * @package Wordlift
 * @subpackage Wordlift/tests
 */
// include dirname( __FILE__ ) . '/../src/modules/include-exclude/includes/Plugin_Enabled.php';

/**
 * Define the Wordlift_Include_Exclude_Test class.
 *
 * @group include-exclude
 */
class Wordlift_Include_Exclude_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Plugin_Enabled} instance to test.
	 *
	 * @var \Wordlift\Modules\Include_Exclude\Plugin_Enabled $plugin_enabled The {@link Plugin_Enabled} instance.
	 */
	private $include_exclude_enabled;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		if ( ! apply_filters( 'wl_feature__enable__include-exclude', false ) ) {
			$this->markTestSkipped( 'Include/Exclude is not enabled.' );
		}

		// Update Site & Home URLs.
		update_option( 'siteurl', 'https://wordlift.io' );
		update_option( 'home', 'https://wordlift.io' );

		$this->include_exclude_enabled = new Wordlift\Modules\Include_Exclude\Plugin_Enabled();
	}

	public function test_given_include_urls_should_return_true() {
		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'include',
				'urls'            => "https://wordlift.io/hello-world \n https://wordlift.io/ \n https://wordlift.io/3",
			)
		);
		$_SERVER['REQUEST_URI'] = '/hello-world';
		$this->assertTrue( $this->include_exclude_enabled->wl_is_enabled( true ) );
	}

	public function test_given_exclude_urls_should_return_false() {
		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'exclude',
				'urls'            => "https://wordlift.io/hello-world \n https://wordlift.io/ \n https://wordlift.io/3",
			)
		);
		$_SERVER['REQUEST_URI'] = '/hello-world';
		$this->assertFalse( $this->include_exclude_enabled->wl_is_enabled( true ) );
	}

	public function test_given_include_wrong_urls_should_return_false() {
		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'include',
				'urls'            => "https://wordlift.io/hello-world \n https://wordlift.io/ \n https://wordlift.io/3",
			)
		);
		$_SERVER['REQUEST_URI'] = '/hello-world-wrong';
		$this->assertFalse( $this->include_exclude_enabled->wl_is_enabled( true ) );
	}

	public function test_given_exclude_wrong_urls_should_return_true() {
		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'exclude',
				'urls'            => "https://wordlift.io/hello-world \n https://wordlift.io/ \n https://wordlift.io/3",
			)
		);
		$_SERVER['REQUEST_URI'] = '/hello-world-wrong';
		$this->assertTrue( $this->include_exclude_enabled->wl_is_enabled( true ) );
	}

	public function test_given_exclude_without_urls_should_return_true() {
		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'exclude',
				'urls'            => "",
			)
		);
		$_SERVER['REQUEST_URI'] = '/hello-world';
		$this->assertTrue( $this->include_exclude_enabled->wl_is_enabled( true ) );
	}

	public function test_given_include_without_urls_should_return_false() {
		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'include_exclude' => 'include',
				'urls'            => "",
			)
		);
		$_SERVER['REQUEST_URI'] = '/hello-world';
		$this->assertFalse( $this->include_exclude_enabled->wl_is_enabled( true ) );
	}
}
