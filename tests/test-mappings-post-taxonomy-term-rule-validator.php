<?php

use Wordlift\Mappings\Validators\Rule_Validator;
use Wordlift\Mappings\Validators\Post_Taxonomy_Term_Rule_Validator;
use Wordlift\Mappings\Mappings_REST_Controller;
use Wordlift\Mappings\Jsonld_Converter;

/**
 * This test checks if the post taxonomy term validator is correct.
 * @group mappings
 */
class Post_Taxonomy_Term_Rule_Validator_Test extends Wordlift_Unit_Test_Case {

    /**
     * Our expected route for rest api.
     */
    protected $mapping_route = '/wordlift/v1/mappings/get_taxonomy_terms';

	/**
	 * @var Taxonomy_Term_Rule_Validator
	 */
	private $instance;

	public function setUp() {
		$this->instance = new Post_Taxonomy_Term_Rule_Validator();

        $this->rest_instance = new Mappings_REST_Controller();
        $this->rest_instance->register_routes();
        global $wp_rest_server, $wpdb;

        $wp_rest_server = new WP_REST_Server();
        $this->server   = $wp_rest_server;
        $this->wpdb = $wpdb;
        do_action( 'rest_api_init' );
	}

    /**
     * Test is rest route exists for fetching post taxonomy & terms.
     */
    public function test_rest_route_for_fetching_post_taxonomy_terms() {
        $routes = $this->server->get_routes();
        $this->assertArrayHasKey( $this->route, $routes );
    }

    public function test_when_not_equal_to_operator_on_non_term_page_should_return_false() {
        $result = $this->instance->is_valid( null,
            Rule_Validator::IS_NOT_EQUAL_TO,
            'post_taxonomy',
            'category',
            Jsonld_Converter::POST
        );
        $this->assertFalse( $result );
    }

    public function test_when_custom_post_type_rule_given_should_return_false() {
        register_post_type( 'foo', null );
        $post_id                  = $this->factory()->post->create(array('post_type' => 'foo'));
		$result                   = $this->instance->is_valid( $post_id,
			Rule_Validator::IS_EQUAL_TO,
			'post_taxonomy',
			'category',
			Jsonld_Converter::POST
		);
		$this->assertFalse( $result );
	}

    public function test_when_equal_to_post_taxonomy_rule_given_should_return_true() {
        register_taxonomy( 'foo', null );
        $term                     = wp_create_term(
            'bar',
            'foo'
        );
        $term_id                  = $term['term_id'];
        $post_id                  = $this->factory()->post->create();
        wp_set_post_terms( $post_id, array( $term_id ), 'foo' );

        $result                   = $this->instance->is_valid( $post_id,
            Rule_Validator::IS_EQUAL_TO,
            'taxonomy',
            'bar',
            Jsonld_Converter::POST
        );
        $this->assertTrue( $result );
    }

    public function test_when_not_equal_to_post_taxonomy_rule_given_should_return_false() {
        $post_id                  = $this->factory()->post->create();
        $result                   = $this->instance->is_valid( $post_id,
            Rule_Validator::IS_NOT_EQUAL_TO,
            'post_taxonomy',
            'category',
            Jsonld_Converter::POST
        );
        $this->assertFalse( $result );
    }

    public function test_when_equal_to_operator_on_term_page_should_return_false() {
        global $wp_query;
        register_taxonomy( 'foo', null );
        $term                     = wp_create_term(
            'bar',
            'foo'
        );
        $term_id                  = $term['term_id'];
        $wp_query->queried_object = get_term( $term_id );
        // The term bar dont belong to category
        $result = $this->instance->is_valid( $term_id,
            Rule_Validator::IS_EQUAL_TO,
            'taxonomy',
            'category',
            Jsonld_Converter::TERM
        );
        $this->assertFalse( $result );
    }

}
