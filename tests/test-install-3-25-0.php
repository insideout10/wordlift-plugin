<?php
/**
 * Tests: Install 3.25.0 Test.
 *
 * Test the {@link 3_25_0} class.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Install_3_25_0_Test class.
 *
 * @since 3.25.0
 */
class Wordlift_Install_3_25_0_Test extends Wordlift_Unit_Test_Case {
	/**
	 * Test to check whether we can create mappings table.
	 */
	public function test_can_create_mappings_table() {
		global $wpdb;
		Wordlift_Install_3_25_0::create_mappings_table();
		$expected_table_name = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$query               = $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $expected_table_name ));
		$table_name          = $wpdb->get_var( $query );
		// When the table is created it should match the expected table name.
		$this->assertEquals( $expected_table_name, $table_name );
	}

}
