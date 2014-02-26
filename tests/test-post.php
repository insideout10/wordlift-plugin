<?php
require_once 'functions.php';

/**
 * We're going to perform a full-blown test here:
 *  - create a post,
 *  - analyse a post,
 *  - save the entities,
 *  - check that the entities have been created:
 *    -- locally
 *    -- in the cloud
 *  - delete the entities (check deletion)
 *  - delete the post (check deletion)
 */
class PostTest extends WP_UnitTestCase {

    // The filename pointing to the tesst contents.
    const FILENAME = 'post.txt';
    const SLUG     = 'tests-post';
    const TITLE    = 'Test Post';

    // When true, the remote response is saved locally and kept as a mock-up (be aware that the previous mockup is
    // overwritten).
    const SAVE_REMOTE_RESPONSE = false;

    /**
     * Set up the test.
     */
    function setUp() {
        parent::setUp();

        // Set the plugin options.
        update_option( WORDLIFT_OPTIONS, array(
            'application_key' => '7CzNylwicEourMXznPxRVfgeT9XskdLr45d35ad1',
            'user_id'         => 353,
            'dataset_name'    => 'wordlift'
        ) );
    }

    /**
     * Test the plugin configuration.
     */
    function testConfiguration() {
        $this->assertNotNull( wordlift_configuration_application_key() );
        $this->assertNotNull( wordlift_configuration_dataset_id() );
        $this->assertNotNull( wordlift_configuration_user_id() );
        $this->assertEquals( 'en', wordlift_configuration_site_language() );
    }

    /**
     * Test create a post and submit it to Redlink for analysis.
     */
    function testRedlinkAPI() {

        // Create the test post.
        $post_id    = $this->createPost();

        // Send the post for analysis.
        $response   = wl_analyze_post( $post_id );
        $status_code = $response['response']['code'];
        $body       = $response['body'];

        // Check the response.
        $this->assertNotNull( $response );
        $this->assertFalse( is_wp_error( $response ) );
        $this->assertTrue( 200 === $status_code );

        // Save the results to a file.
        if ( SAVE_REMOTE_RESPONSE ) {
            $output   = dirname(__FILE__) . '/' . self::FILENAME . '.json';
            $result   = file_put_contents( $output, $body );
            $this->assertFalse( false === $result );
        }

        // Delete the test post.
        $this->deletePost( $post_id );
    }

    /**
     * Test create a post and parse the results (using a mock-up for test results).
     */
    function testEmbedTextAnnotations() {

        // Create the test post.
        $post_id    = $this->createPost();
        $this->assertTrue( is_numeric( $post_id ) );

        $post       = get_post( $post_id );
        $this->assertNotNull( $post );

        // Get the mock-up response.
        $input    = dirname(__FILE__) . '/' . self::FILENAME . '.json';
        $analysis = file_get_contents( $input );
        $this->assertTrue( false != $analysis );
        $this->assertFalse( empty( $analysis ) );

        // Decode the string response to a JSON.
        $json     = json_decode( $analysis );
        $this->assertTrue( is_object( $json ) );

        // Parse the JSON to get the analysis results.
        $analysis_results = wl_parse_response( $json );
        $this->assertTrue( is_array( $analysis_results ) );

        // Embed the text annotations in the content.
        $content_with_text_annotations  = wl_embed_text_annotations( $analysis_results, $post->post_content );
        $this->assertFalse( empty( $content_with_text_annotations ) );

        // Now embed the entities.
        $content_with_entities = wl_embed_entities( $analysis_results, $content_with_text_annotations );
        $this->assertFalse( empty( $content_with_entities ) );

        // TODO: now post the $content_with_entities to test the procedures to save the entity.

        // Delete the test post.
        $this->deletePost( $post_id );

    }

    /**
     * Create a test post.
     * @return int
     */
    function createPost() {

        // Get the post contents.
        $input    = dirname(__FILE__) . '/' . self::FILENAME;
        $content  = file_get_contents( $input );
        $this->assertTrue( false != $content );

        // Create the post.
        $post_id  = wl_create_post( $content, self::SLUG, self::TITLE );
        $this->assertTrue( is_numeric( $post_id ) );

        return $post_id;
    }

    /**
     * Delete a post.
     * @param $post_id
     */
    function deletePost( $post_id ) {

        // Delete the post.
        $result   = wl_delete_post( $post_id );
        $this->assertTrue( false != $result );

    }

}