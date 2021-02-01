<?php

use Wordlift\Jsonld\Jsonld_User_Service;
use Wordlift\Object_Type_Enum;

/**
 * @group jsonld
 */
class Test_Jsonld_User_Service extends Wordlift_Unit_Test_Case {

	/**
	 * @var Jsonld_User_Service|WP_UnitTest_Factory|null
	 */
	private $jsonld_user_service;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|WP_UnitTest_Factory|Wordlift_User_Service|null
	 */

	private $user_service_mock;
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|WP_UnitTest_Factory|Wordlift\Jsonld\Jsonld_Service|null
	 */
	private $jsonld_service_mock;

	function setUp() {
		parent::setUp();

		$this->user_service_mock   = $this->getMockBuilder( 'Wordlift_User_Service' )
		                                  ->disableOriginalConstructor()
		                                  ->getMock();
		$this->jsonld_service_mock = $this->getMockBuilder( 'Wordlift\Jsonld\Jsonld_Service' )
		                                  ->disableOriginalConstructor()
		                                  ->getMock();
		$this->jsonld_user_service = new Jsonld_User_Service( $this->user_service_mock );
		$this->jsonld_user_service->set_jsonld_service( $this->jsonld_service_mock );
	}

	function test__not_found() {

		$retval = $this->jsonld_user_service->get( - 1 );
		$this->assertTrue( is_array( $retval ) );
		$this->assertEmpty( $retval );

	}

	function test__post() {

		$user_id = $this->factory()->user->create();

		$this->user_service_mock->method( 'get_entity' )
		                        ->with( $this->equalTo( $user_id ) )
		                        ->willReturn( 123 );
		$this->user_service_mock->expects( $this->once() )
		                        ->method( 'get_entity' )
		                        ->with( $this->equalTo( $user_id ) );

		$this->jsonld_service_mock->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( 123 ) )
		                          ->willReturn( array( 'key123' => 'value123' ) );
		$this->jsonld_service_mock->expects( $this->once() )
		                          ->method( 'get' )
		                          ->with(
			                          $this->equalTo( Object_Type_Enum::POST ),
			                          $this->equalTo( 123 ) );

		$this->assertEqualSets( array( 'key123' => 'value123' ), $this->jsonld_user_service->get( $user_id ) );

	}

	function test__no_uri() {

		$user_id = $this->factory()->user->create();

		$this->user_service_mock->method( 'get_entity' )
		                        ->with( $this->equalTo( $user_id ) )
		                        ->willReturn( false );
		$this->user_service_mock->expects( $this->once() )
		                        ->method( 'get_entity' )
		                        ->with( $this->equalTo( $user_id ) );

		$this->user_service_mock->method( 'get_uri' )
		                        ->with( $this->equalTo( $user_id ) )
		                        ->willReturn( false );
		$this->user_service_mock->expects( $this->once() )
		                        ->method( 'get_uri' )
		                        ->with( $this->equalTo( $user_id ) );

		$retval = $this->jsonld_user_service->get( $user_id );
		$this->assertTrue( is_array( $retval ) );
		$this->assertEmpty( $retval );

	}

	function test__jsonld() {

		$user_id = $this->factory()->user->create( array(
			'display_name' => 'Display Name 123',
			'first_name'   => 'First Name 123',
			'last_name'    => 'Last Name 123',
			'url'          => 'https://localdomain.localhost',
		) );

		$this->user_service_mock->method( 'get_entity' )
		                        ->with( $this->equalTo( $user_id ) )
		                        ->willReturn( false );
		$this->user_service_mock->expects( $this->once() )
		                        ->method( 'get_entity' )
		                        ->with( $this->equalTo( $user_id ) );

		$this->user_service_mock->method( 'get_uri' )
		                        ->with( $this->equalTo( $user_id ) )
		                        ->willReturn( 'https://localdomain.localhost/dataset123/author/user123' );
		$this->user_service_mock->expects( $this->once() )
		                        ->method( 'get_uri' )
		                        ->with( $this->equalTo( $user_id ) );

		$retval = $this->jsonld_user_service->get( $user_id );
		$this->assertTrue( is_array( $retval ) );
		$this->assertEqualSets( array(
			array(
				"@id"        => "https://localdomain.localhost/dataset123/author/user123",
				"@type"      => "Person",
				"name"       => "Display Name 123",
				"givenName"  => "First Name 123",
				"familyName" => "Last Name 123",
				'@context'   => 'http://schema.org'
			)
		), $retval );

	}

}
