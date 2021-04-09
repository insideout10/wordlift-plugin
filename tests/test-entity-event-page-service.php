<?php
/**
 * Tests: event entity archive page Service.
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Entity_Event_Page_Service_Test} class.
 *
 * @since   3.12.0
 * @package Wordlift
 * @group entity
 */
class Wordlift_Entity_Event_Page_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test non main query is ignored.
	 *
	 * @since 3.12.0
	 */
	public function test_non_main_query_ignored() {

		$entity_type_service = $this->get_wordlift_test()->get_entity_type_service();
		$entity_1_id         = $this->factory->post->create( array(
			'post_type'    => 'entity',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
			'post_date'    => '2014-03-01',
		) );

		$entity_type_service->set( $entity_1_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-01', true );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-07', true );

		$entity_2_id = $this->factory->post->create( array(
			'post_type'    => 'entity',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
			'post_date'    => '2014-02-01',
		) );

		$entity_type_service->set( $entity_2_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-02-01', true );

		$entity_3_id = $this->factory->post->create( array(
			'post_type'    => 'entity',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
			'post_date'    => '2014-01-01',
		) );

		$entity_type_service->set( $entity_3_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_3_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-21', true );

		$query = new WP_Query( array(
			'tax_query' => array(
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'slug',
					'terms'    => 'event',
				),
			),
		) );
		$posts = $query->get_posts();

		// test that three entities are returned.
		$this->assertEquals( 3, count( $posts ) );

		// ... and that it is the reverse publishing order.
		$this->assertEquals( $entity_1_id, $posts[0]->ID );
		$this->assertEquals( $entity_2_id, $posts[1]->ID );
		$this->assertEquals( $entity_3_id, $posts[2]->ID );

	}



}
