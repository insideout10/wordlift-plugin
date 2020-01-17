<?php

/**
 * Tests: Post Excerpt Helper Test
 *
 * Test the {@link Wordlift_Post_Excerpt_Helper} class.
 *
 * @since 3.12.0
 */
class Wordlift_Post_Excerpt_Helper_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test a post with an excerpt.
	 */
	function test_post_with_excerpt() {

		$post = $this->factory->post->create_and_get( array(
			'post_excerpt' => 'The standard Lorem Ipsum passage, used since the 1500s',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		) );

		$excerpt = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post );

		$this->assertEquals( 'The standard Lorem Ipsum passage, used since the 1500s', $excerpt );

	}

	/**
	 * Test a post with an excerpt.
	 */
	function test_post_with_excerpt_with_shortcodes_and_html() {

		$post = $this->factory->post->create_and_get( array(
			'post_excerpt' => '<strong>The standard Lorem Ipsum passage</strong>, [wl_navigator] [vc_column_text]used since the 1500s[/vc_column_text].',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		) );

		$excerpt = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post );

		$this->assertEquals( 'The standard Lorem Ipsum passage,  used since the 1500s.', $excerpt );

	}

	/**
	 * Test a post without an excerpt.
	 */
	function test_post_without_excerpt() {

		$post = $this->factory->post->create_and_get( array(
			// The following line is required, because WP's Post Factory will
			// generate a custom excerpt if none is set, while we want to test
			// a real case scenario where the user doesn't set the excerpt.
			'post_excerpt' => '',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		) );

		$excerpt = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post );

		$this->assertEquals( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat...', $excerpt );

	}

	/**
	 * Test a post without an excerpt. The post content contains shortcode and
	 * html code.
	 */
	function test_post_without_excerpt_shortcodes_and_html() {

		$post = $this->factory->post->create_and_get( array(
			// The following line is required, because WP's Post Factory will
			// generate a custom excerpt if none is set, while we want to test
			// a real case scenario where the user doesn't set the excerpt.
			'post_excerpt' => '',
			'post_content' => '<p>Lorem ipsum dolor sit amet, <em>consectetur adipiscing elit</em>, [gallery] sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [wl_navigator] Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. <strong>Excepteur</strong> sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
		) );

		$excerpt = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post );

		$this->assertEquals( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat...', $excerpt );

	}

	/**
	 * Test a post without an excerpt. The post content is larger than the requested
	 * words' length.
	 */
	function test_post_without_excerpt_shorter_than_caller_length() {

		$post = $this->factory->post->create_and_get( array(
			// The following line is required, because WP's Post Factory will
			// generate a custom excerpt if none is set, while we want to test
			// a real case scenario where the user doesn't set the excerpt.
			'post_excerpt' => '',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
		) );

		$excerpt = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post );

		$this->assertEquals( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', $excerpt );

	}

	/**
	 * Test a post without an excerpt. The post content is larger than the requested
	 * words' length.
	 */
	function test_post_without_excerpt_larger_than_caller_length() {

		$post = $this->factory->post->create_and_get( array(
			// The following line is required, because WP's Post Factory will
			// generate a custom excerpt if none is set, while we want to test
			// a real case scenario where the user doesn't set the excerpt.
			'post_excerpt' => '',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
		) );

		$excerpt = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post, 1 );

		$this->assertEquals( 'Lorem...', $excerpt );

	}

	/**
	 * Test a post without an excerpt with a custom more.
	 */
	function test_post_without_excerpt_larger_custom_more() {

		$post = $this->factory->post->create_and_get( array(
			// The following line is required, because WP's Post Factory will
			// generate a custom excerpt if none is set, while we want to test
			// a real case scenario where the user doesn't set the excerpt.
			'post_excerpt' => '',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
		) );

		$excerpt = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post, 1, ' (continues)' );

		$this->assertEquals( 'Lorem (continues)', $excerpt );

	}

}
