<?php
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Entity\Entity_No_Index_Flag;
use Wordlift\Synonym\Rest_Field;

/**
 * Class Entity_Save_Test
 * @group entity
 */
class Entity_Synonym_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;

	public function setUp() {
		parent::setUp();
		$this->entity_service = Wordlift_Entity_Service::get_instance();
	}

	public function test_synonym_with_html_code_should_not_be_saved() {
		$post_id = $this->factory()->post->create();

		$this->entity_service->set_alternative_labels(
			$post_id,
			array( "<a href='google.com'>test</a>" )
		);
		$this->assertCount( 0, $this->entity_service->get_alternative_labels( $post_id ) );

	}

	public function test_synonym_with_html_code_should_not_be_saved_in_rest_endpoint() {
		$post_id = $this->factory()->post->create();

		$rest_field = new Rest_Field();

		$rest_field->update_value(
			array( "<a href='google.com'>test</a>" ),
			get_post( $post_id ),
			Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY
		);

		$this->assertCount( 0, $this->entity_service->get_alternative_labels( $post_id ) );

	}

	public function test_synonym_with_parenthisis_should_be_saved() {
		$post_id = $this->factory()->post->create();

		$this->entity_service->set_alternative_labels(
			$post_id,
			array( "synonym with (parenthisis)" )
		);
		$this->assertCount( 1, $this->entity_service->get_alternative_labels( $post_id ) );

	}

	public function test_empty_synonym_should_not_be_saved() {
		$post_id = $this->factory()->post->create();

		$this->entity_service->set_alternative_labels(
			$post_id,
			array( "" )
		);
		$this->assertCount( 0, $this->entity_service->get_alternative_labels( $post_id ) );
	}

	public function test_valid_synonym_should_be_saved() {
		$post_id = $this->factory()->post->create();

		$this->entity_service->set_alternative_labels(
			$post_id,
			array( "valid synonym" )
		);
		$this->assertCount( 1, $this->entity_service->get_alternative_labels( $post_id ) );
	}


}
