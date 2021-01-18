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

/**
 * Test the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @since   3.28.0
 * @package Wordlift
 * @group jsonld
 */
class Wordlift_Jsonld_Issue_1241 extends Wordlift_Unit_Test_Case {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// Disable sending SPARQL queries, since we don't need it.
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		$wordlift = new Wordlift_Test();

		$this->jsonld_service = $wordlift->get_jsonld_service();
	}

	public function mock_data( $post_id ) {
		$date_published = date( 'Y-m-d', 1576800000 );

		if ( ! add_post_meta( $post_id, 'wl_cal_date_published', $date_published ) ) {
			add_post_meta( $post_id, 'wl_cal_date_published', $date_published );
		}

		$date_modified = date( 'Y-m-d', 1576900000 );
		if ( add_post_meta( $post_id, 'wl_cal_date_modified', $date_modified ) ) {
			add_post_meta( $post_id, 'wl_cal_date_modified', $date_modified );
		}

		$image = array(
			"@type"  => "ImageObject",
			"url"    => "https://wordlift.io/blog/en/wp-content/uploads/sites/3/2017/02/wordlift-serp.png",
			"width"  => "640",
			"height" => "480"
		);

		$author = array(
			"@type"      => "Person",
			"name"       => "John Smith",
			"givenName"  => "John",
			"familyName" => "Smith"
		);

		$publisher = array(
			"@type" => "Organization",
			"name"  => "WordLift",
			"logo"  => array(
				"@type" => "ImageObject",
				"url"   => "https://wordlift.io/logo.png"
			)
		);


		return array(
			'datePublished' => $date_published,
			'dateModified'  => $date_modified,
			'image'         => $image,
			'author'        => $author,
			'publisher'     => $publisher
		);
	}

	public function test() {
		$name      = 'Test Entity Name';
		$entity_id = $this->factory->post->create( array(
			'post_title' => $name,
			'post_type'  => 'entity',
		) );

		$mocked_data = $this->mock_data( $entity_id );

		$this->entity_type_service->set( $entity_id, 'http://schema.org/Thing' );

		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10, 2 );

		$jsonld = $this->jsonld_service->get_jsonld( false, $entity_id );

		var_dump( $jsonld );

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
		$this->assertArrayHasKey( '@type', $jsonld[0]['image'] );
		$this->assertEquals( $mocked_data['image']['@type'], $jsonld[0]['image']['@type'] );
		$this->assertArrayHasKey( 'url', $jsonld[0]['image'] );
		$this->assertEquals( $mocked_data['image']['url'], $jsonld[0]['image']['url'] );
		$this->assertArrayHasKey( 'width', $jsonld[0]['image'] );
		$this->assertEquals( $mocked_data['image']['width'], $jsonld[0]['image']['width'] );
		$this->assertArrayHasKey( 'height', $jsonld[0]['image'] );
		$this->assertEquals( $mocked_data['image']['height'], $jsonld[0]['image']['height'] );

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
		$this->assertArrayHasKey( '@type', $jsonld[0] )['publisher'];
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

	public function wl_after_get_jsonld( $jsonld, $post_id ) {
		if ( ! is_array( $jsonld ) || count( $jsonld ) === 0 ) {
			return $jsonld;
		}

		// Copy the 1st array element
		$post_jsonld    = $jsonld[0];
		$post_jsonld_id = array_key_exists( '@id', $post_jsonld ) ? $post_jsonld['@id'] : false;

		if ( ! $post_jsonld_id ) {
			return $jsonld;
		}

		$mocked_data = $this->mock_data( $post_id );

		foreach ( $post_jsonld as $key => $value ) {
			if ( $key === '@id' ) {
				$post_jsonld[ $key ] = $post_jsonld_id . '#article';
			}

			if ( $key === '@type' ) {
				$post_jsonld[ $key ]          = 'Article';
				$post_jsonld['headline']      = $post_jsonld['name'];
				$post_jsonld['datePublished'] = $mocked_data['datePublished'];
				$post_jsonld['dateModified']  = $mocked_data['dateModified'];
				$post_jsonld['image']         = array(
					"@type"  => "ImageObject",
					"url"    => "https://wordlift.io/blog/en/wp-content/uploads/sites/3/2017/02/wordlift-serp.png",
					"width"  => "640",
					"height" => "480"
				);
				$post_jsonld['author']        = array(
					"@type"      => "Person",
					"name"       => "John Smith",
					"givenName"  => "John",
					"familyName" => "Smith"
				);
				$post_jsonld['publisher']     = array(
					"@type" => "Organization",
					"name"  => "WordLift",
					"logo"  => array(
						"@type" => "ImageObject",
						"url"   => "https://wordlift.io/logo.png"
					)
				);
				$post_jsonld['about']         = array( '@id' => $post_jsonld_id );
				unset( $post_jsonld['name'] );
			}
		}

		// Add back the post jsonld to first of array.
		array_unshift( $jsonld, $post_jsonld );

		return $jsonld;
	}


}
