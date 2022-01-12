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
 * @group admin
 */
class Wordlift_Admin_Settings_Page_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test without a publisher in `$_POST`.
	 *
	 * @since 3.11.0
	 */
	public function test_no_publisher_in_post() {

		// Create a random id.
		$publisher_id = 123;

		$entity_service_mock = $this->getMockBuilder( 'Wordlift_Entity_Service' )
		                            ->disableOriginalConstructor()
		                            ->setMethods( array( 'create' ) )
		                            ->getMock();
		$entity_service_mock->expects( $this->never() )->method( 'create' );

		$admin_settings_page = new Wordlift_Admin_Settings_Page(
			$entity_service_mock,
			$this->getMockBuilder( 'Wordlift_Admin_Input_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Language_Select_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Country_Select_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Publisher_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Radio_Input_Element' )->disableOriginalConstructor()->getMock()
		);

		// Call `sanitize_callback`.
		$input = $admin_settings_page->sanitize_callback( array( 'publisher_id' => $publisher_id ) );

		// Check that the value is returned.
		$this->assertCount( 1, $input );
		$this->assertEquals( $publisher_id, $input['publisher_id'] );
		$this->assertEquals( 123, $publisher_id );

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

		$entity_service_mock = $this->getMockBuilder( 'Wordlift_Entity_Service' )
		                            ->disableOriginalConstructor()
		                            ->setMethods( array( 'create' ) )
		                            ->getMock();
		$entity_service_mock->expects( $this->once() )
		                    ->method( 'create' )
		                    ->with( $this->equalTo( 'John Smith' ), $this->equalTo( 'http://schema.org/Person' ), $this->equalTo( '' ), $this->equalTo( 'publish' ) )
		                    ->willReturn( 123 );

		$admin_settings_page = new Wordlift_Admin_Settings_Page(
			$entity_service_mock,
			$this->getMockBuilder( 'Wordlift_Admin_Input_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Language_Select_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Country_Select_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Publisher_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Radio_Input_Element' )->disableOriginalConstructor()->getMock()
		);

		$input = $admin_settings_page->sanitize_callback( array( 'publisher_id' => null ) );

		// Check that we have only one setting.
		$this->assertCount( 1, $input );

		// Get the newly created publisher id.
		$publisher_id = $input['publisher_id'];

		// Check that it's numeric.
		$this->assertTrue( is_numeric( $publisher_id ) );
		$this->assertEquals( 123, $publisher_id );

	}

	/**
	 * Test with an organization publisher in `$_POST`.
	 *
	 * @since 3.11.0
	 */
	public function test_organization_publisher_in_post() {

		// Simulate posting Organization data.
		$_POST['wl_publisher'] = array(
			'name'         => 'Acme Inc',
			'type'         => 'organization',
			'thumbnail_id' => 456,
		);

		$entity_service_mock = $this->getMockBuilder( 'Wordlift_Entity_Service' )
		                            ->disableOriginalConstructor()
		                            ->setMethods( array( 'create' ) )
		                            ->getMock();
		$entity_service_mock->expects( $this->once() )
		                    ->method( 'create' )
		                    ->with( $this->equalTo( 'Acme Inc' ), $this->equalTo( 'http://schema.org/Organization' ), $this->equalTo( 456 ), $this->equalTo( 'publish' ) )
		                    ->willReturn( 123 );

		$admin_settings_page = new Wordlift_Admin_Settings_Page(
			$entity_service_mock,
			$this->getMockBuilder( 'Wordlift_Admin_Input_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Language_Select_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Country_Select_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Publisher_Element' )->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder( 'Wordlift_Admin_Radio_Input_Element' )->disableOriginalConstructor()->getMock()
		);

		// Call the sanitize callback.
		$input = $admin_settings_page->sanitize_callback( array( 'publisher_id' => null ) );

		// Check that we only have on value.
		$this->assertCount( 1, $input );

		// Get the value.
		$publisher_id = $input['publisher_id'];

		// Check that the value is numeric.
		$this->assertTrue( is_numeric( $publisher_id ) );
		$this->assertEquals( 123, $publisher_id );

	}

}
