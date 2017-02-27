/**
 * Tests: Post Edit Page.
 *
 * Test the analysis.
 *
 * @since 3.11.0
 */

/**
 * Define the `PostEditPage` test.
 *
 * @since 3.11.0
 * @constructor
 */
const PostEditPage = function() {
	// @todo: enable creating a post when Safari and FF will support it.
	//	browser.waitForExist( '#menu-posts > a[href="edit.php"]' );
	//
	//	browser.click( '#menu-posts > a[href="edit.php"]' );
	//
	//	browser.waitForExist( 'a.page-title-action' );
	//
	//	browser.click( 'a.page-title-action' );
	//
	//	browser.waitForExist( '#content_ifr' );
	//
	//	browser.pause(5000);
	//
	//	browser.frame( browser.element( '#content_ifr' ).value );
	//
	//	browser.waitForExist( '#tinymce' );
	//
	//	browser.click( '#tinymce' );
	//
	//	browser.keys( 'WordLift brings the power of Artificial
	// Intelligence to help you produce richer content and organize it around
	// your audience.' );  // Set the company name. // browser.setValue(
	// '#tinymce p', 'WordLift brings the power of // Artiﬁcial Intelligence to
	// help you produce richer content and // organize it around your
	// audience.' ); browser.frameParent();  browser.element( '#publish'
	// ).scroll(); browser.click( '#publish' );

	// Open a post page.
	browser.url( '/wp-admin/post.php?post=3&action=edit' );

	// Wait for the analysis results to load.
	browser.waitForExist( '#wl-entity-list ul li' );

	// Click on the first analysis result.
	browser.click( '#wl-entity-list ul li' );
};

// Finally export the `PostEditPage`.
export default PostEditPage;
