<?php

use Wordlift\Sameas_Metabox\Task_Validator;

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group metabox
 */
class Install_3_28_0_Test extends Wordlift_Unit_Test_Case {

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


	public function test_when_installed_should_remove_the_meta_values() {

		$post_ids = $this->factory()->post->create_many( 100, array(
			'post_type'   => 'entity',
			'post_status' => 'publish'
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Sameas task test %s' ),
		) );

		// we pick a post id and add an invalid same as.
		$dataset_uri = $this->configuration_service->get_dataset_uri();

		$invalid_dataset_uris = array(
			$dataset_uri . "/aaa",
			$dataset_uri . "/bbb",
			"foo",
			"htt://bar"
		);

		$sample_post_ids = array_slice( $post_ids, 10, 5 );

		foreach ( $sample_post_ids as $post_id ) {

			$this->add_dataset_uris( $invalid_dataset_uris, $post_id );
		}

		$this->install_instance->install();

		// after running install the uris should not be present.
		$this->assertFalse( $this->is_invalid_dataset_uris_present() );
	}

	public function test_when_installed_should_not_delete_valid_urls() {
		$post_1             = $this->factory()->post->create();
		$valid_dataset_uris = array(
			'https://google.com',
			'https://test.com',
		);
		$this->add_dataset_uris( $valid_dataset_uris, $post_1 );
		$this->install_instance->install();
		$this->assertCount( 2, get_post_meta( $post_1, Wordlift_Schema_Service::FIELD_SAME_AS ) );
	}

	public function test_when_installed_should_not_delete_valid_http_urls() {
		$post_1             = $this->factory()->post->create();
		$valid_dataset_uris = array(
			'http://google.com',
			'http://test.com',
		);
		$this->add_dataset_uris( $valid_dataset_uris, $post_1 );
		$this->install_instance->install();
		$this->assertCount( 2, get_post_meta( $post_1, Wordlift_Schema_Service::FIELD_SAME_AS ) );
	}



	/**
	 * @return bool
	 */
	public function is_invalid_dataset_uris_present() {

		$local_dataset_uri = $this->configuration_service->get_dataset_uri();

		$posts = get_posts( array(
			'post_type'   => \Wordlift_Entity_Service::valid_entity_post_types(),
			'meta_query'  => array(
				'relation' => 'OR',
				array(
					'key'     => \Wordlift_Schema_Service::FIELD_SAME_AS,
					'value'   => $local_dataset_uri,
					'compare' => 'LIKE'
				),
				array(
					'key'     => \Wordlift_Schema_Service::FIELD_SAME_AS,
					'value'   => 'http://',
					'compare' => 'NOT LIKE'
				),
				array(
					'key'     => \Wordlift_Schema_Service::FIELD_SAME_AS,
					'value'   => 'https://',
					'compare' => 'NOT LIKE'
				)
			),
			'numberposts' => 1
		) );

		return count( $posts ) > 0;
	}

	/**
	 * @param array $dataset_uris
	 * @param $post_id
	 */
	private function add_dataset_uris( $dataset_uris, $post_id ) {
		foreach ( $dataset_uris as $uri ) {
			add_post_meta( $post_id, Wordlift_Schema_Service::FIELD_SAME_AS, $uri );
		}
	}


}
