<?php

// TODO: split this class in more focused test classes.

/**
 * Class WordLiftTest The main test class.
 */
class WordLiftTest extends WP_UnitTestCase {

    // this vars are set during test SetUp.
    private $wp_version;
    private $wp_multisite;
    private $dataset_name;

    private $dataset_name_prefix = "wordlift-tests";

    // the configuration parameters for WordLift.
    private $user_id;
    private $application_key;

    // sample entity data.
    // the dbpedia URI.
    private $entity_same_as = 'http://dbpedia.org/resource/Colorado';
    private $entity_name    = 'Colorado';

    function get_dataset_name() {
        $dataset_name = "$this->dataset_name_prefix-php-" . PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION . "-wp-$this->wp_version-ms-$this->wp_multisite";
        return str_replace('.', '-', $dataset_name);
    }

    /**
     * Get the entity URI for the custom dataset. It'll be constructed using the configured user id and the dataset name.
     * @param string $name The name of the entity.
     * @return string The entity URI.
     */
    function get_entity_uri($name) {

        $user_id      = wordlift_configuration_user_id();
        $dataset_name = wordlift_configuration_dataset_id();
        return "http://data.redlink.io/$user_id/$dataset_name/entity/$name";
    }

    /**
     * Get the post URI.
     * @param int $id The post ID.
     * @return string The post URI.
     */
    function get_post_uri($id) {
        $user_id      = wordlift_configuration_user_id();
        $dataset_name = wordlift_configuration_dataset_id();
        return "http://data.redlink.io/$user_id/$dataset_name/post/$id";
    }

    function add_allowed_post_tags() {
        global $allowedposttags;

        $tags = array( 'span' );
        $new_attributes = array(
            'itemscope' => array(),
            'itemtype'  => array(),
            'itemprop'  => array(),
            'itemid'    => array()
        );

        foreach ( $tags as $tag ) {
            if ( isset( $allowedposttags[ $tag ] ) && is_array( $allowedposttags[ $tag ] ) )
                $allowedposttags[ $tag ] = array_merge( $allowedposttags[ $tag ], $new_attributes );
        }
    }

    /**
     * Set up the test, allowing the microdata taggings and setting the plugin options.
     */
    function setUp() {

        parent::setUp();

        // Set the user ID and the application key.
        $this->user_id         = getenv('REDLINK_USER_ID');
        $this->application_key = getenv('REDLINK_APP_KEY');


        // set the dataset name for this test.
        $this->wp_version   = getenv('WP_VERSION');
        $this->wp_multisite = getenv('WP_MULTISITE');
        $this->dataset_name = $this->get_dataset_name();

        // TODO: ensure dataset cleanup on Redlink.

        $this->add_allowed_post_tags();

        // set the WordLift test configuration.
        update_option(WORDLIFT_OPTIONS, array(
            'user_id'         => $this->user_id,
            'dataset_name'    => $this->dataset_name,
            'application_key' => $this->application_key
        ));

    } // end setup

    /**
     * Test the configuration settings.
     */
    function testConfiguration() {
        $this->assertEquals( $this->user_id,      wordlift_configuration_user_id() );
        $this->assertEquals( $this->dataset_name, wordlift_configuration_dataset_id() );
    }

