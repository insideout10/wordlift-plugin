/**
 * Tests: Setup Page.
 *
 * @since 3.11.0
 */

/**
 * Define the `SetupPage` test.
 *
 * @since 3.11.0
 * @constructor
 */
const SetupPage = function() {
	// `paneX` represents the expected horizontal offset of the current
	// pane. It is set the first time, when the _wl-setup_ page is opened.
	let paneX;

	/**
	 * Click on the next button in the pane at `index` (1-based).
	 *
	 * @since 3.9.0
	 *
	 * @param {Number} index The pane index (1-based).
	 */
	const clickNextAndWaitForPane = function( index ) {
		// Click on the next button.
		browser.click( '.viewport > ul > li:nth-child(' + index + ') [data-wl-next]' );

		// Wait until the next pane is visible.
		browser.waitUntil( function() {
			// console.log(browser.getLocation('.viewport > ul >
			// li:nth-child()', 'x'));
			return paneX === browser.getLocation( '.viewport > ul > li:nth-child(' + (
												  index + 1
												  ) + ')', 'x' );
		}, 750, 'expected pane to be visible within 750ms' );
	};

	it( 'opens the plugins page and activates WordLift', function() {
		// Navigate to the plugins page.
		browser.url( '/wp-admin/plugins.php' );

		// Check the URL.
		// expect(browser.getUrl()).toMatch(/\/wp-admin\/plugins\.php$/);

		// Get WordLift's row in the plugins' list.
		browser.waitForExist( '[data-slug="wordlift"]' );

		// Activate WordLift.
		browser.click( '[data-slug="wordlift"] .activate a' );
		browser.pause( 2500 );

		// We got redirected to the `wl-setup` page.
		// expect(browser.getUrl()).toMatch(/\/wp-admin\/index\.php\?page=wl-setup$/);

		// Wait until the element becomes invalid.
		browser.waitForExist( '.viewport > ul > li:first-child' );

		// Set the x offset for the current visible pane.
		paneX = browser.getLocation( '.viewport > ul > li:first-child', 'x' );
	} );

	it( 'continues to License Key', function() {
		// Click next and wait for the 2nd pane.
		clickNextAndWaitForPane( 1 );

		// Set an invalid key.
		browser.setValue( '#key', 'an-invalid-key' );

		// Wait until the element becomes invalid.
		browser.waitForExist( '#key.invalid' );

		// Set a valid key.
		browser.setValue( '#key', process.env.WORDLIFT_KEY );

		// Wait until the element becomes valid.
		browser.waitForExist( '#key.valid' );
	} );

	it( 'continues to Vocabulary', function() {
		// Click next and wait for the 3rd pane.
		clickNextAndWaitForPane( 2 );

		// browser.click('input#vocabulary');
		//
		// // Set an invalid vocabulary path.
		// browser.keys(['Backspace', '_']);
		//
		// browser.saveScreenshot();
		//
		// // Wait until the element becomes invalid.
		// browser.waitForExist('input#vocabulary.invalid');
		//
		// // Set a valid vocabulary.
		// browser.keys('Backspace');

		// Wait until the element becomes valid.
		browser.waitForExist( 'input#vocabulary.valid' );
	} );

	it( 'continues to Language', function() {
		// Click next and wait for the 4th pane.
		clickNextAndWaitForPane( 3 );
	} );

	it( 'continues to Publisher', function() {
		// Click next and wait for the 5th pane.
		clickNextAndWaitForPane( 4 );

		browser.waitForExist( 'input#name' );

		// Set the company name.
		browser.setValue( 'input#name', 'Acme Inc.' );
		browser.pause( 2500 );

		// Click on finish.
		browser.waitForExist( '#btn-finish' );
		browser.click( '#btn-finish' );
		browser.pause( 5000 );

		// Check that we got back to the admin area.
		browser.waitForExist( '.wp-admin' );
	} );
};

// Finally export the `SetupPage`.
export default SetupPage;
