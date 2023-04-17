<?php
/**
 * Test the {@link Jsonld_Article_Wrapper}.
 *
 * The {@link Jsonld_Article_Wrapper} wraps entities inside an Article markup.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1241
 */

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Jsonld\Jsonld_Article_Wrapper;
use Wordlift\Jsonld\Jsonld_Context_Enum;

/**
 * Test the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @since   3.28.0
 * @package Wordlift
 * @group jsonld
 */
class Wordlift_Jsonld_Article_Wrapper extends Wordlift_Unit_Test_Case {

	/**
	 * @var Jsonld_Article_Wrapper
	 */
	private $jsonld_article_wrapper;

	private $post_to_jsonld_converter;
	private $jsonld_service;
	/**
	 * @var Wordlift_Cached_Post_Converter
	 */
	private $cached_postid_to_jsonld_converter;
	/**
	 * @var Wordlift_Postid_To_Jsonld_Converter
	 */
	private $postid_to_jsonld_converter;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// Disable sending SPARQL queries, since we don't need it.
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		new Wordlift_Test();

		$this->post_to_jsonld_converter = $this->getMockBuilder( 'Wordlift_Post_To_Jsonld_Converter' )
											   ->disableOriginalConstructor()
											->setMethods(
												array(
													'convert',
													'new_instance_with_filters_disabled',
												)
											)
											   ->getMock();

		$this->post_to_jsonld_converter->method( 'new_instance_with_filters_disabled' )
									   ->willReturn( $this->post_to_jsonld_converter );

		$this->jsonld_article_wrapper = new Jsonld_Article_Wrapper(
			$this->post_to_jsonld_converter,
			null
		);

		$property_getter          = Wordlift_Property_Getter_Factory::create();
		$post_to_jsonld_converter = new Wordlift_Post_To_Jsonld_Converter(
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_User_Service::get_instance(),
			Wordlift_Attachment_Service::get_instance()
		);

		$entity_post_to_jsonld_converter = new Wordlift_Entity_Post_To_Jsonld_Converter(
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_User_Service::get_instance(),
			Wordlift_Attachment_Service::get_instance(),
			$property_getter,
			Wordlift_Schemaorg_Property_Service::get_instance(),
			$post_to_jsonld_converter
		);

		$this->postid_to_jsonld_converter = new Wordlift_Postid_To_Jsonld_Converter(
			$entity_post_to_jsonld_converter,
			$post_to_jsonld_converter
		);

