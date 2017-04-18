/**
 * Opens the 'Lorem Ipsum' post and waits for the Navigator to load.
 *
 * @since 3.12.0
 * @constructor
 */
const OpenPostLoremIpsum = function() {
	it( 'opens the Lorem Ipsum post', function() {
		browser.url( '/lorem-ipsum' );
		browser.waitForExist( '[data-wl-navigator] > ul' );
		browser.scroll( '[data-wl-navigator]', 0, 100 );
		browser.pause( 3000 );
	} );
};

// Finally export the function.
export default OpenPostLoremIpsum;
