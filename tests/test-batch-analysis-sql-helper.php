<?php
/**
 * Tests: Batch Analysis Sql Helper Test.
 *
 * @since      3.17.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Test the {@link Wordlift_Batch_Analysis_Sql_Helper} class.
 *
 * @since 3.17.0
 */
class Wordlift_Batch_Analysis_Sql_Helper_Test extends Wordlift_Unit_Test_Case {

	public function test_one_post_type() {

		// 1 post type.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array( 'post_type' => 'post' ) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );
	}

	public function test_two_post_types() {
		// 2 post types.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'post_type' => array(
				'post',
				'page',
			),
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post', 'page')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );
	}

	public function test_include_annotated_true() {

		// Include annotated: true.
		// 2 post types.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'include_annotated' => true,
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish'",
			$sql );

	}

	public function test_include_annotated_yes() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'include_annotated' => 'yes',
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );

	}

	public function test_include_annotated_false() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'include_annotated' => false,
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );

	}

	public function test_date_from_valid() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'from' => '2018-01-01T00:00:00+00:00',
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">' AND p.post_date_gmt >= '2018-01-01 00:00:00'",
			$sql );

	}

	public function test_date_from_valid_with_timezone_conversion() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'from' => '2018-01-01T00:00:00+02:00',
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">' AND p.post_date_gmt >= '2017-12-31 22:00:00'",
			$sql );

	}

	public function test_date_to_valid() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'to' => '2018-01-01T00:00:00+00:00',
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">' AND p.post_date_gmt <= '2018-01-01 00:00:00'",
			$sql );

	}

	public function test_date_to_valid_with_timezone_conversion() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'to' => '2018-01-01T00:00:00+02:00',
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">' AND p.post_date_gmt <= '2017-12-31 22:00:00'",
			$sql );

	}

	public function test_include_posts_null() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'include' => null,
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );

	}

	public function test_include_posts_empty_array() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'include' => array(),
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );

	}

	public function test_include_posts_one_post() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql_for_ids( self::get_args( array(
			'ids' => 1,
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.id IN (1)",
			$sql );

	}

	public function test_include_posts_two_posts() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql_for_ids( self::get_args( array(
			'ids' => array( 1, 2 ),
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.id IN (1, 2)",
			$sql );

	}

	public function test_exclude_posts_null() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'exclude' => null,
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );

	}

	public function test_exclude_posts_empty_array() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'exclude' => array(),
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">'",
			$sql );

	}

	public function test_exclude_posts_one_post() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'exclude' => 1,
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">' AND p.ID NOT IN ( 1 )",
			$sql );

	}

	public function test_exclude_posts_two_posts() {

		// Include annotated: false.
		$sql = Wordlift_Batch_Analysis_Sql_Helper::get_sql( self::get_args( array(
			'exclude' => array( 1, 2 ),
		) ) );

		$this->assertEquals( "SELECT p.ID FROM wptests_posts p WHERE p.post_type IN ('post')  AND p.post_status = 'publish' AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">' AND p.ID NOT IN ( 1,2 )",
			$sql );

	}

	private static function get_args( $args ) {

		return wp_parse_args( $args, array(
			'post_type'         => 'post',
			'links'             => 'default',
			'min_occurrences'   => 1,
			'include_annotated' => false,
			'from'              => null,
			'to'                => null,
			'include'           => array(),
			'exclude'           => array(),
		) );

	}

}
