<?php

use Wordlift\Sameas_Metabox\Task_Validator;

/**
 * @since 3.29.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group metabox
 */
class Metabox_Sameas_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var WL_Metabox_Field_sameas
	 */
	private $sameas_metabox_instance;

	/**
	 * @var Task_Validator
	 */
	private $instance;

	public function setUp() {
		parent::setUp();
		$config                        = array(
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
		$this->sameas_metabox_instance = new WL_Metabox_Field_sameas( $config );
		$this->instance = new Task_Validator( $this->configuration_service );
	}


//	public function test_on_plugin_upgrade_should_show_clean_up_task_in_dashboard_if_invalid_sameas_present() {
//		do_action( 'upgrader_process_complete' );
//
//	}


	public function test_many_posts_with_same_dataset_uris_should_find_only_one_post() {

		$post_ids = $this->factory()->post->create_many( 100, array(
			'post_type' => 'entity',
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Sameas task test %s' ),
		) );

		// we pick a post id and add an invalid same as.
		$dataset_uri = $this->configuration_service->get_dataset_uri();

		$invalid_dataset_uris = array(
			$dataset_uri . "/aaa",
			$dataset_uri . "/bbb",
		);

		$sample_post_ids = array_slice( $post_ids, 0, 5 );

		foreach ( $sample_post_ids as $post_id ) {
			$_POST['post_ID'] = $post_id;
			var_dump($invalid_dataset_uris);
			$this->sameas_metabox_instance->save_data( $invalid_dataset_uris );
		}

		// Check if we need to add a cleanup task
		$this->assertTrue( $this->instance->is_cleanup_task_should_be_shown() );
	}


}
