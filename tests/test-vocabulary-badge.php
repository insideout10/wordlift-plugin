<?php

use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Term_Count\Cached_Term_Count;
use Wordlift\Vocabulary\Data\Term_Count\Term_Count_Factory;
use Wordlift\Vocabulary\Menu\Badge\Badge_Generator;
use Wordlift\Vocabulary\Vocabulary_Loader;

/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Badge_Test extends \Wordlift_Vocabulary_Unit_Test_Case {


	public function test_round_to_nearest_hundred_for_term_count() {

		$result = Badge_Generator::round_to_nearest_hundred( 340 );
		$this->assertEquals( 300, $result );
		$result = Badge_Generator::round_to_nearest_hundred( 290 );
		$this->assertEquals( 200, $result );
		$result = Badge_Generator::round_to_nearest_hundred( 200 );
		$this->assertEquals( 200, $result );
		$result = Badge_Generator::round_to_nearest_hundred( 70 );
		$this->assertEquals( 70, $result );

	}


	public function test_badge_generated_html() {
		$result        = Badge_Generator::generate_html( 340 );
		$expected_html = "<span class=\"wl-admin-menu-badge\">300+</span>";
		$this->assertEquals( $result, $expected_html );
	}

	public function test_badge_generated_html_for_numbers_less_than_100_should_return_without_plus_sign() {
		$result        = Badge_Generator::generate_html( 70 );
		$expected_html = "<span class=\"wl-admin-menu-badge\">70</span>";
		$this->assertEquals( $result, $expected_html );
	}


	public function test_term_count_should_be_cached() {
		$tag_1 = $this->create_tag("foo1");
		$tag_2 = $this->create_tag("bar1");
		$tag_3 = $this->create_tag("foo2");
		$tag_4 = $this->create_tag("bar2");
		$tag_5 = $this->create_tag("foo3");

		// make $tag_1, $tag_2, $tag_3 tags not supported.
		update_term_meta($tag_1, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 1);
		update_term_meta($tag_3, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 1);
		// tag_2 should not be returned since they dont have entities exists meta key.

		// make tag_4, tag_5 returned by procedure.
		update_term_meta( $tag_4, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1);
		update_term_meta( $tag_5, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1);

		$this->assertFalse( get_transient( Cached_Term_Count::TRANSIENT_KEY ) );
		// make a call to term count service, we should have transient now.
		$term_count_provider = Term_Count_Factory::get_instance( Term_Count_Factory::CACHED_TERM_COUNT );
		$term_count_provider->get_term_count();
		$this->assertEquals( 2, get_transient( Cached_Term_Count::TRANSIENT_KEY ) );
		// on next call should get the transient, to verify we update transient with different value and expect it
		// to return the updated value.
		set_transient( Cached_Term_Count::TRANSIENT_KEY, 100 );
		$count = $term_count_provider->get_term_count();
		$this->assertEquals( 100, $count, 'Should return count from transient cache');
	}



	public function test_should_generate_correct_html_in_menu() {
		$current_user_id = $this->factory()->user->create( array(
			'role' => 'administrator',
		) );
		wp_set_current_user( $current_user_id );
		$this->create_tags(2);
		global $wp_filter;
		$wp_filter = array();
		$vocabulary_loader = new Vocabulary_Loader();
		$vocabulary_loader->init_vocabulary();
		do_action('admin_menu');
		global $submenu;
		$page_settings = $submenu["wl_admin_menu"];

		$this->assertEquals( "Match Terms <span class=\"wl-admin-menu-badge\">2</span>", $page_settings[0][0]);


	}


}