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
	it( 'opens a post edit page and waits for the analysis results', () => {
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
		// Intelligence to help you produce richer content and organize it
		// around your audience.' );  // Set the company name. //
		// browser.setValue( '#tinymce p', 'WordLift brings the power of //
		// Artificial Intelligence to help you produce richer content and //
		// organize it around your audience.' ); browser.frameParent();
		// browser.element( '#publish' ).scroll(); browser.click( '#publish' );

		// Open a post page.
		browser.url( '/wp-admin/post.php?post=3&action=edit' );
		browser.pause( 2500 );

		// Wait for the analysis results to load.
		browser.waitForExist( '#wl-entity-list ul li' );

		// Click on the first analysis result.
		browser.click( '#wl-entity-list ul li:nth-child(1) > div:nth-child(1)' );
		browser.pause( 1000 );

		// Open the drawer.
		browser.click( '#wl-entity-list ul li:nth-child(1) > div:nth-child(3)' );
		browser.pause( 1000 );

		// Disable link.
		browser.click( '#wl-entity-list ul li:nth-child(1) > div:nth-child(2) > div:nth-child(1)' );
		browser.pause( 1000 );

		// Re-enable link.
		browser.click( '#wl-entity-list ul li:nth-child(1) > div:nth-child(2) > div:nth-child(1)' );
		browser.pause( 1000 );

		// Click on the second analysis result.
		browser.click( '#wl-entity-list ul li:nth-child(2) > div:nth-child(1)' );
		browser.pause( 2500 );

		// Expect the drawer of the 1st tile to be invisible.
		expect( browser.isVisible( '#wl-entity-list ul li:nth-child(1) > div:nth-child(2)' ) )
			.toBe( false );

		// Expect the drawer of the 2nd tile to be visible.
		expect( browser.isVisible( '#wl-entity-list ul li:nth-child(2) > div:nth-child(2)' ) )
			.toBe( false );
	} );
};

// Finally export the `PostEditPage`.
export default PostEditPage;
