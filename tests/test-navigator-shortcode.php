<?php
/**
 * @since 2.57.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Navigator_Shortcode_Test extends Wordlift_Unit_Test_Case {

	public function test_given_no_sort_param_navigator_should_return_results_in_desc_format() {

		// Create 2 posts and 2 entities
		$post_1_id = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id = wl_create_post( '', 'post2', 'A post', 'publish' );
		$post_3_id = wl_create_post( '', 'post3', 'A post', 'publish' );
		FacetedSearchShortcodeTest::set_post_modified_to_one_year_after( $post_3_id );

		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_3_id, WL_WHAT_RELATION, $entity_1_id );

		$_GET['post_id'] = $post_1_id;
		$_GET['uniqid'] = 'test-navigator-unique-id';
		var_dump(_wl_navigator_get_data());
	}
}