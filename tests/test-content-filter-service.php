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

/**
 * Test the {@link Wordlift_Content_Filter_Service} class.
 *
 * @since      3.8.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
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

		$wordlift_test                = $this->get_wordlift_test();
		$this->entity_service         = $wordlift_test->get_entity_service();
		$this->entity_uri_service     = $wordlift_test->get_entity_uri_service();
		$this->content_filter_service = new Wordlift_Content_Filter_Service( $this, $this->configuration_service, $this->entity_uri_service );

	}

	/**
	 * Test a content without entities.
	 *
	 * @since 3.8.0
	 */
	public function test_content_no_entities() {

		$this->assertEquals( 'no entities in this content', $this->content_filter_service->the_content( 'no entities in this content' ) );

	}

	/**
	 * Test a content with one entity.
	 *
	 * @since 3.8.0
	 */
	public function test_content_with_entity() {

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// test with no synonym
		$this->synonym_labels = array();

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = '<a class=\'wl-entity-page-link\'  href=\'http://example.org/link\'>Matt Mullenweg</a> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// Check that the expected content matches the function output.
		$this->assertNotNull( $this->content_filter_service );
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

		// test with synonym
		$this->synonym_labels = array( 'Creator of WordPress' );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = '<a class=\'wl-entity-page-link\' title="Creator of WordPress" href=\'http://example.org/link\'>Matt Mullenweg</a> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

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
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-no-link disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

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
		$this->assertEquals( 'WordPress Creator', $this->content_filter_service->get_link_title( $post_id, 'Matt Mullenweg' ) );

		// No synonym, label same as post title with different case.
		$this->synonym_labels = array();
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

		$this->configuration_service->set_link_by_default( false );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-no-link disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

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

		$this->configuration_service->set_link_by_default( false );

		$this->assertFalse( $this->configuration_service->is_link_by_default() );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

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

		$this->configuration_service->set_link_by_default( false );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-link disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = '<a class=\'wl-entity-page-link\'  href=\'http://example.org/link\'>Matt Mullenweg</a> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test a content with an entity marked as `wl-no-link`.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_link_entity_link() {

		$this->configuration_service->set_link_by_default( true );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-link disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = '<a class=\'wl-entity-page-link\'  href=\'http://example.org/link\'>Matt Mullenweg</a> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test a content.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_link_entity_not_specified() {

		$this->configuration_service->set_link_by_default( true );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// The expected content with a link.
		$expected = '<a class=\'wl-entity-page-link\'  href=\'http://example.org/link\'>Matt Mullenweg</a> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

		// Check that the expected content matches the function output.
		$this->assertEquals( $expected, $this->content_filter_service->the_content( $content ) );

	}

	/**
	 * Test if the highlighting done by FAQ is removed.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_question_highlighting_tag_removed() {
		$src = <<<EOF
			<p><span class="wl-faq--question" id="wl-faq--question--3164315662">this is a question?</span></p>
EOF;
		$expected_output = <<<EOF
			<p>this is a question?</p>
EOF;
		$result = $this->content_filter_service->the_content($src);
		$this->assertEquals($expected_output, $result);
	}

	/**
	 * Test if the highlighting done by FAQ is removed.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_answer_highlighting_tag_removed() {
		$src = <<<EOF
			<p><span class="wl-faq--answer" id="wl-faq--answer--3164315662">this is a answer</span></p>
EOF;
		$expected_output = <<<EOF
			<p>this is a answer</p>
EOF;
		$result = $this->content_filter_service->the_content($src);
		$this->assertEquals($expected_output, $result);
	}

	/**
	 * Testing if the nested tags inside the span should not affect the outside regex.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_answer_highlighting_nested_tags_removed_correctly() {
		$src = <<<EOF
			<p><span class="wl-faq--answer" id="wl-faq--answer--3164315662">this<span class="foo">is</span> a answer</span></p>
EOF;
		$expected_output = <<<EOF
			<p>this<span class="foo">is</span> a answer</p>
EOF;
		$result = $this->content_filter_service->the_content($src);
		$this->assertEquals($expected_output, $result);
	}

	/**
	 * Testing if the nested tags inside the span should not affect the outside regex.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_question_highlighting_nested_tags_removed_correctly() {
		$src = <<<EOF
			<p><span class="wl-faq--question" id="wl-faq--question--3164315662">this<span class="foo">is</span> a question?</span></p>
EOF;
		$expected_output = <<<EOF
			<p>this<span class="foo">is</span> a question?</p>
EOF;
		$result = $this->content_filter_service->the_content($src);
		$this->assertEquals($expected_output, $result);
	}


	/**
	 * Test a content with an entity marked as `wl-no-link`.
	 *
	 * @since 3.13.0
	 */
	public function test_entity_default_link_entity_no_link() {

		$this->configuration_service->set_link_by_default( true );

		// Add a filter to set the permalink to a fixed value we can test.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 3 );

		// The content.
		$content = '<span id="urn:enhancement-4b54b56d-7142-5dd3-adc6-27e51c70fdad" class="textannotation wl-no-link disambiguated wl-person" itemid="http://data.example.org/entity">Matt Mullenweg</span> would love to see what we\'re achieving with WordLift for <span id="urn:enhancement-7aa39603-d48f-8ac8-5437-c74b3b0e28ef" class="textannotation">WordPress</span>!';

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

}