    /**
     * Test saving a post with entities.
     */
    function testSavingAPostWithEntities() {

        // set the post content.
        $content = <<<EOF
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident <span class="textannotation place disambiguated" id="urn:enhancement-ba2ace0d-2e21-ae84-3a7c-db37206be078" itemid="http://dbpedia.org/resource/Colorado" itemscope="itemscope" itemtype="http://schema.org/Place"><span itemprop="name">Colorado</span></span>, sunt in culpa qui officia deserunt mollit anim id est laborum.
EOF;

        // create the post.
        $post_id = wp_insert_post( array(
            'post_type'    => 'post',
            'post_content' => $content,
            'post_title'   => 'A Sample Post'
        ) );

        // get the entity URI.
        $entity_uri = $this->get_entity_uri( $this->entity_name );

        // try to get the post using the WordLift method.
        $posts_using_entity_uri = wordlift_get_entity_posts_by_uri( $entity_uri );

        // check that an entity is found.
        $this->assertCount( 1, $posts_using_entity_uri );

        // TODO: check that the post_meta/same_as has the same_as value.

        // try to get the post using the WordLift method.
        $posts_using_entity_same_as = wordlift_get_entity_posts_by_uri( $this->entity_same_as );

        // check that an entity is found.
        $this->assertCount( 1, $posts_using_entity_same_as );

        // save a reference to the post.
        $entity_post_id = $posts_using_entity_same_as[0]->ID;

        // test the wordlift_get_related_posts function.
        $related_posts = wordlift_get_related_posts( $entity_post_id, 'any' );
        $this->assertEquals( 1, count( $related_posts ) );


        // check that the entity URI matches.
        $entity_uri_from_post_meta = get_post_meta( $posts_using_entity_same_as[0]->ID, 'entity_url', true );
        $this->assertEquals( $entity_uri, $entity_uri_from_post_meta );

        // check in fact that they're the same post.
        $this->assertEquals( $entity_post_id, $posts_using_entity_same_as[0]->ID );

        // check that the post relates to the entity.
        $related_entities = get_post_meta( $post_id, 'wordlift_related_entities', true );
        $this->assertTrue( in_array( $entity_post_id, $related_entities ) );

        // check that the entity relates to the post.
        $related_posts = get_post_meta( $entity_post_id, 'wordlift_related_posts', true );
        $this->assertTrue( in_array( $post_id, $related_posts ) );

        // check that the post is created on Redlink.
        $post_uri    = $this->get_post_uri( $post_id );
        $wp_response = wp_remote_get( $post_uri . '.json', array( 'sslverify' => false, 'blocking' => true, 'httpversion' => '1.1' ) );

        // check that the response is not an error.
        $this->assertFalse( is_wp_error( $wp_response ) );

        // check that the response code is 200-OK.
        $this->assertEquals( 200, $wp_response['response']['code'] );

        // get the graph instance.
        $json  = json_decode( $wp_response['body'] );
        $graph = $json[0]->{'@graph'}[0];

        // check that the id is equal to the post URI.
        $this->assertEquals( $post_uri, $graph->{'@id'} );

        $this->assertTrue( in_array( 'http://schema.org/BlogPosting', $graph->{'@type'} ) );

        // check why sometimes we get that this property doesn't exist.
        // check that the post references the entity URI.
        $this->assertTrue( array_reduce( $graph->{'http://purl.org/dc/terms/references'},
            function( $result, $item ) use ( $entity_uri ) {
                return $result || ( $entity_uri === $item->{'@id'} );
            } , false
        ) );

        // check that the post published date is correct.
        $post_date_published = get_the_time( 'c', $post_id );
        $this->assertTrue( array_reduce( $graph->{'http://schema.org/datePublished'},
            function( $result, $item ) use ( $post_date_published ) {
                return $result || ( $post_date_published === $item->{'@value'} );
            } , false
        ) );

        // check that the post published date is correct.
        $post_permalink = get_permalink( $post_id );
        $this->assertTrue( array_reduce( $graph->{'http://schema.org/url'},
            function( $result, $item ) use ( $post_permalink ) {
                return $result || ( $post_permalink === $item->{'@id'} );
            } , false
        ) );

        // check that the post published date is correct.
        $post_title = get_the_title( $post_id );
        $this->assertTrue( array_reduce( $graph->{'http://www.w3.org/2000/01/rdf-schema#label'},
            function( $result, $item ) use ( $post_title ) {
                return $result || ( $post_title === $item->{'@value'} );
            } , false
        ) );



        // check that the entity is created on Redlink.
        $wp_response = wp_remote_get( $entity_uri . '.json', array( 'sslverify' => false, 'blocking' => true, 'httpversion' => '1.1' ) );

        // check that the response is not an error.
        $this->assertFalse( is_wp_error( $wp_response ) );

        // check that the response code is 200-OK.
        $this->assertEquals( 200, $wp_response['response']['code'] );

        // get the graph instance.
        $json  = json_decode( $wp_response['body'] );
        $graph = $json[0]->{'@graph'}[0];

        // check that the id is equal to the entity URI.
        $this->assertEquals( $entity_uri, $graph->{'@id'} );

        // check that the schema:url is equal to the permalink.
        $entity_post_permalink = get_permalink( $entity_post_id );
        $this->assertTrue( array_reduce( $graph->{'http://schema.org/url'},
            function( $result, $item ) use ( $entity_post_permalink ) {
                return $result || ( $entity_post_permalink === $item->{'@id'} );
            } , false
        ) );

        // check that the label is equal to the entity name.
        $entity_name = $this->entity_name;
        $this->assertTrue( array_reduce( $graph->{'http://www.w3.org/2000/01/rdf-schema#label'},
            function( $result, $item ) use ( $entity_name ) {
                return $result || ( $entity_name === $item->{'@value'} );
            } , false
        ) );

        // check that the same as is included.
        $entity_same_as = $this->entity_same_as;
        $this->assertTrue( array_reduce( $graph->{'http://www.w3.org/2002/07/owl#sameAs'},
            function( $result, $item ) use ( $entity_same_as ) {
                return $result || ( $entity_same_as === $item->{'@id'} );
            } , false
        ) );

        // set the post content.
        $content = <<<EOF
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident Colorado, sunt in culpa qui officia deserunt mollit anim id est laborum.
EOF;

        // update the post removing the reference to the entity.
        $post_id = wp_insert_post( array(
            'ID'           => $post_id,
            'post_type'    => 'post',
            'post_content' => $content,
            'post_title'   => 'A Sample Post'
        ) );

        // check that there are no entities related to the post.
        $related_entities = get_post_meta( $post_id, 'wordlift_related_entities', true );
        $this->assertCount( 0, $related_entities );

        // check that the entity no more relates to the post.
        $related_posts = get_post_meta( $entity_post_id, 'wordlift_related_posts', true );
        //        echo "[ entity_post_id :: $entity_post_id ][ related_posts :: " . join( ',', $related_posts ) . " ]\n";
        $this->assertCount( 0, $related_posts );

    }

    /**
     * Test create an entity.
     */
    function testCreateEntity() {}

    /**
     * Test update an entity.
     */
    function testUpdateEntity() {}

    /**
     * Test an entity which relates to other entities.
     */
    function testEntityWithEntities() {}

    /**
     * Test the relationship between a post and its entities.
     */
    function testPostRelationshipWithEntities() {}


}

