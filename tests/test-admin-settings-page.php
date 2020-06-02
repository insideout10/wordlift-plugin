<?php
/**
 * Tests: Settings Page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Settings_Page_Test} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Admin_Settings_Page_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Settings_Page} under testing.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Settings_Page $settings_page The {@link Wordlift_Admin_Settings_Page} instance.
	 */
	private $settings_page;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->settings_page = $this->get_wordlift_test()->get_settings_page();

	}

	/**
	 * Test without a publisher in `$_POST`.
	 *
	 * @since 3.11.0
	 */
	public function test_no_publisher_in_post() {

		// Create a random id.
		$publisher_id = rand( 1, 100 );

		// Call `sanitize_callback`.
		$input = $this->settings_page->sanitize_callback( array( 'publisher_id' => $publisher_id ) );

		// Check that the value is returned.
		$this->assertCount( 1, $input );
		$this->assertEquals( $publisher_id, $input['publisher_id'] );

	}

	/**
	 * Test with a person publisher in `$_POST`.
	 *
	 * @since 3.11.0
	 */
	public function test_person_publisher_in_post() {

		// The publisher data.
		$_POST['wl_publisher'] = array(
			'name'         => 'John Smith',
			'type'         => 'person',
			'thumbnail_id' => '',
		);

		// Call the `sanitize_callback`.
		$input = $this->settings_page->sanitize_callback( array( 'publisher_id' => null ) );

		// Check that we have only one setting.
		$this->assertCount( 1, $input );

		// Get the newly created publisher id.
		$publisher_id = $input['publisher_id'];

		// Check that it's numeric.
		$this->assertTrue( is_numeric( $publisher_id ) );

		// Check that there's the corresponding post.
		$post = get_post( $publisher_id );

		$this->assertNotNull( $post );

		// Check that data match.
		$this->assertEquals( 'John Smith', $post->post_title );
		$this->assertEquals( Wordlift_Entity_Service::TYPE_NAME, $post->post_type );

		// Check the schema.org type.
		$type = $this->entity_type_service->get( $publisher_id );
		$this->assertEquals( 'http://schema.org/Person', $type['uri'] );

		// Check that there's no thumbnail.
		global $wp_version;
		if ( version_compare( $wp_version, '5.4', '>=' ) ) {
			$this->assertEquals( 0, get_post_thumbnail_id( $publisher_id ) );
		} else {
			$this->assertEquals( '', get_post_thumbnail_id( $publisher_id ) );
		}

	}

	/**
	 * Test with an organization publisher in `$_POST`.
	 *
	 * @since 3.11.0
	 */
	public function test_organization_publisher_in_post() {

		// Create a mock attachment.
		$attachment_id = $this->factory->attachment->create_object( "image.jpg", 0, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment',
		) );

		// Simulate posting Organization data.
		$_POST['wl_publisher'] = array(
			'name'         => 'Acme Inc',
			'type'         => 'organization',
			'thumbnail_id' => $attachment_id,
		);

		// Call the sanitize callback.
		$input = $this->settings_page->sanitize_callback( array( 'publisher_id' => null ) );

		// Check that we only have on value.
		$this->assertCount( 1, $input );

		// Get the value.
		$publisher_id = $input['publisher_id'];

		// Check that the value is numeric.
		$this->assertTrue( is_numeric( $publisher_id ) );

		// Check that there's a corresponding post.
		$post = get_post( $publisher_id );

		$this->assertNotNull( $post );

		// Check that the data matches.
		$this->assertEquals( 'Acme Inc', $post->post_title );
		$this->assertEquals( Wordlift_Entity_Service::TYPE_NAME, $post->post_type );

		// Check that the schema.org type matches.
		$type = $this->entity_type_service->get( $publisher_id );
		$this->assertEquals( 'http://schema.org/Organization', $type['uri'] );

		// Check that the thumbnail id matches.
		$this->assertEquals( $attachment_id, get_post_thumbnail_id( $publisher_id ) );

	}

}