		$jsonld_cache                            = new Ttl_Cache( 'jsonld', 86400 );
		$this->cached_postid_to_jsonld_converter = new Wordlift_Cached_Post_Converter(
			$this->postid_to_jsonld_converter,
			$jsonld_cache
		);
	}

	public function test_exit_early_when_context_isnt_page() {

		$jsonld = array( 'lorem' );

		$this->assertEquals(
			$jsonld,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld, 1, Jsonld_Context_Enum::FAQ )
		);
		$this->assertEquals(
			$jsonld,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld, 1, Jsonld_Context_Enum::REST )
		);
		$this->assertEquals(
			$jsonld,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld, 1, Jsonld_Context_Enum::CAROUSEL )
		);
		$this->assertEquals(
			$jsonld,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld, 1, Jsonld_Context_Enum::UNKNOWN )
		);
		$this->assertEquals(
			$jsonld,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld, 1, null )
		);
	}

	public function test_exit_early_if_not_an_array() {

		$this->assertEquals(
			123,
			$this->jsonld_article_wrapper->after_get_jsonld( 123, 1, Jsonld_Context_Enum::PAGE )
		);

	}

	public function test_exit_early_if_array_empty() {

		$jsonld = array();
		$this->assertEquals(
			$jsonld,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld, 1, Jsonld_Context_Enum::PAGE )
		);

	}

	public function test_exit_early_if_jsonld_doesnt_satisfy_requirements() {

		$jsonld_1 = array( array( 'aProperty' => 'aValue' ) );
		$this->assertEquals(
			$jsonld_1,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld_1, 1, Jsonld_Context_Enum::PAGE )
		);

		$jsonld_2 = array( array( '@id' => 'aValue' ) );
		$this->assertEquals(
			$jsonld_2,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld_2, 1, Jsonld_Context_Enum::PAGE )
		);

	}

	public function test_exit_early_if_article() {

		$jsonld_1 = array(
			array(
				'@id'   => 'aValue',
				'@type' => 'AnalysisNewsArticle',
			),
		);
		$this->assertEquals(
			$jsonld_1,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld_1, 1, Jsonld_Context_Enum::PAGE )
		);

		$jsonld_2 = array(
			array(
				'@id'   => 'aValue',
				'@type' => array(
					'Backgro)undNewsArticle',
					'OpinionNewsArticle',
				),
			),
		);
		$this->assertEquals(
			$jsonld_2,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld_2, 1, Jsonld_Context_Enum::PAGE )
		);

		$jsonld_3 = array(
			array(
				'@id'   => 'aValue',
				'@type' => 'Thing',
			),
		);
		$this->assertNotEquals(
			$jsonld_3,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld_3, 1, Jsonld_Context_Enum::PAGE )
		);

	}

	public function test_thing_wrapped_in_article() {

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$thing_jsonld = json_decode( file_get_contents( __DIR__ . '/assets/test_jsonld_article_wrapper_1.json' ), true );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$article_jsonld = json_decode( file_get_contents( __DIR__ . '/assets/test_jsonld_article_wrapper_2.json' ), true );

		$this->post_to_jsonld_converter->method( 'convert' )
									   ->with( $this->equalTo( 123 ) )
									   ->willReturn( $article_jsonld );
		$this->post_to_jsonld_converter->expects( $this->once() )
									   ->method( 'convert' )
									   ->with( $this->equalTo( 123 ) );

		$jsonld = $this->jsonld_article_wrapper->after_get_jsonld( array( $thing_jsonld ), 123, Jsonld_Context_Enum::PAGE );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertCount( 2, $jsonld );

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
		$this->assertArrayHasKey( '@context', $jsonld[0], var_export( $jsonld[0], true ) );
		$this->assertEquals( 'http://schema.org', $jsonld[0]['@context'] );

		$this->assertArrayHasKey( '@type', $jsonld[0] );
		$this->assertEquals( 'Article', $jsonld[0]['@type'] );

		$this->assertArrayHasKey( 'headline', $jsonld[0] );
		$this->assertEquals( 'Search engine results page (SERP)', $jsonld[0]['headline'] );

		$this->assertArrayHasKey( 'url', $jsonld[0] );

		$this->assertArrayHasKey( 'description', $jsonld[0] );

		$this->assertArrayHasKey( 'image', $jsonld[0] );

		$this->assertArrayHasKey( 'mainEntityOfPage', $jsonld[0] );
		$this->assertArrayHasKey( 'url', $jsonld[0] );

		$this->assertArrayHasKey( 'author', $jsonld[0] );
		$this->assertArrayHasKey( '@type', $jsonld[0]['author'] );
		$this->assertEquals( 'Person', $jsonld[0]['author']['@type'] );
		$this->assertArrayHasKey( 'name', $jsonld[0]['author'] );
		$this->assertEquals( 'John Smith', $jsonld[0]['author']['name'] );
		$this->assertArrayHasKey( 'givenName', $jsonld[0]['author'] );
		$this->assertEquals( 'John', $jsonld[0]['author']['givenName'] );
		$this->assertArrayHasKey( 'familyName', $jsonld[0]['author'] );
		$this->assertEquals( 'Smith', $jsonld[0]['author']['familyName'] );

		$this->assertArrayHasKey( 'datePublished', $jsonld[0] );
		$this->assertEquals( '2020-01-02', $jsonld[0]['datePublished'] );
		$this->assertArrayHasKey( 'dateModified', $jsonld[0] );
		$this->assertEquals( '2020-01-03', $jsonld[0]['dateModified'] );

		$this->assertArrayHasKey( 'publisher', $jsonld[0] );
		$this->assertArrayHasKey( '@type', $jsonld[0]['publisher'] );
		$this->assertEquals( 'Organization', $jsonld[0]['publisher']['@type'] );
		$this->assertArrayHasKey( 'name', $jsonld[0]['publisher'] );
		$this->assertEquals( 'WordLift', $jsonld[0]['publisher']['name'] );
		$this->assertArrayHasKey( 'logo', $jsonld[0]['publisher'] );

		$this->assertArrayHasKey( 'about', $jsonld[0] );
		$this->assertArrayHasKey( '@id', $jsonld[0]['about'] );
		$this->assertEquals( 'http://data.wordlift.io/wl0216/entity/search_engine_results_page_serp', $jsonld[0]['about']['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld[1] );
		$this->assertEquals( 'Thing', $jsonld[1]['@type'] );

	}

	public function test_article_as_is() {

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$article_jsonld = json_decode( file_get_contents( __DIR__ . '/assets/test_jsonld_article_wrapper_2.json' ), true );

		$this->assertEquals( array( $article_jsonld ), $this->jsonld_article_wrapper->after_get_jsonld( array( $article_jsonld ), 123, Jsonld_Context_Enum::PAGE ) );

	}

	public function test_when_author_reference_added_in_article_jsonld_should_be_expanded() {

		// Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$jsonld = $this->setup_env_for_linked_entity_test();

		// we need to get Article, Thing, and author entity.
		$this->assertCount( 3, $jsonld );

		$article_jsonld = $jsonld[0];
		$this->assertArrayHasKey( 'author', $article_jsonld );

		$author_id     = $article_jsonld['author']['@id'];
		$author_jsonld = $jsonld[2];

		$this->assertEquals( $author_id, $author_jsonld['@id'] );

	}

	public function test_when_author_reference_added_in_article_jsonld_with_entity_type_set_to_person_should_not_duplicate() {

		// Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$jsonld = $this->setup_env_for_linked_entity_test( 'http://schema.org/Person' );

		// we need to get Article, Thing, and author entity.
		$this->assertCount( 3, $jsonld );

		$article_jsonld = $jsonld[0];
		$this->assertArrayHasKey( 'author', $article_jsonld );

		$author_id     = $article_jsonld['author']['@id'];
		$author_jsonld = $jsonld[2];

		$this->assertEquals( $author_id, $author_jsonld['@id'] );

	}

	public function test_article_wrapper_get() {

		$this->markTestSkipped( "`filter_input` can't be tested." );

		$jsonld = array(
			array(
				'@type' => 'Thing',
				'name'  => 'name',
			),
		);

		$_GET['article_wrapper'] = 'true';
		$this->assertEquals(
			$jsonld,
			$this->jsonld_article_wrapper->after_get_jsonld( $jsonld, 1, null )
		);
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		unset( $_GET['article_wrapper'] );

	}

	/**
	 * @return array
	 */
	private function setup_env_for_linked_entity_test( $linked_entity_type = 'http://schema.org/Thing' ) {

		$this->get_wordlift_test();
		$jsonld_wrapper = new Jsonld_Article_Wrapper( Wordlift_Post_To_Jsonld_Converter::get_instance(), $this->cached_postid_to_jsonld_converter );

		// create a user, link it to an entity
		$current_user_id = $this->factory()->user->create(
			array(
				'role' => 'editor',
			)
		);
		wp_set_current_user( $current_user_id );

		$author_entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $author_entity, $linked_entity_type, true );

		// Link the author to entity.
		$user_service = Wordlift_User_Service::get_instance();
		$user_service->set_entity( $current_user_id, $author_entity );

		$post_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		$mock_jsonld = array(
			array(
				'@id'   => 'https://foo.com/#bar',
				'@type' => 'Thing',
			),
		);

		$jsonld = $jsonld_wrapper->after_get_jsonld( $mock_jsonld, $post_id, Jsonld_Context_Enum::PAGE );

		return $jsonld;
	}

}
