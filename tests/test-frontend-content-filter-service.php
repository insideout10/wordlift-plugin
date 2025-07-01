<?php
/**
 * Tests: Content Filter Service Test.
 *
 * Define the test for the {@link Wordlift_Content_Filter_Service}.
 *
 * @since      3.8.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Content_Service;

/**
 * Test the {@link Wordlift_Content_Filter_Service} class.
 *
 * @since      3.8.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group frontend
 */
class Wordlift_Content_Filter_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Content_Filter_Service} instance to test.
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Content_Filter_Service $content_filter_service A {@link Wordlift_Content_Filter_Service} instance.
	 */
	private $content_filter_service;

	/**
	 * Array of synonyms to the simulate entity title.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var array
	 */
	private $synonym_labels;

	/**
	 * The post ID of a simulate entity.
	 * Used to prevent regeneration of the post during tests, or set an explicit one.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var int
	 */
	private $dummy_post_id;

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		$this->entity_service         = Wordlift_Entity_Service::get_instance();
		$this->entity_uri_service     = Wordlift_Entity_Uri_Service::get_instance();
		$this->content_filter_service = Wordlift_Content_Filter_Service::get_instance();

	}

	/**
	 * Test a content without entities.
	 *
	 * @since 3.8.0
	 */
	public function test_content_no_entities() {

		$this->assertEquals( 'no entities in this content', $this->content_filter_service->the_content( 'no entities in this content' ) );

	}

	public function create_entity_with_uri( $url ) {
		$post_id = $this->factory()->post->create( array(
			'post_type' => 'entity'
		) );
		Wordpress_Content_Service::get_instance()
		                         ->set_entity_id( Wordpress_Content_Id::create_post( $post_id ), $url );

		return $post_id;
	}

	/**
	 * Test a content with one entity.
	 *
	 * @since 3.8.0
	 */
	public function test_content_with_entity() {

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'https://data.localdomain.localhost/dataset/' );

		$this->setup_link_options( $entity_url, $entity_link, $entity_title, $entity_id );

		// test with no synonym
		$this->synonym_labels = array();

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="' . $entity_url . '">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = <<<EOF
<a class="wl-entity-page-link" title="$entity_title" href="$entity_link" data-id="$entity_url" >Matt Mullenweg</a> would love to see what we're achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!
EOF;

		// Check that the expected content matches the function output.
		$this->assertNotNull( $this->content_filter_service );

		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

		// test with synonym
		$this->synonym_labels = array( 'Creator of WordPress' );

		Wordlift_Entity_Service::get_instance()->set_alternative_labels( $entity_id, $this->synonym_labels );
		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="' . $entity_url . '">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = <<<EOF
