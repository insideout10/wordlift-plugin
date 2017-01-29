<?php
/**
 * Test for the Prefixes module.
 */

require_once( 'functions.php' );

/**
 * Class PrefixesTest
 */
class PrefixesTest extends Wordlift_Unit_Test_Case {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		$this->turn_off_entity_push();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

		// Empty the blog.
		wl_empty_blog();
	}

	/**
	 * Test that the default prefixes are loaded in WL.
	 */
	function test_prefixes_defaults() {

		$items = array(
			array( 'prefix'    => 'geo',
			       'namespace' => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
			),
			array( 'prefix'    => 'dct',
			       'namespace' => 'http://purl.org/dc/terms/',
			),
			array( 'prefix'    => 'rdfs',
			       'namespace' => 'http://www.w3.org/2000/01/rdf-schema#',
			),
			array( 'prefix'    => 'owl',
			       'namespace' => 'http://www.w3.org/2002/07/owl#',
			),
			array( 'prefix' => 'schema', 'namespace' => 'http://schema.org/' ),
		);

		foreach ( $items as $item ) {
			$this->assertEquals( $item['namespace'], wl_prefixes_get( $item['prefix'] ) );
		}

	}

	/**
	 * Test adding a new prefix.
	 */
	function test_prefixes_add_and_delete() {

		// Create a random prefix and an example namespace.
		$prefix    = uniqid( 'prefix' );
		$namespace = 'http://example.org/ns/';

		// Check that the prefix doesn't exist yet.
		$this->assertFalse( wl_prefixes_get( $prefix ) );

		// Add the prefix.
		wl_prefixes_add( $prefix, $namespace );

		$path = uniqid( 'this/is/a/test/' );

		// Compact a URL.
		$url_to_compact = $namespace . $path;
		$url_compacted  = wl_prefixes_compact( $url_to_compact );
		$this->assertEquals( $prefix . ':' . $path, $url_compacted );

		// Expand a URL.
		$url_to_expand = $url_compacted;
		$url_expanded  = wl_prefixes_expand( $url_to_expand );
		$this->assertEquals( $namespace . $path, $url_expanded );

		// Check the namespace.
		$this->assertEquals( $namespace, wl_prefixes_get( $prefix ) );

		// Now delete the prefix.
		wl_prefixes_delete( $prefix );

		// Check that the prefix doesn't exist.
		$this->assertFalse( wl_prefixes_get( $prefix ) );

	}


	/**
	 * Test the Prefixes list table.
	 */
	function test_prefixes_list_table() {

		$GLOBALS['hook_suffix'] = 'test';

		$prefixes_list_table = new WL_Prefixes_List_Table();

		// Test the columns.
		$columns = $prefixes_list_table->get_columns();
		$this->assertTrue( is_array( $columns ) );
		$this->assertCount( 2, $columns );

		// Check that the prepare items returns the same number of items in the prefixes.
		$items = wl_prefixes_list();
		$this->assertGreaterThan( 0, sizeof( $items ) );
		$prefixes_list_table->prepare_items();
		$this->assertCount( sizeof( $items ), $prefixes_list_table->items );

		// Check that we get a return value for each column.
		foreach ( $prefixes_list_table->items as $item ) {
			foreach ( $columns as $key => $value ) {
				$this->assertEquals( $item[ $key ], $prefixes_list_table->column_default( $item, $key ) );
			}
		}

	}

}
