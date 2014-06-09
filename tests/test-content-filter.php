<?php

/**
 * This file covers tests related to the save-post related routines.
 */

require_once 'functions.php';

class ContentFilterTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        wl_configure_wordpress_test();
        wl_empty_blog();
        rl_empty_dataset();
    }

    function testColorCodingOnFrontEnd() {

        $entity_id  = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
        $entity_uri = wl_get_entity_uri( $entity_id );
        wl_set_entity_main_type( $entity_id, 'http://schema.org/Event' );

//        # Prepare the props array simulating what a $_POST would give us.
//        $props = array(
//            '@id'                                      => array( $entity_uri ),
//            'http://linkedevents.org/ontology/atPlace' => array( 'http://example.org/a-place' ),
//            'http://linkedevents.org/ontology/atTime'  => array( 'http://example.org/a-time' ),
//            'http://schema.org/image'                  => array( 'http://example.org/an-image' ),
//            'http://www.w3.org/2002/12/cal#dtend'      => array( '2014-05-05' ),
//            'http://www.w3.org/2002/12/cal#dtstart'    => array( '2014-05-04' ),
//            'http://www.w3.org/2002/12/cal#uid'        => array( 'fafba0aa-1617-4d18-ba13-081d5965cdf9' )
//        );
//
//        # Save the props.
//        wl_entity_props_save( $entity_uri,  $props );

        # Create a test entity post.
        $content    = <<<EOF
This post is referencing the sample <span id="urn:enhancement-4f0e0fbc-e981-7852-9521-f4718eafa13f" class="textannotation highlight wl-event" itemid="$entity_uri">Entity 1</span>.
EOF;

        $post_id    = wl_create_post( $content, 'post-1', 'Post 1', 'publish', 'post' );
        $post       = get_post( $post_id );

        $this->setColorCode( 'no' );
        $this->assertEquals(
            'This post is referencing the sample <span itemscope itemtype="http://schema.org/Event" itemid="http://data.redlink.io/353/wordlift-tests-php-5-4-wp-3-8-ms-0/entity/Entity_1"><link itemprop="url" href="http://example.org/?post_type=entity&p=' . $entity_id . '" /><span itemprop="name">Entity 1</span></span>.',
            wl_content_embed_item_microdata( $post->post_content, $entity_uri )
        );

        $this->setColorCode( 'yes' );
        $this->assertEquals(
            'This post is referencing the sample <span itemscope itemtype="http://schema.org/Event" class="wl-event" itemid="http://data.redlink.io/353/wordlift-tests-php-5-4-wp-3-8-ms-0/entity/Entity_1"><link itemprop="url" href="http://example.org/?post_type=entity&p=' . $entity_id . '" /><span itemprop="name">Entity 1</span></span>.',
            wl_content_embed_item_microdata( $post->post_content, $entity_uri )
        );

    }

    function setColorCode( $value ) {

        // Set the default as index.
        $options = get_option( WL_OPTIONS_NAME );
        $options[WL_CONFIG_ENABLE_COLOR_CODING_ON_FRONTEND_NAME] = $value;
        update_option( WL_OPTIONS_NAME, $options );
    }

}

