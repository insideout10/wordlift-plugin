<?php

/**
 * Test the Wordlift_Page_Service.
 *
 * @since 3.5.3
 */
class Wordlift_Page_Service_Test extends WP_UnitTestCase {

	/**
	 * Test the page service.
	 */
	public function test() {

		// Create a page service instance.
		$page_service = new Wordlift_Page_Service();

		// The test is going to start buffering the response (altered by the Page Service). We feed the buffer with a
		// sample page output (comprising the article and h1 tags), and let the page service do its work.
		// Then we get the output buffer content and check that it matches the expected output.

		ob_start();

		// Start buffering.
		$page_service->wp_head();

		// Echo the article.
		echo( <<<EOF
	<article class="type-post">
		<h1 class="entry-title">Post Title</h1>
	</article>
EOF
		);

		// Flush the buffer.
		$page_service->wp_footer();

		// Check that the output buffer matches the expected result, by getting the buffer contents.
		$this->assertEquals( <<<EOF
	<article itemscope itemtype="http://schema.org/Article" class="type-post">
		<h1 itemprop="name" class="entry-title">Post Title</h1>
	</article>
EOF
			, ob_get_contents() );

		// Discard the buffer.
		ob_end_clean();

	}

	/**
	 * Test that the schema:Article markup is added only on posts and pages.
	 */
	public function testOnlyPostsAndPages() {

		// @todo

	}

	/**
	 * Test that we don't add our own markup if it's already there.
	 */
	public function testDontAddMarkupIfPresentAlready() {

		// @todo

	}

}
