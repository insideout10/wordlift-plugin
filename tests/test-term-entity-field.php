<?php
/**
 * Test the {@link Wordlift_Admin_Term_Adapter}.
 *
 * @since 3.31.3
 * @group term
 */
class Test_Wordlift_Admin_Term_Adapter extends Wordlift_Unit_Test_Case {
	function setUp() {
		parent::setUp();
		require_once __DIR__ . '/../src/admin/class-wordlift-admin-term-adapter.php';
	}


	public function test_when_term_doesnt_have_data_for_term_entity_should_not_show_it() {
		$term_adapter = new Wordlift_Admin_Term_Adapter();
		$term_data = wp_insert_term('wordlift_admin_term_adapter_test_1', 'category' );
		$term = get_term( $term_data['term_id'] );
		ob_start();
		$term_adapter->edit_form_fields( $term, 'category' );
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertSame( '', $output );
	}



}