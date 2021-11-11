<?php

/**
 * Unit test cases for the code added to incorporate feature request 1496
 * @since 3.30.0
 * @group webhooks
 * @author
 */

 use Wordlift\Webhooks\Webhooks_Loader;
 use Wordlift\Webhooks\Api\Rest_Controller;
 use Wordlift\Webhooks\Tabs\Settings_Tab;

class Webhooks_Settings_Tab_Test extends Wordlift_Unit_Test_Case {

    private $loader;
    private $settings_tab;
	private $settings_page;
	private $rest_controller_instance;

	public function setUp() {
		parent::setUp();
		if ( ! taxonomy_exists( 'post_tag' ) ) {
			register_taxonomy( 'post_tag', 'post' );
		}
		// Reset all global filters.
		global $wp_filter, $wp_scripts, $wp_styles;
		$wp_filter  = array();
		$wp_scripts = null;
		$wp_styles  = null;
		$this->loader     = new Webhooks_Loader();
		$this->rest_controller_instance = $this->loader->get_rest_controller_object();
        $this->settings_tab = $this->loader->init_all_dependencies();
		update_option( 'wl_webhook_url',
		            array( 'https://ensh04p5m7cs4hw.m.pipedream.net' ),
		            'no'
		            );

	}

	function tearDown() {
		parent::tearDown();
	}

    /**
    * Test the functions in the Webhooks_Loader class
    */

	public function test_loader_methods() {
	    $this->assertSame( 'webhooksobject', $this->loader->get_feature_slug() );
	    $this->assertSame( false, $this->loader->get_feature_default_value() );
	    $this->assertEquals( new Rest_Controller(), $this->rest_controller_instance );
	}

    /**
    * Test cases to cover hooks registered when Rest_Controller class is instantiated through Webhooks_Loader's
    * rest_controller_instance method
    */

    public function test_wl_admin_register_setting(){
	    $this->assertTrue( is_numeric( has_action( 'wl_sync__sync_many', array( $this->rest_controller_instance, 'register_sync_many' ) ) ) );
	    $this->assertTrue( is_numeric( has_action( 'wl_sync__delete_one', array( $this->rest_controller_instance, 'register_sync_delete' ) ) ) );
    }

    /**
    * Test cases to cover events and objects createed on instantiating Settings_Tab class
    * through Webhooks_Loader's init_all_dependencies method
    */

	public function test_should_have_admin_tab_registered_for_match_terms() {
        $this->assertTrue( is_numeric( has_action( 'admin_init', array( $this->settings_tab, 'wl_admin_register_setting' ) ) ) );
		$tabs = apply_filters( 'wl_admin_page_tabs', array() );
		$this->assertCount( 1, $tabs );
		$match_terms_tab = $tabs[0];
		$this->assertArrayHasKey( 'slug', $match_terms_tab );
		$this->assertArrayHasKey( 'title', $match_terms_tab );
		$this->assertEquals( 'webhooksobject-settings', $match_terms_tab['slug'] );
		$this->assertEquals( 'Webhooks Settings', $match_terms_tab['title'] );
		//$post_id = self::factory()->post->create();
		//echo $post_id;
	}

    /**
    * Test cases to cover various scenarios arising from uri entry through the webhooks setting tab
    */

	function test_sanitize_callback() {

        $uri = 'http://data-dev.wordlift.io/wl040/page/privacy_policy';
        $incorrect_uri = "Test123";

        $expected_uris = array(
            'https://ensh04p5m7cs4hw.m.pipedream.net',
            $uri
        );
       $expected_uris_incorrect_entry = array( 'https://ensh04p5m7cs4hw.m.pipedream.net' );

        $this->assertEquals( $expected_uris, $this->settings_tab->sanitize_callback( $uri ) );
        $this->assertEquals(
            $expected_uris_incorrect_entry,
            $this->settings_tab->sanitize_callback( $incorrect_uri )
        );
	}
}