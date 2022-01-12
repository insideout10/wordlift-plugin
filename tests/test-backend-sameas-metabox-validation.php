<?php

use Wordlift\Metabox\Field\Wl_Metabox_Field_sameas;
use Wordlift\Object_Type_Enum;

/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group backend
 */
class Sameas_Metabox_Validation_Test extends Wordlift_Unit_Test_Case {


	/**
	 * @var array[][]
	 */
	private $config;


	public function setUp() {
		parent::setUp();
		$this->config = array(
			'sameas' =>
				array(
					'entity_same_as' =>
						array(
							'predicate'   => 'http://schema.org/sameAs',
							'type'        => 'uri',
							'export_type' => 'http://schema.org/Thing',
							'constraints' =>
								array(
									'cardinality' => INF,
								),
							'input_field' => 'sameas',
						),
				),
		);


	}

	public function test_given_text_should_not_save() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post_id          = $this->factory()->post->create();
		$instance         = new Wl_Metabox_Field_sameas( $this->config, $post_id, Object_Type_Enum::POST );
		$_POST['post_ID'] = $post_id;
		$test_data        = array(
			0 => 'aaaa',
			1 => '',
			2 => 'dsadasda',
			3 => 'https://google.com',
			4 => 'https://test.com'
		);
		$instance->save_data( $test_data );
		// now it should save only two rows
		$rows = get_post_meta( $post_id, 'entity_same_as' );
		$this->assertCount( 2, $rows );
	}


	public function test_given_local_dataset_url_should_not_save() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post_id          = $this->factory()->post->create();
		$instance         = new Wl_Metabox_Field_sameas( $this->config, $post_id, Object_Type_Enum::POST );
		$_POST['post_ID'] = $post_id;
		$dataset_uri      = Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
		$test_data        = array(
			0 => $dataset_uri . '/aaaa',
			2 => $dataset_uri . '/dsadasda',
			3 => 'https://google.com',
			4 => 'https://test.com'
		);
		$instance->save_data( $test_data );
		// now it should save only two rows
		$rows = get_post_meta( $post_id, 'entity_same_as' );
		$this->assertCount( 2, $rows );
	}

}
