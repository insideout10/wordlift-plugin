<?php

// TODO: split this class in more focused test classes.

/**
 * Class WordLiftTest The main test class.
 */
class WordLiftTest extends WP_UnitTestCase {

    // the configuration parameters for WordLift.
    private $user_id         = 353;
    private $dataset_name    = 'wordlift';
    private $application_key = '5VnRvvkRyWCN5IWUPhrH7ahXfGCBV8N0197dbccf';

    // sample entity data.
    // the dbpedia URI.
    private $entity_same_as = 'http://dbpedia.org/resource/Colorado';
    private $entity_name    = 'Colorado';
    // the expected entity URI.
    private $entity_uri     = 'http://data.redlink.io/353/wordlift/resource/Colorado';

    /**
     * Get the entity URI for the custom dataset. It'll be constructed using the configured user id and the dataset name.
     * @param string $name The name of the entity.
     * @return string The entity URI.
     */
    function get_entity_uri($name) {
        $user_id      = wordlift_configuration_user_id();
        $dataset_name = wordlift_configuration_dataset_id();
        return "http://data.redlink.io/$user_id/$dataset_name/resource/$name";
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

        // try to get the post using the WordLift method.
        $posts_using_entity_uri = wordlift_get_entity_posts_by_uri( $this->entity_uri );

        // check that an entity is found.
        $this->assertCount( 1, $posts_using_entity_uri );

        // TODO: check that the post_meta/same_as has the same_as value.

        // try to get the post using the WordLift method.
        $posts_using_entity_same_as = wordlift_get_entity_posts_by_uri( $this->entity_same_as );

        // check that an entity is found.
        $this->assertCount( 1, $posts_using_entity_same_as );

        // save a reference to the post.
        $entity_post_id = $posts_using_entity_same_as[0]->ID;

        // check that the entity URI matches.
        $entity_uri_from_post_meta = get_post_meta( $posts_using_entity_same_as[0]->ID, 'entity_url', true );
        $this->assertEquals( $this->entity_uri, $entity_uri_from_post_meta );

        // check in fact that they're the same post.
        $this->assertEquals( $entity_post_id, $posts_using_entity_same_as[0]->ID );

        // check that the post relates to the entity.
        $related_entities = get_post_meta( $post_id, 'wordlift_related_entities', true );
        $this->assertTrue( in_array( $entity_post_id, $related_entities ) );

        // check that the entity relates to the post.
        $related_posts = get_post_meta( $entity_post_id, 'wordlift_related_posts', true );
        $this->assertTrue( in_array( $post_id, $related_posts ) );

        // TODO: check that the post is created on Redlink.

        // check that the entity is created on Redlink.
        $wp_response = wp_remote_get( $this->entity_uri . '.json' );

        // check that the response is not an error.
        $this->assertFalse( is_wp_error( $wp_response ) );

        // get the graph instance.
        $json  = json_decode( $wp_response['body'] );
        $graph = $json[0]->{'@graph'}[0];

        // check that the id is equal to the entity URI.
        $this->assertEquals( $this->entity_uri, $graph->{'@id'} );

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

