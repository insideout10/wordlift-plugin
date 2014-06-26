<?php
require_once 'functions.php';

/**
 * Class GeomapShortcodeTest
 */
class GeomapShortcodeTest extends WP_UnitTestCase
{
	private static $FIRST_POST_ID;
	private static $MOST_CONNECTED_ENTITY_ID;
	
    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Empty the blog.
        wl_empty_blog();
    }

    /**
     * Create:
     *  * 1 Post
     *  * 3 Place entities referenced by the Post
     *  * 1 Person entity reference by the Post
     *
     * Check that only the first 2 entities are returned when calling *wl_set_referenced_entities*.
     *
     * @uses wl_set_referenced_entities to retrieve the entities referenced by a post.
     */
    function testGetPlaces() {

        $post_id = wl_create_post( '', 'post-1', 'Post 1', 'publish', 'post' );

        $entity_1_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $entity_1_id, 'http://schema.org/Place' );
        add_post_meta( $entity_1_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $entity_1_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true );

        $entity_2_id = wl_create_post( "Entity 2 Text", 'entity-2', "Entity 2 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Place' );
        add_post_meta( $entity_2_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 41.20, true );
        add_post_meta( $entity_2_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 78.2, true );

        $entity_3_id = wl_create_post( 'Entity 3 Text', 'entity-3', 'Entity 3 Title', 'publish', 'entity' );
        wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Place' );
        add_post_meta( $entity_3_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 45.12, true );
        add_post_meta( $entity_3_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 90.3, true );

        $entity_4_id = wl_create_post( '', 'entity-4', 'Entity 4', 'publish', 'entity' );
        wl_set_entity_main_type( $entity_4_id, 'http://schema.org/Person' );

        wl_set_referenced_entities( $post_id, array(
            $entity_1_id,
            $entity_2_id,
            $entity_4_id
        ) );

        $places = wl_shortcode_geomap_get_places( $post_id );
        $this->assertCount( 2, $places );

        $places_ids = array_map( function ( $item ) { return $item->ID; }, $places );
        $this->assertContains( $entity_1_id, $places_ids );
        $this->assertContains( $entity_2_id, $places_ids );

        // From here onwards we check that the JSON response matches the places data.
        $json = wl_shortcode_geomap_to_json( $places );

        $response = json_decode( $json );

		// Check retrieved boundaries
        $this->assertTrue( isset( $response->boundaries ) );
		$this->assertCount( 2, $response->boundaries );	// Should contain two set of coordinates.
		$this->assertCount( 2, $response->boundaries[0] );	// [minLat, minLon]
		$this->assertCount( 2, $response->boundaries[1] );	// [maxLat, maxLon]
		
		// Check if coordinates are actually numbers
		$this->assertTrue( is_numeric($response->boundaries[0][0]) );
		$this->assertTrue( is_numeric($response->boundaries[0][1]) );
		$this->assertTrue( is_numeric($response->boundaries[1][0]) );
		$this->assertTrue( is_numeric($response->boundaries[1][1]) );
		
		// Check retrieved places
		$this->assertTrue( isset( $response->features ) );
		
		$i = 0;
		foreach($places as $place) {
			
			// Check object attributes
			$poi = $response->features[$i];
			$this->assertTrue( isset( $poi ) );
			$this->assertTrue( isset( $poi->type ) );
			$this->assertTrue( isset( $poi->properties ) );
			$this->assertNotEmpty( isset( $poi->properties->popupContent ) );
			$this->assertTrue( isset( $poi->geometry ) );
			$this->assertTrue( isset( $poi->geometry->coordinates ) );
			$this->assertCount( 2, $poi->geometry->coordinates );
			$this->assertTrue( is_numeric( $poi->geometry->coordinates[0] ) );
			$this->assertTrue( is_numeric( $poi->geometry->coordinates[1] ) );
			
			// Check consistency with declared places
			$coords = wl_get_coordinates( $place->ID );
			$coords = array( $coords['longitude'], $coords['latitude'] ); // Leaflet geoJSON wants them swapped
			$this->assertEquals($coords, $poi->geometry->coordinates);
			$i++;
		}
    }

    /**
     * Create:
     *  * 2 Posts
     *  * 1 Place entity referenced by both posts
     *
     * Check that the geomap popup of the place contains a link to the two posts.
     */
    function testPlacePopupRelatedPosts() {
		
		// Create two posts.
        $post_id_1 = wl_create_post( '', 'post-1', 'Post 1', 'publish', 'post' );
		$post_id_2 = wl_create_post( '', 'post-2', 'Post 2', 'publich', 'post');

		// Create a place-
        $place_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true );

		// Reference place from both posts-
        wl_set_referenced_entities( $post_id_1, array( $place_id ) );
		wl_set_referenced_entities( $post_id_2, array( $place_id ) );

		// Check referencing.
        $places = wl_shortcode_geomap_get_places( $post_id_1 );
        $this->assertCount( 1, $places );
		$this->assertEquals( $places[0]->ID, $place_id );

        // Check json formatted data.
        $json = wl_shortcode_geomap_to_json( $places );
        $response = json_decode( $json );
	
		// Check object attributes
		$poi = $response->features[0];
		$this->assertTrue( isset( $poi ) );
		$this->assertTrue( isset( $poi->properties ) );
		$this->assertTrue( isset( $poi->properties->popupContent ) );
		
		// Check if popup contains links to the two posts
		$popup = $poi->properties->popupContent;
		$link1 = esc_attr( get_permalink($post_id_1) );
		$this->assertContains( $link1, $popup );
		$link2 = esc_attr( get_permalink($post_id_2) );
		$this->assertContains( $link2, $popup );
		
		// Check no thumbnail has been echoed
		$this->assertNotContains( '<img', $popup );
    }
}
