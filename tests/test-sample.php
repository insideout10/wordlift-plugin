<?php

class SampleTest extends WP_UnitTestCase {

    // the configuration parameters for WordLift.
    private $user_id         = 353;
    private $dataset_name    = 'wordlift';
    private $application_key = '5VnRvvkRyWCN5IWUPhrH7ahXfGCBV8N0197dbccf';

    // sample entity data.
    // the dbpedia URI.
    private $entity_same_as = 'http://dbpedia.org/resource/Colorado';
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

	function testSample() {

        $content = <<<EOF
The shift by Mr. Cuomo, a Democrat who had long resisted legalizing medical marijuana, comes as other states are taking increasingly liberal positions on it — most notably <span class="textannotation place disambiguated" id="urn:enhancement-ba2ace0d-2e21-ae84-3a7c-db37206be078" itemid="http://dbpedia.org/resource/Colorado" itemscope="itemscope" itemtype="http://schema.org/Place"><span itemprop="name">Colorado</span></span>, where thousands have flocked to buy the drug for recreational use since it became legal on Jan. 1.
EOF;

        $post_id = wp_insert_post( array(
            'post_type'    => 'post',
            'post_content' => $content,
            'post_title'   => 'Hello world!'
        ) );


        // try to get the post using the WordLift method.
        $posts = wordlift_get_entity_posts_by_uri($this->entity_uri);

        $this->assertCount(1, $posts);

	}
}

