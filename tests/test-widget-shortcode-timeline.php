<?php

/**
 * Class TimelineShortcodeTest
 * @group widget
 */
class TimelineShortcodeTest extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Timeline_Service} instance.
	 *
	 * @since  3.7.0
	 * @access private
	 * @var \Wordlift_Timeline_Service $timeline_service The {@link Wordlift_Timeline_Service} instance.
	 */
	private $timeline_service;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		add_theme_support( 'post-thumbnails' );

		$this->timeline_service = Wordlift_Timeline_Service::get_instance();

		$_REQUEST['_wpnonce'] = wp_create_nonce( 'wl_timeline' );

		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
	}

	function tearDown() {
		add_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}

	function _mock_api( $response, $request, $url ) {

		if ( 'POST' === $request['method']
		     && isset( $request['headers']['Content-type'] )
		     && 'application/sparql-update; charset=utf-8' === $request['headers']['Content-type']
		     && ( in_array( md5( $request['body'] ),
					array(
						'f934d227ea0458348a79b7175999620b',
						'38d8232cd981b7f35b387ea21322ddfd',
						'13e87a7955f921a009f088bb77953386',
						'3be0897f0cdb6652e3826e0dc714b586',
						'51021876ecebae68dc6e27423fe13595',
						'203115f9a023de0d83af24ba9601b86f',
						'126f67f5df76a494d21ad78c8e47e10f',
						'e2e78e4e7259368d37fccaba20070aca',
						'5013cd6b57c2a10baee01d032343a28c',
						'5ec4e0b391f3161c20708691479406fb',
						'52a7d2730fb8210e2abddf1f30e8094a',
						'147e036336058153f0ccc1ee16e6e221'
					) )
		          || preg_match( "~^INSERT DATA { <https://data\.localdomain\.localhost/dataset/post/post_1> <http://schema\.org/headline> \"Post 1\"@en \. 
<https://data\.localdomain\.localhost/dataset/post/post_1> <http://schema\.org/url> <http://example\.org/\?p=\d+> \. 
<https://data\.localdomain\.localhost/dataset/post/post_1> <http://www\.w3\.org/1999/02/22-rdf-syntax-ns#type> <http://schema\.org/Article> \.  };$~", $request['body'] ) )
		     && preg_match( '@/datasets/key=key123/queries$@', $url ) ) {

			return array(
				'response' => array( 'code' => 200 ),
				'body'     => '',
			);
		}

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/index$@', $url ) ) {

			return array(
				'response' => array( 'code' => 200 ),
				'body'     => '',
			);
		}

		return $response;
	}


	/**
	 * Create 4 test events of which 2 are related.
	 *
	 * @return array An array of events' posts.
	 * @since 3.7.0
	 */
	private function create_2_test_related_events() {

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

		wl_core_add_relation_instances( $post_id, WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_2_id,
			$entity_4_id,
		) );

		// Call retrieving function with null argument (i.e. global timeline)
		return $this->timeline_service->get_events( $post_id );
	}

	/**
	 * Create:
	 *  * 1 Post
	 *  * 3 Event entities of which 2 referenced by the Post
	 *  * 1 Person entity reference by the Post
	 */
	function test_get_events() {

		// We need to push entities to the Linked Data store for this test. We'll
		// turn entity push back off at the end of the test.
		self::turn_on_entity_push();

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

		wl_core_add_relation_instances( $post_id, WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_2_id,
			$entity_4_id,
		) );

		$events = Wordlift_Timeline_Service::get_instance()
		                                   ->get_events( $post_id );
		$this->assertCount( 2, $events );

		$event_ids = array_map( function ( $item ) {
			return $item->ID;
		}, $events );
		$this->assertContains( $entity_1_id, $event_ids );
		$this->assertContains( $entity_2_id, $event_ids );

		// From here onwards we check that the JSON response matches the events data.
		$response = Wordlift_Timeline_Service::get_instance()
		                                     ->to_json( $events );

		$this->assertTrue( isset( $response['timeline'] ) );
		$this->assertCount( 2, $response['timeline']['events'] );
		// This property doesn't exist anymore once we transitioned to TimelineJS v3
		// $this->assertEquals( 'default', $response['timeline']['type'] );

		$entity_1 = get_post( $entity_1_id );
		$entity_2 = get_post( $entity_2_id );

		$entity_1_headline = '<a href="' . get_permalink( $entity_1_id ) . '">' . $entity_1->post_title . '</a>';
		$entity_2_headline = '<a href="' . get_permalink( $entity_2_id ) . '">' . $entity_2->post_title . '</a>';

		// We're using fixed content (see lines 100-103).
		// $entity_1_date_start = str_replace( '-', ',', get_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_START, TRUE ) );
		// $entity_1_date_end   = str_replace( '-', ',', get_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_END, TRUE ) );
		// $entity_2_date_start = str_replace( '-', ',', get_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_START, TRUE ) );
		// $entity_2_date_end   = str_replace( '-', ',', get_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_END, TRUE ) );

		// This is the right order, i.e. the event 1 is in the 2nd position in the dates array.
		$date_1 = $response['timeline']['events'][1];
		$date_2 = $response['timeline']['events'][0];

		// To factor DST variance
		$this->assertContains( implode( ',', array_values( $date_1['start_date'] ) ), array( '2014,1,1', '2014,1,2' ) );
		$this->assertContains( implode( ',', array_values( $date_1['end_date'] ) ), array( '2014,1,7', '2014,1,8' ) );
		$this->assertContains( implode( ',', array_values( $date_2['start_date'] ) ), array(
			'2014,1,1',
			'2014,1,2',
			'2014,1,3'
		) );
		$this->assertContains( implode( ',', array_values( $date_2['end_date'] ) ), array(
			'2014,1,7',
			'2014,1,8',
			'2014,1,9'
		) );

		$this->assertTrue( isset( $date_1['text']['text'] ) );
		$this->assertTrue( isset( $date_2['text']['text'] ) );

		// We don't publish the post content anymore in the widget.
		// $this->assertEquals( $entity_1->post_content, $date_1['text'] );
		// $this->assertEquals( $entity_2->post_content, $date_2['text'] );

		$this->assertEquals( $entity_1_headline, $date_1['text']['headline'], var_export( $response, true ) );
		$this->assertEquals( $entity_2_headline, $date_2['text']['headline'] );

		$thumbnail_1 = wp_get_attachment_image_src( $thumbnail_1_id );
		$thumbnail_2 = wp_get_attachment_image_src( $thumbnail_2_id );

		$this->assertEquals( $thumbnail_1[0], $date_1['media']['url'] );
		$this->assertEquals( $thumbnail_2[0], $date_2['media']['url'] );

		//
		self::turn_off_entity_push();;

	}

	function createPostThumbnail( $guid, $label, $content_type, $file, $post_id ) {

		$attachment = array(
			'guid'           => $guid,
			'post_title'     => $label, // Set the title to the post title.
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_mime_type' => $content_type,
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
		$post_2_id = wl_create_post( '', 'post-2', 'Post 2', 'publish', 'page' );

		$entity_1_id = wl_create_post( "Entity 1's Text", 'entity-1', "Entity 1's Title", 'publish', 'entity' );
		wl_set_entity_main_type( $entity_1_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-01', true );
		add_post_meta( $entity_1_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-07', true );

		$entity_2_id = wl_create_post( "Entity 2's Text", 'entity-2', "Entity 2's Title", 'publish', 'entity' );
		wl_set_entity_main_type( $entity_2_id, Wordlift_Schema_Service::SCHEMA_EVENT_TYPE );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_START, '2014-01-02', true );
		add_post_meta( $entity_2_id, Wordlift_Schema_Service::FIELD_DATE_END, '2014-01-08', true );

		$entity_3_id = wl_create_post( 'Entity 3 Text', 'entity-3', 'Entity 3 Title', 'publish', 'entity' );
		wl_set_entity_main_type( $entity_3_id, 'http://schema.org/Place' );
		add_post_meta( $entity_3_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 45.12, true );
		add_post_meta( $entity_3_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 90.3, true );

		wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_3_id,
		) );
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

	/**
	 * Test the that width and the height are set according to the provided shortcodes.
	 *
	 * @since 3.7.0
	 */
	function test_width_and_height() {

		$width   = '100px';
		$height  = '200px';
		$content = do_shortcode( "[wl_timeline width='$width' height='$height']" );

		$this->assertTrue( - 1 < strpos( $content, "width:$width" ) );
		$this->assertTrue( - 1 < strpos( $content, "height:$height" ) );

	}

	/**
	 * Test the JSON output when the request `display_image_as` parameter is set
	 * to `media`.
	 *
	 * @since 3.7.0
	 */
	function test_images_as_media() {

		// Set the display as 'media'.
		$_REQUEST['display_images_as'] = 'media';

		// Get the JSON from test events.
		$json = $this->timeline_service->to_json( $this->create_2_test_related_events() );

		// Check that we have a timeline.
		$this->assertArrayHasKey( 'timeline', $json );

		// Check that we have events.
		$this->assertArrayHasKey( 'events', $json['timeline'] );

		// Check that we have 4 events.
		$this->assertCount( 2, $json['timeline']['events'] );

		// Check that each event has a media and a thumbnail.
		foreach ( $json['timeline']['events'] as $event ) {
			$this->assertArrayHasKey( 'media', $event );
			$this->assertArrayHasKey( 'url', $event['media'] );
			$this->assertArrayHasKey( 'thumbnail', $event['media'] );
			$this->assertFalse( array_key_exists( 'background', $event ) );
		}

	}

	/**
	 * Test the JSON output when the request `display_image_as` parameter is set
	 * to anything else but `media` or `background` (it should default to `media`)s.
	 *
	 * @since 3.7.0
	 */
	function test_images_as_media_when_display_images_as_anything_else() {

		// Set the display as 'media'.
		$_REQUEST['display_images_as'] = 'anything_else';

		// Get the JSON from test events.
		$json = $this->timeline_service->to_json( $this->create_2_test_related_events() );

		// Check that we have a timeline.
		$this->assertArrayHasKey( 'timeline', $json );

		// Check that we have events.
		$this->assertArrayHasKey( 'events', $json['timeline'] );

		// Check that we have 4 events.
		$this->assertCount( 2, $json['timeline']['events'] );

		// Check that each event has a media and a thumbnail.
		foreach ( $json['timeline']['events'] as $event ) {
			$this->assertArrayHasKey( 'media', $event );
			$this->assertArrayHasKey( 'url', $event['media'] );
			$this->assertArrayHasKey( 'thumbnail', $event['media'] );
			$this->assertFalse( array_key_exists( 'background', $event ) );
		}

	}

	/**
	 * Test the JSON output when the request `display_image_as` parameter is set
	 * to `background`.
	 *
	 * @since 3.7.0
	 */
	function test_images_as_background() {

		// Set the display as 'background'.
		$_REQUEST['display_images_as'] = 'background';

		// Get the JSON from test events.
		$json = $this->timeline_service->to_json( $this->create_2_test_related_events() );

		// Check that we have a timeline.
		$this->assertArrayHasKey( 'timeline', $json );

		// Check that we have events.
		$this->assertArrayHasKey( 'events', $json['timeline'] );

		// Check that we have 2 events.
		$this->assertCount( 2, $json['timeline']['events'] );

		// Check that each event has a media and a thumbnail.
		foreach ( $json['timeline']['events'] as $event ) {
			$this->assertArrayHasKey( 'media', $event );
			$this->assertFalse( array_key_exists( 'url', $event['media'] ) );
			$this->assertArrayHasKey( 'thumbnail', $event['media'] );
			$this->assertArrayHasKey( 'background', $event );
			$this->assertArrayHasKey( 'url', $event['background'] );
		}

	}

	/**
	 * Test setting the excerpt to 0 words, that we have no text.
	 *
	 * @since 3.7.0
	 */
	function test_excerpt_set_to_zero() {

		// Set the excerpt_length to zero.
		$_REQUEST['excerpt_length'] = 0;

		// Get the JSON from test events.
		$json = $this->timeline_service->to_json( $this->create_2_test_related_events() );

		// Check that we have a timeline.
		$this->assertArrayHasKey( 'timeline', $json );

		// Check that we have events.
		$this->assertArrayHasKey( 'events', $json['timeline'] );

		// Check that we have 2 events.
		$this->assertCount( 2, $json['timeline']['events'] );

		// Check that each event has a media and a thumbnail.
		foreach ( $json['timeline']['events'] as $event ) {
			$this->assertArrayHasKey( 'text', $event );
			$this->assertArrayHasKey( 'headline', $event['text'] );
			$this->assertFalse( array_key_exists( 'text', $event['text'] ) );
		}

	}

	/**
	 * Test setting the excerpt to 1 word, that we have a text.
	 *
	 * @since 3.7.0
	 */
	function test_excerpt_set_to_one() {

		// Set the excerpt_length to 1 word.
		$_REQUEST['excerpt_length'] = 1;

		// Get the JSON from test events.
		$json = $this->timeline_service->to_json( $this->create_2_test_related_events() );

		// Check that we have a timeline.
		$this->assertArrayHasKey( 'timeline', $json );

		// Check that we have events.
		$this->assertArrayHasKey( 'events', $json['timeline'] );

		// Check that we have 2 events.
		$this->assertCount( 2, $json['timeline']['events'] );

		// Check that each event has a media and a thumbnail.
		foreach ( $json['timeline']['events'] as $event ) {
			$this->assertArrayHasKey( 'text', $event );
			$this->assertArrayHasKey( 'headline', $event['text'] );
			$this->assertArrayHasKey( 'text', $event['text'] );
		}

	}

}
