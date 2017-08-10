<?php
/**
 * Tests: Batch Analysis Service.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Batch_Analysis_Service_Test} class.
 *
 * @since   3.14.0
 * @package Wordlift
 */
class Wordlift_Batch_Analysis_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Batch_Analysis_Service} to test.
	 *
	 * @since  3.14.2
	 * @access private
	 * @var \Wordlift_Batch_Analysis_Service $batch_service The {@link Wordlift_Batch_Analysis_Service} to test.
	 */
	private $batch_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->batch_service = new Wordlift_Batch_Analysis_Service( new Wordlift(), Wordlift_Configuration_Service::get_instance() );

	}

	function test_submit_auto_selected_posts() {

		$result_1 = $this->batch_service->submit_auto_selected_posts( 'no' );

		// A post that must fall into the auto selection.
		$post_1 = $this->factory->post->create( array(
			'post_status' => 'publish',
		) );

		// A post that should not be analyzed because the status is `draft`.
		$post_2 = $this->factory->post->create( array(
			'post_status' => 'draft',
		) );

		// A post that should not be analyzed because it has an annotation.
		$post_3 = $this->factory->post->create( array(
			'post_status'  => 'publish',
			'post_content' => '<span id="urn:enhancement-xyz" class="class" itemid="itemid">Lorem Ipsum</span>',
		) );

		// A post that should not be analyzed because it has been analyzed already.
		$post_4 = $this->factory->post->create( array(
			'post_status' => 'publish',
		) );
		update_post_meta( $post_4, Wordlift_Batch_Analysis_Service::STATE_META_KEY, Wordlift_Batch_Analysis_Service::STATE_SUCCESS );

		// A page that must fall into the auto selection.
		$post_5 = $this->factory->post->create( array(
			'post_status' => 'publish',
			'post_type'   => 'page',
		) );

		// A post that should not be analyzed because it's an entity.
		$post_6 = $this->factory->post->create( array(
			'post_status' => 'publish',
			'post_type'   => 'entity',
		) );

		$result_2 = $this->batch_service->submit_auto_selected_posts( 'no' );

		// We expect 2 submitted posts/pages.
		$this->assertEquals( 2, $result_2, 'Expect to submit only 2 posts/page.' );

		// Check that the state has been set.
		$this->assertEquals( Wordlift_Batch_Analysis_Service::STATE_SUBMIT, $this->batch_service->get_state( $post_1 ) );
		$this->assertEquals( Wordlift_Batch_Analysis_Service::STATE_SUBMIT, $this->batch_service->get_state( $post_5 ) );
		$this->assertEquals( 'no', $this->batch_service->get_link( $post_1 ) );
		$this->assertEquals( 'no', $this->batch_service->get_link( $post_5 ) );

		// Check the other states.
		$this->assertEmpty( $this->batch_service->get_state( $post_2 ) );
		$this->assertEmpty( $this->batch_service->get_state( $post_3 ) );
		$this->assertEquals( Wordlift_Batch_Analysis_Service::STATE_SUCCESS, $this->batch_service->get_state( $post_4 ) );
		$this->assertEmpty( $this->batch_service->get_state( $post_6 ) );

	}

	public function test_batch_analysis_regex_1() {

		$content = 'a<span id="urn:enhancement-xyz" class="class" itemid="http://example.org">Lorem</span>';

		$matches = array();
		$warning = 0 < preg_match_all( '/\w<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">/', $content, $matches )
		           || 0 < preg_match_all( '/<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">\s/', $content, $matches );

		$this->assertTrue( $warning );

	}

	public function test_batch_analysis_regex_2() {

		$content = '<span id="urn:enhancement-xyz" class="class" itemid="http://example.org"> Lorem</span>';

		$matches = array();
		$warning = 0 < preg_match_all( '/\w<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">/', $content, $matches )
		           || 0 < preg_match_all( '/<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">\s/', $content, $matches );

		$this->assertTrue( $warning );

	}

	public function test_batch_analysis_regex_3() {

		$content = ' <span id="urn:enhancement-xyz" class="class" itemid="http://example.org">Lorem</span>.';

		$matches = array();
		$warning = 0 < preg_match_all( '/\w<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">/', $content, $matches )
		           || 0 < preg_match_all( '/<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">\s/', $content, $matches );

		$this->assertFalse( $warning );

	}

}
