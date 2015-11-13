<?php
require_once 'functions.php';

/**
 * Class TimelineShortcodeTest
 */
class TimelineShortcodeTest extends WP_UnitTestCase {
	private static $FIRST_POST_ID;
	private static $MOST_CONNECTED_ENTITY_ID;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		// Empty the blog.
		wl_empty_blog();

		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Create:
	 *  * 1 Post
	 *  * 3 Event entities of which 2 referenced by the Post
	 *  * 1 Person entity reference by the Post
	 */
	function testGetEvents() {
		$log = Wordlift_Log_Service::get_logger( 'testGetEvents' );

		$post_id = wl_create_post( '', 'post-1', 'Post 1', 'publish', 'post' );

		$entity_1_id    = wl_create_post( "Entity 1's\nText", 'entity-1', "Entity 1's Title", 'publish', 'entity' );
		$thumbnail_1_id = $this->createPostThumbnail( 'http://example.org/entity_1.png', 'Entity 1 Thumbnail', 'image/png', 'dummy/image_1.png', $entity_1_id );
		wl_set_entity_main_type( $entity_1_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-01', true );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-07', true );

		$entity_2_id    = wl_create_post( "Entity 2's\nText", 'entity-2', "Entity 2's Title", 'publish', 'entity' );
		$thumbnail_2_id = $this->createPostThumbnail( 'http://example.org/entity_2.png', 'Entity 2 Thumbnail', 'image/png', 'dummy/image_2.png', $entity_2_id );
		wl_set_entity_main_type( $entity_2_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-02', true );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-08', true );

		$entity_3_id = wl_create_post( '', 'entity-3', 'Entity 3', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_3_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_3_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-03', true );
		add_post_meta( $entity_3_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-09', true );

		$entity_4_id = wl_create_post( '', 'entity-4', 'Entity 4', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_4_id, 'http://schema.org/Person' );

		wl_write_log( "[ entity 1 ID :: $entity_1_id ][ entity 2 ID :: $entity_2_id ][ entity 3 ID :: $entity_3_id ][ entity 4 ID :: $entity_4_id ]" );

		wl_core_add_relation_instances( $post_id, WL_WHAT_RELATION, array( $entity_1_id, $entity_2_id, $entity_4_id ) );

		$events = Wordlift_Timeline_Service::get_instance()->get_events( $post_id );
		$this->assertCount( 2, $events );

		$event_ids = array_map( function ( $item ) {
			return $item->ID;
		}, $events );
		$this->assertContains( $entity_1_id, $event_ids );
		$this->assertContains( $entity_2_id, $event_ids );

		// From here onwards we check that the JSON response matches the events data.
		$response = Wordlift_Timeline_Service::get_instance()->to_json( $events );

		$this->assertTrue( isset( $response['timeline'] ) );
		$this->assertCount( 2, $response['timeline']['date'] );
		$this->assertEquals( 'default', $response['timeline']['type'] );

		$entity_1 = get_post( $entity_1_id );
		$entity_2 = get_post( $entity_2_id );

		$entity_1_headline = '<a href="' . get_permalink( $entity_1_id ) . '">' . $entity_1->post_title . '</a>';
		$entity_2_headline = '<a href="' . get_permalink( $entity_2_id ) . '">' . $entity_2->post_title . '</a>';

		$entity_1_date_start = str_replace( '-', ',', get_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_START, true ) );
		$entity_1_date_end   = str_replace( '-', ',', get_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_END, true ) );
		$entity_2_date_start = str_replace( '-', ',', get_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_START, true ) );
		$entity_2_date_end   = str_replace( '-', ',', get_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_END, true ) );

		// This is the right order, i.e. the event 1 is in the 2nd position in the dates array.
		$date_1 = $response['timeline']['date'][1];
		$date_2 = $response['timeline']['date'][0];

		$this->assertEquals( $entity_1_date_start, $date_1['startDate'] );
		$this->assertEquals( $entity_1_date_end, $date_1['endDate'] );
		$this->assertEquals( $entity_2_date_start, $date_2['startDate'] );
		$this->assertEquals( $entity_2_date_end, $date_2['endDate'] );

		$this->assertEquals( $entity_1->post_content, $date_1['text'] );
		$this->assertEquals( $entity_2->post_content, $date_2['text'] );

		$this->assertEquals( $entity_1_headline, $date_1['headline'] );
		$this->assertEquals( $entity_2_headline, $date_2['headline'] );

		$thumbnail_1 = wp_get_attachment_image_src( $thumbnail_1_id );
		$thumbnail_2 = wp_get_attachment_image_src( $thumbnail_2_id );

		$this->assertEquals( $thumbnail_1[0], $date_1['asset']['media'] );
		$this->assertEquals( $thumbnail_2[0], $date_2['asset']['media'] );
	}

	function createPostThumbnail( $guid, $label, $content_type, $file, $post_id ) {

		$attachment = array(
			'guid'           => $guid,
			'post_title'     => $label, // Set the title to the post title.
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_mime_type' => $content_type
		);

		// Create the attachment in WordPress and generate the related metadata.
		$attachment_id = wp_insert_attachment( $attachment, $file, $post_id );
		wl_write_log( "createPostThumbnail [ attachment ID :: $attachment_id ]" );
		wl_write_log( "createPostThumbnail [ " . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . " ]" );

		// Set it as the featured image.
		$this->assertTrue( false !== set_post_thumbnail( $post_id, $attachment_id ) );

		return $attachment_id;
	}

	/**
	 * Create:
	 *  * 2 Post
	 *  * 2 Event entities referenced, one per Post
	 *  * 1 Place entity as a distractor
	 * Check that the 2 events are retrieved from the global timeline (no post specified).
	 */
	function testGlobalTimeline() {

		// Create posts
		$post_1_id = wl_create_post( '', 'post-1', 'Post 1', 'publish', 'post' );
		$post_2_id = wl_create_post( '', 'post-2', 'Post 2', 'publish', 'post' );

		// Create entities (2 events and one place)

		$entity_1_id = wl_create_post( "Entity 1's Text", 'entity-1', "Entity 1's Title", 'publish', 'entity' );
		wl_set_entity_main_type( $entity_1_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-01', true );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-07', true );

		$entity_2_id = wl_create_post( "Entity 2's Text", 'entity-2', "Entity 2's Title", 'publish', 'entity' );
		wl_set_entity_main_type( $entity_2_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-02', true );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-08', true );

		$entity_3_id = wl_create_post( 'Entity 3 Text', 'entity-3', 'Entity 3 Title', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Place' );
		add_post_meta( $entity_3_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 45.12, true );
		add_post_meta( $entity_3_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 90.3, true );

		wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array( $entity_1_id, $entity_3_id ) );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

		// Call retrieving function with null argument (i.e. global timeline)
		$events = Wordlift_Timeline_Service::get_instance()->get_events();
		$this->assertCount( 2, $events );

		$event_ids = array_map( function ( $item ) {
			return $item->ID;
		}, $events );
		$this->assertContains( $entity_1_id, $event_ids );
		$this->assertContains( $entity_2_id, $event_ids );
	}
}
