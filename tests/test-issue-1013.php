<?php

use Wordlift\Autocomplete\Linked_Data_Autocomplete_Service;
use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Content_Service;
use Wordlift\Entity\Entity_Helper;

/**
 * Test issue #1013.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.1
 * @package Wordlift\Tests
 * @group issue
 */
class Wordlift_Issue_1013_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Linked_Data_Autocomplete_Service
	 */
	private $linked_data_autocomplete_service;

	function setUp() {
		parent::setUp();

		$this->linked_data_autocomplete_service = new Linked_Data_Autocomplete_Service(
			Entity_Helper::get_instance(),
			Wordlift_Entity_Uri_Service::get_instance(),
			Wordlift_Entity_Service::get_instance() );

	}

	function test_linked_data_autocomplete_without_local_replacements() {

		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );
		$results = $this->linked_data_autocomplete_service->query( 'Acme' );
		remove_filter( 'pre_http_request', array( $this, 'pre_http_request' ) );

		$this->assertCount( 9, $results, 'We expect 9 results.' );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q296960', $results[0]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q341970', $results[1]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q1510236', $results[2]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q3604581', $results[3]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q4674380', $results[4]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q7866966', $results[5]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q14691556', $results[6]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q15753194', $results[7]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q28941601', $results[8]['id'] );

	}

	function test_linked_data_autocomplete_with_local_replacements() {

		$post_ids = $this->factory()->post->create_many( 2, array(
			'post_type' => Wordlift_Entity_Service::TYPE_NAME
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Test Issue 1013 %s' ),
		) );
		add_post_meta( $post_ids[0], Wordlift_Schema_Service::FIELD_SAME_AS, 'http://www.wikidata.org/entity/Q296960' );
		add_post_meta( $post_ids[1], Wordlift_Schema_Service::FIELD_SAME_AS, 'http://dbpedia.org/resource/Acme_(text_editor)' );

		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );
		$results = $this->linked_data_autocomplete_service->query( 'Acme' );
		remove_filter( 'pre_http_request', array( $this, 'pre_http_request' ) );

		$content_service = Wordpress_Content_Service::get_instance();
		$entity_uri_0    = $content_service->get_entity_id( Wordpress_Content_Id::create_post( $post_ids[0] ) );
		$entity_uri_1    = $content_service->get_entity_id( Wordpress_Content_Id::create_post( $post_ids[1] ) );

		$this->assertCount( 9, $results, 'We expect 9 results.' );
		$this->assertEquals( $entity_uri_0, $results[0]['id'], 'This result has to be replaced with the local entity.' );
		$this->assertEquals( $entity_uri_1, $results[1]['id'], 'This result has to be replaced with the local entity.' );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q1510236', $results[2]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q3604581', $results[3]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q4674380', $results[4]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q7866966', $results[5]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q14691556', $results[6]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q15753194', $results[7]['id'] );
		$this->assertEquals( 'http://www.wikidata.org/entity/Q28941601', $results[8]['id'] );

	}

	/**
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array $r HTTP request arguments.
	 * @param string $url The request URL.
	 */
	function pre_http_request( $preempt, $r, $url ) {

		return array(
			'response' => array( 'code' => 200, ),
			'body'     => file_get_contents( dirname( __FILE__ ) . '/assets/issue_1013_response.json' )
		);
	}

}