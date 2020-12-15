<?php
/**
 * Tests: Admin Person Element Test.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Person_Element_Test} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @group admin
 */
class Wordlift_Admin_Person_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Person_Element} to test.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Admin_Author_Element $admin_person_element The {@link Wordlift_Admin_Person_Element} to test.
	 */
	private $admin_person_element;

	/**
	 * A mocked {@link Wordlift_Admin_Select2_Element} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Admin_Select2_Element $select2_element A mocked {@link Wordlift_Admin_Select2_Element} instance.
	 */
	private $select2_element;

	/**
	 * A mocked {@link Wordlift_Publisher_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Publisher_Service $publisher_service A mocked {@link Wordlift_Publisher_Service} instance.
	 */
	private $publisher_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->publisher_service = $this->getMockBuilder( 'Wordlift_Publisher_Service' )
										->disableOriginalConstructor()
										->setMethods( array( 'query' ) )
										->getMock();

		$this->select2_element = $this->getMockBuilder( 'Wordlift_Admin_Select2_Element' )
									  ->disableOriginalConstructor()
									  ->setMethods( array( 'render' ) )
									  ->getMock();

		$this->admin_person_element = new Wordlift_Admin_Author_Element( $this->publisher_service, $this->select2_element );

	}

	/**
	 * Test the `render` method.
	 *
	 * @since 3.14.0
	 */
	function test_render() {

		// Create an entity.
		$post_title     = 'Test Admin Author Element test_render';
		$entity_post_id = $this->entity_factory->create( array(
			'post_title' => $post_title,
		) );

		// Check that the publisher service gets called.
		$this->publisher_service->expects( $this->once() )
								->method( 'query' )
								->willReturn( array( 'First Element' ) );

		// Check that the select2 element gets called.
		$this->select2_element->expects( $this->once() )
							->method( 'render' )
							->with( $this->callback( function ( $value ) use ( $entity_post_id, $post_title ) {
								if ( isset( $value['data']['wl-select2-data'] ) ) {
									$data = json_decode( $value['data']['wl-select2-data'], true );
								}

								return
									isset( $value['value'] ) && $entity_post_id === $value['value'] &&
									isset( $value['options'] ) && is_array( $value['options'] ) &&
									isset( $value['options'][ $entity_post_id ] ) && $post_title === $value['options'][ $entity_post_id ] &&
									isset( $data ) && is_array( $data ) &&
									0 == $data[0]['id'] &&
									'First Element' === $data[1];
							} ) );


		// Call the render function with the entity post id.
		$result = $this->admin_person_element->render( array(
			'current_entity' => $entity_post_id,
		) );

		//
		$this->assertEquals( $this->admin_person_element, $result );

	}

}
