<?php
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Widgets\Navigator\Filler_Posts\Filler_Posts_Util;

/**
 * if post type is post:
 * Latest posts with same category as subject post;
 * If does not fill, latest posts (of post type post);
 * if does not fill, latest any posts;
 *
 * if any other post type:
 * Latest posts of same post type as subject post
 * If does not fill, latest any posts
 **/
class Widget_Filler_Posts_Test extends Wordlift_Unit_Test_Case {


	private function test_create_post_with_category( $category, $post_type = 'post' ) {

		$post_id = $this->create_post_with_thumbnail( $post_type );

		if ( ! category_exists( $category ) ) {
			wp_create_category( $category );
		}
		$category = get_category_by_slug( $category );
		wp_set_post_categories( $post_id, array( $category->term_id ) );

		return $post_id;
	}


	private function extract_post_ids_and_sort( $posts ) {
		$post_ids = array_map( function ( $post ) {
			return $post->ID;
		}, $posts );
		sort( $post_ids );

		return $post_ids;
	}

	public function test_when_post_type_is_post_should_get_latest_posts_with_same_category() {
		$subject_post = $this->test_create_post_with_category( 'test_category' );

		// Lets create 4 posts in the same category, it should be returned.
		$post_1 = $this->test_create_post_with_category( 'test_category' );
		$post_2 = $this->test_create_post_with_category( 'test_category' );
		$post_3 = $this->test_create_post_with_category( 'test_category' );
		$post_4 = $this->test_create_post_with_category( 'test_category' );

		$post_ids = array( $post_1, $post_2, $post_3, $post_4 );
		sort( $post_ids );
		$filler_posts_util = new Filler_Posts_Util( $subject_post );
		$filler_posts      = $filler_posts_util->get_filler_posts( 4, array( $subject_post ) );
		$returned_post_ids = $this->extract_post_ids_and_sort( $filler_posts );
		$this->assertEquals( $post_ids, $returned_post_ids );
	}


	public function test_post_type_is_post_but_same_category_posts_are_not_present_should_retrieve_latest_posts_of_same_post_type() {
		$subject_post = $this->test_create_post_with_category( 'test_category' );
		/**
		 * Lets create posts with post type 'post', but not on same category.
		 */
		$post_1 = $this->create_post_with_thumbnail();
		$post_2 = $this->create_post_with_thumbnail();
		$post_3 = $this->create_post_with_thumbnail();
		$post_4 = $this->create_post_with_thumbnail();

		$post_ids = array( $post_1, $post_2, $post_3, $post_4 );
		sort( $post_ids );
		$filler_posts_util = new Filler_Posts_Util( $subject_post );
		$filler_posts      = $filler_posts_util->get_filler_posts( 4, array( $subject_post ) );
		$returned_post_ids = $this->extract_post_ids_and_sort( $filler_posts );
		$this->assertEquals( $post_ids, $returned_post_ids );
	}

	public function test_when_post_type_is_not_post_should_return_latest_posts_from_same_post_type() {
		// Here the post type is set to page
		$subject_post = $this->test_create_post_with_category( 'test_category', 'page' );



		// Now we will create 4 posts on the page post type.
		$post_1 = $this->create_post_with_thumbnail('page');
		$post_2 = $this->create_post_with_thumbnail('page');
		$post_3 = $this->create_post_with_thumbnail('page');
		$post_4 = $this->create_post_with_thumbnail('page');

		// And 2 posts on `post` post type ( these posts should not be in the result )
		$post_5 = $this->test_create_post_with_category('test_category');
		$post_6 = $this->test_create_post_with_category('test_category');

		$post_ids = array( $post_1, $post_2, $post_3, $post_4 );
		sort( $post_ids );
		$filler_posts_util = new Filler_Posts_Util( $subject_post );
		$filler_posts      = $filler_posts_util->get_filler_posts( 4, array( $subject_post ) );
		$returned_post_ids = $this->extract_post_ids_and_sort( $filler_posts );
		$this->assertEquals( $post_ids, $returned_post_ids );


	}

	/**
	 * @param $post_type
	 *
	 * @return mixed
	 */
	private function create_post_with_thumbnail( $post_type = 'post' ) {
		$post_id = $this->factory()->post->create( array( 'post_type' => $post_type ) );
		update_post_meta( $post_id, '_thumbnail_id', 'https://some-url-from-test.com' );

		return $post_id;
	}

}
