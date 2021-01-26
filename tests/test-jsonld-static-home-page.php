<?php
/**
 * @since 3.27.9
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;

/**
 * Class Jsonld_Static_Home_Page_Test
 * @see https://github.com/insideout10/wordlift-plugin/issues/1033
 * @group jsonld
 */
class Jsonld_Static_Home_Page_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Jsonld_Service
	 */
	private $jsonld_service;

	public function setUp() {
		parent::setUp();
		global $wp_filter;
		$wp_filter = array();
		run_wordlift();
		$this->jsonld_service = Jsonld_Service::get_instance();
	}


	public function test_when_the_homepage_is_static_and_not_singular_should_not_have_mentions_property() {

		$home_page       = $this->factory()->post->create();
		$entity_1        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_2        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$GLOBALS['post'] = get_post( $home_page );
		// Link the home page with entities.
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_1 );
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_2 );

		// Emulate the collections page query.
		global $wp_query;
		$args     = array(
			'posts_per_page' => - 1,
			'post_type'      => 'post',
		);
		$wp_query = new WP_Query( $args );
		$jsonld   = $this->jsonld_service->get( Object_Type_Enum::HOMEPAGE, $home_page );
		$this->assertFalse( array_key_exists( 'mentions', $jsonld ), 'Should not have mentions property in the  jsonld' );

	}

	public function test_when_the_homepage_is_static_and_singular_should_have_mentions_property() {
		$home_page       = $this->factory()->post->create();
		$entity_1        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_2        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$GLOBALS['post'] = get_post( $home_page );
		// Link the home page with entities.
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_1 );
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_2 );

		// Emulate the collections page query.
		global $wp_query;
		$args     = array(
			'p' => $home_page
		);
		$wp_query = new WP_Query( $args );
		$jsonld   = $this->jsonld_service->get( Object_Type_Enum::HOMEPAGE, $home_page );
		$this->assertCount( 3, $jsonld, 'Referenced entities should be expanded in the result' );

		$jsonld = $jsonld[0];
		$this->assertTrue( array_key_exists( 'mentions', $jsonld ), 'Should have mentions property in the  jsonld' );
		$this->assertCount( 2, $jsonld['mentions'], 'Should have two referenced entities in the result' );
		$mentions = $jsonld['mentions'];

		$this->assertEquals( array( '@id' => Wordlift_Entity_Service::get_instance()->get_uri( $entity_1 ) ), $mentions[0],
			'Mentions not in correct format' );
	}



	public function test_when_the_homepage_is_static_and_singular_should_have_type_should_be_set_to_webpage() {
		$home_page       = $this->factory()->post->create();
		$entity_1        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_2        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$GLOBALS['post'] = get_post( $home_page );
		// Link the home page with entities.
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_1 );
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_2 );

		// Emulate the collections page query.
		global $wp_query;
		$args     = array(
			'p' => $home_page
		);
		$wp_query = new WP_Query( $args );
		$jsonld   = $this->jsonld_service->get( Object_Type_Enum::HOMEPAGE, $home_page );
		$this->assertCount( 3, $jsonld, 'Referenced entities should be expanded in the result' );

		$jsonld = $jsonld[0];
		$this->assertEquals($jsonld['@type'], 'WebPage');
		$this->assertTrue( array_key_exists( 'mentions', $jsonld ), 'Should have mentions property in the  jsonld' );
		$this->assertCount( 2, $jsonld['mentions'], 'Should have two referenced entities in the result' );
		$mentions = $jsonld['mentions'];

		$this->assertEquals( array( '@id' => Wordlift_Entity_Service::get_instance()->get_uri( $entity_1 ) ), $mentions[0],
			'Mentions not in correct format' );
	}


	public function test_when_the_homepage_is_static_and_singular_should_not_have_mentions_property_if_post_is_entity() {
		$this->create_home_page_entity( $home_page, $wp_query );
		$jsonld   = $this->jsonld_service->get( Object_Type_Enum::HOMEPAGE, $home_page );
		$this->assertFalse( array_key_exists( 'mentions', $jsonld ), 'Should have mentions property in the  jsonld' );
	}


	public function test_when_the_homepage_is_static_and_singular_should_have_mainEntityOfPage_property_if_post_is_entity() {
		$this->create_home_page_entity( $home_page, $wp_query );
		$jsonld   = $this->jsonld_service->get( Object_Type_Enum::HOMEPAGE, $home_page );
		$this->assertTrue( array_key_exists( 'mainEntity', $jsonld ), 'Should have mainEntity property in the  jsonld' );
		$this->assertTrue(is_array($jsonld['mainEntity']));
		$this->assertArrayHasKey('@id', $jsonld['mainEntity'], '@id should be present for entity');
		$this->assertEquals( Wordlift_Entity_Service::get_instance()->get_uri($home_page), $jsonld['mainEntity']['@id'] );
	}

	/**
	 * @param $home_page
	 * @param $wp_query
	 */
	private function create_home_page_entity( &$home_page, &$wp_query ) {
		$home_page       = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_1        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_2        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$GLOBALS['post'] = get_post( $home_page );
		// Link the home page with entities.
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_1 );
		wl_core_add_relation_instance( $home_page, WL_WHAT_RELATION, $entity_2 );

		// Emulate the collections page query.
		global $wp_query;
		$args     = array(
			'p' => $home_page
		);
		$wp_query = new WP_Query( $args );
	}


}