<a class="wl-entity-page-link" title="Creator of WordPress" href="$entity_link" data-id="$entity_url" >Matt Mullenweg</a> would love to see what we're achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!
EOF;

		wp_update_post( array(
			'ID'         => $entity_id,
			'post_title' => 'Matt Mullenweg'
		) );
		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );
	}

	/**
	 * Test a content with an entity marked as `wl-no-link`.
	 *
	 * @since 3.11.0
	 */
	public function test_entity_no_link() {

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-no-link disambiguated wl-person" itemid="https://data.localdomain.localhost/dataset/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content without a link.
		$expected = 'Matt Mullenweg would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Intercept the permalink call to return a fixed link we can test.
	 *
	 * @param $permalink
	 * @param $post
	 * @param $leavename
	 *
	 * @return string
	 * @since 3.8.0
	 *
	 */
	public function post_link( $permalink, $post, $leavename ) {

		return 'http://example.org/link';
	}

	/**
	 * Mock {@link Wordlift_Entity_Service} get_entity_post_by_uri function.
	 *
	 * @param string $uri The post URI.
	 *
	 * @return mixed A fake post instance.
	 * @since 3.8.0
	 *
	 */
	public function get_entity_post_by_uri( $uri ) {

		// to make sure we use only one post per test.
		if ( empty( $this->dummy_post_id ) ) {
			$this->dummy_post_id = $this->factory->post->create( array( 'post_title' => 'Matt Mullenweg' ) );
		}

		return get_post( $this->dummy_post_id );
	}

	/**
	 * Mock {@link Wordlift_Entity_Service} get_alternative_labels function.
	 *
	 * @param int $post_id Post id.
	 *
	 * @return mixed An array  of alternative labels.
	 * @since 3.15.0
	 *
	 */
	public function get_alternative_labels( $post_id ) {
		return $this->synonym_labels;
	}

	/**
	 * Test a the get_link_title method
	 *
	 * @since 3.13.0
	 */
	function test_get_link_title() {

		// Initialize the dummy post
		$this->dummy_post_id = $this->factory->post->create( array( 'post_title' => 'Matt Mullenweg' ) );
		$post_id             = $this->dummy_post_id;

		// No synonym, label same as post title.
		$this->synonym_labels = array();
		$this->assertEquals( '', $this->content_filter_service->get_link_title( $post_id, 'Matt Mullenweg' ) );

		// No synonym, label different from post title.
		$this->synonym_labels = array();

		$this->assertEquals( 'Matt Mullenweg', $this->content_filter_service->get_link_title( $post_id, 'label' ) );

		// Have synonym.
		$this->synonym_labels = array( 'WordPress Creator' );
		$this->entity_service->set_alternative_labels( $this->dummy_post_id, $this->synonym_labels );
		$this->assertEquals( 'WordPress Creator', $this->content_filter_service->get_link_title( $post_id, 'Matt Mullenweg' ) );

		// No synonym, label same as post title with different case.
		$this->synonym_labels = array();
		$this->entity_service->set_alternative_labels( $this->dummy_post_id, array() );
		$this->assertEquals( '', $this->content_filter_service->get_link_title( $post_id, 'matt mullenweg' ) );

		// No synonym, label same as synonym with different case.
		$this->synonym_labels = array( 'Matt Mullenweg' );
		$this->assertEquals( '', $this->content_filter_service->get_link_title( $post_id, 'matt mullenweg' ) );

	}

	/**
	 * Test a content with an entity marked as `wl-no-link`.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_no_link_entity_no_link() {

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( false );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-no-link disambiguated wl-person" itemid="https://data.localdomain.localhost/dataset/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content without a link.
		$expected = 'Matt Mullenweg would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';


		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test a content.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_no_link_entity_not_specified() {

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( false );

		$this->assertFalse( Wordlift_Configuration_Service::get_instance()->is_link_by_default() );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="https://data.localdomain.localhost/dataset/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content without a link.
		$expected = 'Matt Mullenweg would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test a content with an entity marked as `wl-link`.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_no_link_entity_link() {


		Wordlift_Configuration_Service::get_instance()->set_link_by_default( false );

		$entity_url  = Wordlift_Configuration_Service::get_instance()->get_dataset_uri() . "/entity";
		$entity_id   = $this->create_entity_with_uri( $entity_url );
		$entity_link = get_permalink( $entity_id );
		$post_tile   = get_the_title( $entity_id );
		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-link disambiguated wl-person" itemid="' . $entity_url . '">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';


		// The expected content with a link.
		$expected = <<<EOF
<a class="wl-entity-page-link" title="$post_tile" href="$entity_link" data-id="$entity_url" >Matt Mullenweg</a> would love to see what we're achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!
EOF;


		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test a content with an entity marked as `wl-no-link`.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_link_entity_link() {

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( true );

		$this->setup_link_options( $entity_url, $entity_link, $entity_title, $entity_id );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-link disambiguated wl-person" itemid="' . $entity_url . '">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = <<<EOF
<a class="wl-entity-page-link" title="$entity_title" href="$entity_link" data-id="$entity_url" >Matt Mullenweg</a> would love to see what we're achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!
EOF;

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test a content.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_link_entity_not_specified() {

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( true );

		$this->setup_link_options( $entity_url, $entity_link, $entity_title, $entity_id );
		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="' . $entity_url . '">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = <<<EOF
<a class="wl-entity-page-link" title="$entity_title" href="$entity_link" data-id="$entity_url" >Matt Mullenweg</a> would love to see what we're achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!
EOF;

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test a content with an entity marked as `wl-no-link`.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_link_entity_no_link() {

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( true );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-no-link disambiguated wl-person" itemid="https://data.localdomain.localhost/dataset/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content without a link.
		$expected = 'Matt Mullenweg would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Override the {@link Wordlift_Entity_Service} method.
	 *
	 * @param array $uris An array of URIs.
	 *
	 * @since 3.17.0
	 *
	 */
	public function preload_uris( $uris ) {

		$this->entity_uri_service->preload_uris( $uris );

	}

	/**
	 * Reset the URIs.
	 *
	 * @since 3.17.0
	 */
	public function reset_uris() {

		$this->entity_uri_service->reset_uris();

	}

	public function get_uri( $post_id ) {

		$post = get_post( $post_id );

		return 'http://example.org/' . $post->post_name;

	}

	/**
	 * @param $entity_url
	 * @param $entity_link
	 * @param $entity_title
	 */
	private function setup_link_options( &$entity_url, &$entity_link, &$entity_title, &$entity_id ) {
		$entity_url   = untrailingslashit( Wordlift_Configuration_Service::get_instance()->get_dataset_uri() ) . "/entity";
		$entity_id    = $this->create_entity_with_uri( $entity_url );
		$entity_link  = get_permalink( $entity_id );
		$entity_title = get_the_title( $entity_id );
	}

}
