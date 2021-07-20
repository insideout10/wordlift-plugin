<?php

use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Jsonld\Jsonld_User_Service;
use Wordlift\Object_Type_Enum;

/**
 * Test issue #1072.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.26.0
 * @package Wordlift\Tests
 * @group issue
 */
class Wordlift_Issue_1072_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Jsonld_Service
	 */
	private $jsonld_service;

	/**
	 * @var Wordlift_Jsonld_Service
	 */
	private $legacy_jsonld_service;

	/**
	 * @var Wordlift_Term_JsonLd_Adapter
	 */
	private $term_jsonld_adapter;

	function setUp() {
		parent::setUp();

		$wordlift = Wordlift::get_instance();
		$this->assertNotNull( $wordlift, '`Wordlift` must exist.' );

		$this->legacy_jsonld_service = Wordlift_Jsonld_Service::get_instance();
		$this->assertNotNull( $this->legacy_jsonld_service, '`Wordlift_Jsonld_Service` must exist.' );

		$this->term_jsonld_adapter = Wordlift_Term_JsonLd_Adapter::get_instance();
		$this->assertNotNull( $this->term_jsonld_adapter, '`Wordlift_Term_JsonLd_Adapter` must exist.' );

		$this->jsonld_service = new Jsonld_Service( $this->legacy_jsonld_service, $this->term_jsonld_adapter, new Jsonld_User_Service( Wordlift_User_Service::get_instance() ) );
		$this->assertNotNull( $this->jsonld_service, '`Jsonld_Service` must exist.' );

	}

	function test_post() {

		$post_id       = $this->factory()->post->create();
		$post_jsonld_a = $this->legacy_jsonld_service->get_jsonld( false, $post_id );
		$post_jsonld_b = $this->jsonld_service->get( Object_Type_Enum::POST, $post_id );
		$this->assertEqualSets( $post_jsonld_a, $post_jsonld_b );

	}

	function test_homepage() {

		$post_jsonld_a = $this->legacy_jsonld_service->get_jsonld( true );
		$post_jsonld_b = $this->jsonld_service->get( Object_Type_Enum::HOMEPAGE );
		$this->assertEqualSets( $post_jsonld_a, $post_jsonld_b );

	}

	function test_term() {

		$term_id  = $this->factory()->term->create( array( 'taxonomy' => 'category' ) );
		$post_ids = $this->factory()->post->create_many( 100 );

		foreach ( $post_ids as $post_id ) {
			wp_add_object_terms( $post_id, $term_id, 'category' );
		}

		$term_jsonld_a = $this->term_jsonld_adapter->get( $term_id, Jsonld_Context_Enum::PAGE );
		$term_jsonld_b = $this->jsonld_service->get( Object_Type_Enum::TERM, $term_id );

		// Since 3.31.2 we dont push the itemlist element to KG, the below assertion would fail.
	//	$this->assertEqualSets( $term_jsonld_a, $term_jsonld_b );

		$this->assertArrayHasKey( 'itemListElement', $term_jsonld_a[0],
			'Key `itemListElement` must exist in array.' );
		$this->assertCount( 100, $term_jsonld_a[0]['itemListElement'],
			'Key `itemListElement` must have 100 elements.' );

	}

}
