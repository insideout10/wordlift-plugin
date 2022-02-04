<?php
/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Entity\Remote_Entity\Invalid_Remote_Entity;
use Wordlift\Entity\Remote_Entity\Remote_Entity;
use Wordlift\Entity\Remote_Entity\Url_To_Remote_Entity_Converter;
use Wordlift\Entity\Remote_Entity\Valid_Remote_Entity;


/**
 * Class Url_To_Entity_Converter_Test
 * @group entity
 */
class Url_To_Entity_Converter_Test extends Wordlift_Unit_Test_Case {

	public function test_given_a_invalid_url_should_return_invalid_remote_entity() {
		$converter = new Url_To_Remote_Entity_Converter();
		$entity    = $converter->convert( 'http://www.wikidata.org/entity/invalid' );
		$this->assertTrue(
			$entity instanceof Invalid_Remote_Entity
		);
	}

	public function test_given_a_url_with_empty_response_should_return_invalid_remote_entity() {
		$converter = new Url_To_Remote_Entity_Converter();
		$entity    = $converter->convert( 'http://www.wikidata.org/entity/invalid_response' );
		$this->assertTrue(
			$entity instanceof Invalid_Remote_Entity
		);
	}

	public function test_for_valid_url_should_return_valid_remote_entity() {
		$converter = new Url_To_Remote_Entity_Converter();
		$entity    = $converter->convert( 'http://dbpedia.org/resource/WordLift' );
		$this->assertTrue(
			$entity instanceof Valid_Remote_Entity
		);

		$valid_remote_entity_data = self::get_valid_remote_entity_data();

		$this->assertEquals( $entity->get_types(), array( $valid_remote_entity_data['@type'] ) );
		$this->assertEquals( $entity->get_name(), $valid_remote_entity_data['name'] );
		$this->assertEquals( $entity->get_description(), $valid_remote_entity_data['description'] );
		$this->assertEquals( $entity->get_same_as(), array_merge( array( 'http://dbpedia.org/resource/WordLift' ),
			$valid_remote_entity_data['sameAs'] ) );
	}


	static function get_valid_remote_entity_data() {
		return array(
			'@type'       => 'Organization',
			'name'        => 'WordLift',
			'description' => 'WordLift is a start-up founded in 2017 and based in Rome, Italy. The company has developed the homonymous WordPress plugin which, through the use of semantic technologies and artificial intelligence, optimises the writing and organisation of content and the findability of websites. Wordlift supports 32 different languages and in 2017 has had over 200 clients, amongst which are SalzburgerLand Tourismus GmbH, Greenpeace, Legambiente and The American University in Cairo.',
			'@id'         => 'http://dbpedia.org/resource/WordLift',
			'@context'    => 'https://schema.org',
			'sameAs'      =>
				array(
					0 => 'http://de.dbpedia.org/resource/WordLift',
					1 => 'https://global.dbpedia.org/id/2xENs',
					2 => 'http://www.wikidata.org/entity/Q31998763',
				),
		);
	}

	static function get_url_to_responses_map() {

		$valid_entity_response   = array(
			'body'     => json_encode( self::get_valid_remote_entity_data() ),
			'response' => array( 'code' => 200 )
		);
		$invalid_entity_response = array(
			'body'     => '',
			'response' => array( 'code' => 500 )
		);

		return array(
			'https://api.wordlift.io/id/http/dbpedia.org/resource/WordLift'            => $valid_entity_response,
			'https://api.wordlift.io/id/http/www.wikidata.org/entity/invalid'          => $invalid_entity_response,
			'https://api.wordlift.io/id/http/www.wikidata.org/entity/invalid_response' => array(
				'body'     => '',
				'response' => array( 'code' => 200 )
			)
		);
	}


	function setUp() {
		parent::setUp();
		add_filter( 'pre_http_request', array( $this, 'mock_api' ), 10, 3 );
	}

	function tearDown() {
		parent::tearDown();
		remove_filter( 'pre_http_request', array( $this, 'mock_api' ) );
	}

	public function mock_api( $response, $request, $url ) {

		$method = $request['method'];

		if ( $method !== 'GET' ) {
			return $response;
		}

		$map = self::get_url_to_responses_map();

		if ( array_key_exists( $url, $map ) ) {
			return $map[ $url ];
		}

		return $response;
	}


}
