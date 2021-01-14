<?php

use Wordlift\Sameas_Metabox\Task_Validator;

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group metabox
 */
class Metabox_Sameas_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Wordlift_Install_3_28_0
	 */
	private $install_instance;

	/**
	 * @var Task_Validator
	 */
	private $instance;

	public function setUp() {
		parent::setUp();
		$this->install_instance = new Wordlift_Install_3_28_0();
	}


//	public function test_on_plugin_upgrade_should_show_clean_up_task_in_dashboard_if_invalid_sameas_present() {
//		do_action( 'upgrader_process_complete' );
//
//	}


	public function test_many_posts_with_same_dataset_uris_should_find_only_one_post() {

		$post_ids = $this->factory()->post->create_many( 100, array(
			'post_type' => 'entity',
			'post_status' => 'publish'
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Sameas task test %s' ),
		) );

		// we pick a post id and add an invalid same as.
		$dataset_uri = $this->configuration_service->get_dataset_uri();

		$invalid_dataset_uris = array(
			$dataset_uri . "/aaa",
			$dataset_uri . "/bbb",
		);

		$sample_post_ids = array_slice( $post_ids, 10, 5 );

		foreach ( $sample_post_ids as $post_id ) {

			foreach ( $invalid_dataset_uris as $uri ) {
				add_post_meta( $post_id, Wordlift_Schema_Service::FIELD_SAME_AS, $uri );
			}
		}

		// Check if we need to add a cleanup task
		$this->assertFalse( $this->is_invalid_dataset_uris_present() );
	}



	/**
	 * @return bool
	 */
	public function is_invalid_dataset_uris_present() {

		$local_dataset_uri = $this->configuration_service->get_dataset_uri();

		$posts = get_posts( array(
			'post_type'   => \Wordlift_Entity_Service::valid_entity_post_types(),
			'meta_query'  => array(
				array(
					'key'     => \Wordlift_Schema_Service::FIELD_SAME_AS,
					'value'   => $local_dataset_uri,
					'compare' => 'LIKE'
				)
			),
			'numberposts' => 1
		) );

		return count( $posts ) > 0;
	}


}
