<?php
/**
 * Tests: Entity Post to JSON-LD Converter Test.
 *
 * This file defines tests for the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1241
 * @since   3.8.
 * @package Wordlift
 */

use Wordlift\Jsonld\Jsonld_Article_Wrapper;

/**
 * Test the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @since   3.28.0
 * @package Wordlift
 * @group jsonld
 */
class Wordlift_Jsonld_Issue_1241 extends Wordlift_Unit_Test_Case {
	/**
	 * @var Jsonld_Article_Wrapper
	 */
	private $jsonld_article_wrapper;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// Disable sending SPARQL queries, since we don't need it.
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		$wordlift = new Wordlift_Test();

		$this->post_to_jsonld_converter = $wordlift->get_post_to_jsonld_converter();
		$this->jsonld_service           = $wordlift->get_jsonld_service();

//		$this->jsonld_article_wrapper = new Jsonld_Article_Wrapper(
//			$this->post_to_jsonld_converter,
//			$this->jsonld_service
//		);
	}

//	public function test_is_article() {
//
//		$this->assertTrue( $this->jsonld_article_wrapper->is_article( 'AnalysisNewsArticle' ) );
//		$this->assertTrue( $this->jsonld_article_wrapper->is_article( array(
//			'BackgroundNewsArticle',
//			'OpinionNewsArticle'
//		) ) );
//
//		$this->assertFalse( $this->jsonld_article_wrapper->is_article( 'Thing' ) );
//
//	}

	public function test() {

		$user_id = wp_insert_user( array(
			'user_login' => 'lorem_ipsum',
			'user_pass'  => 'tmppass',
			'first_name' => 'Lorem',
			'last_name'  => 'Ipsum',
		) );

		$name      = 'Test Entity Name';
		$entity_id = $this->factory->post->create( array(
			'post_title'  => $name,
			'post_type'   => 'entity',
			'post_author' => $user_id // Link the user with post
		) );

		// Attach the thumbnail image to the post
		$attachment_id = $this->factory->attachment->create_upload_object( __DIR__ . '/assets/cat-1200x1200.jpg', $entity_id );
		set_post_thumbnail( $entity_id, $attachment_id );

		$mocked_data = $this->post_to_jsonld_converter->convert( $entity_id );
		//var_dump($mocked_data);

		$this->entity_type_service->set( $entity_id, 'http://schema.org/Thing' );

		$jsonld = $this->jsonld_service->get_jsonld( false, $entity_id );

		print_r( $jsonld );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertCount( 2, $jsonld );

		$this->assertArrayHasKey( '@context', $jsonld[0] );
		$this->assertEquals( 'http://schema.org', $jsonld[0]['@context'] );

		$this->assertArrayHasKey( '@type', $jsonld[0] );
		$this->assertEquals( 'Article', $jsonld[0]['@type'] );

		$this->assertArrayHasKey( 'headline', $jsonld[0] );
		$this->assertEquals( $name, $jsonld[0]['headline'] );

		$this->assertArrayHasKey( 'url', $jsonld[0] );

		$this->assertArrayHasKey( 'description', $jsonld[0] );

		$this->assertArrayHasKey( 'image', $jsonld[0] );

		$this->assertArrayHasKey( 'mainEntityOfPage', $jsonld[0] );
		$this->assertArrayHasKey( 'url', $jsonld[0] );

		$this->assertArrayHasKey( 'author', $jsonld[0] );
		$this->assertArrayHasKey( '@type', $jsonld[0]['author'] );
		$this->assertEquals( $mocked_data['author']['@type'], $jsonld[0]['author']['@type'] );
		$this->assertArrayHasKey( 'name', $jsonld[0]['author'] );
		$this->assertEquals( $mocked_data['author']['name'], $jsonld[0]['author']['name'] );
		$this->assertArrayHasKey( 'givenName', $jsonld[0]['author'] );
		$this->assertEquals( $mocked_data['author']['givenName'], $jsonld[0]['author']['givenName'] );
		$this->assertArrayHasKey( 'familyName', $jsonld[0]['author'] );
		$this->assertEquals( $mocked_data['author']['familyName'], $jsonld[0]['author']['familyName'] );

		$this->assertArrayHasKey( 'datePublished', $jsonld[0] );
		$this->assertEquals( $mocked_data['datePublished'], $jsonld[0]['datePublished'] );
		$this->assertArrayHasKey( 'dateModified', $jsonld[0] );
		$this->assertEquals( $mocked_data['dateModified'], $jsonld[0]['dateModified'] );

		$this->assertArrayHasKey( 'publisher', $jsonld[0] );
		$this->assertArrayHasKey( '@type', $jsonld[0]['publisher'] );
		$this->assertEquals( $mocked_data['publisher']['@type'], $jsonld[0]['publisher']['@type'] );
		$this->assertArrayHasKey( 'name', $jsonld[0]['publisher'] );
		$this->assertEquals( $mocked_data['publisher']['name'], $jsonld[0]['publisher']['name'] );
		$this->assertArrayHasKey( 'logo', $jsonld[0]['publisher'] );

		$this->assertArrayHasKey( 'about', $jsonld[0] );
		$this->assertArrayHasKey( '@id', $jsonld[0]['about'] );
		//$this->assertEquals($entity_id, $jsonld[0]['about']['@id']);

		$this->assertArrayHasKey( '@type', $jsonld[1] );
		$this->assertEquals( 'Thing', $jsonld[1]['@type'] );
	}

}
