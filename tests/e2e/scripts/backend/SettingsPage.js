/**
 * Tests: WordLift Settings Page.
 *
 * @since 3.11.0
 */

/**
 * Define the `SettingsPage` tests.
 *
 * @since 3.11.0
 */
const SettingsPage = function() {
	// Opens the settings page and check for the initial settings.
	it( 'opens the settings page', () => {
		// The link to the settings page.
		const settingsPageLink = '[href="admin.php?page=wl_configuration_admin_menu"]';

		// Wait for the link to exists, then click.
		browser.waitForExist( settingsPageLink );
		browser.click( settingsPageLink );

		// Wait for the `wl-key` element to exist and to have a `valid` css
		// class indicating that the key is valid.
		browser.waitForExist( '#wl-key.valid' );

		// Expect the entity base path with `vocabulary` as path and to be
		// readonly.
		expect( browser.getValue( '#wl-entity-base-path' ) ).toBe( 'vocabulary' );
		expect( browser.getAttribute( '#wl-entity-base-path', 'readonly' ) ).toBe( 'true' );

		// Expect English to be selected as language.
		// @todo: FF has issues in getting the value for the selected option.
		expect( browser.getValue( '#wl-site-language option[selected="selected"]' ) ).toBe( 'en' );

		// Check that a publisher is set.
		expect( browser.getValue( '#wl-publisher-id' ) ).not.toBe( '' );
	} );

	// Try changing the license key and see how the input reacts.
	it( 'change the license key', () => {
		// Get the existing license key.
		const licenseKey = browser.getValue( '#wl-key' );

		// Set an invalid key.
		browser.setValue( '#wl-key', 'xyz' );

		// Wait for the `wl-key` to turn invalid.
		browser.waitForExist( '#wl-key.invalid' );

		// Set the valid key again.
		browser.setValue( '#wl-key', licenseKey );

		// Wait for the `wl-key` to turn valid.
		browser.waitForExist( '#wl-key.valid' );

		// @todo: replace with sendKeys when FF will support it.
		//		// Set an empty key.
		//		browser.setValue( '#wl-key', '' );
		//
		//		// Wait for the `wl-key` to turn invalid.
		//		browser.waitForExist( '#wl-key.invalid' );
		//		// Set the valid key again.
		//		browser.setValue( '#wl-key', licenseKey );
		//
		//		// Wait for the `wl-key` to turn valid.
		//		browser.waitForExist( '#wl-key.valid' );
	} );

	// Test changing the settings and create a publisher.
	it( 'change the settings', () => {
		// Click on the 'Create a New Publisher' tab.
		browser.click( '[href="#tabs-2"]' );

		// Wait for the `wp_publisher[name]` field to be visible.
		browser.waitForVisible( '[name="wl_publisher\[name\]"]' );

		// Click on the `Add Logo` button.
		browser.click( '#wl-publisher-media-uploader' );

		// Check that the `.media-modal` is visible.
		browser.waitForVisible( '.media-modal' );

		// @todo: add image upload and selection.

		// Then close it.
		browser.click( '.media-modal-close' );

		// Set the name.
		browser.setValue( '[name="wl_publisher\[name\]"]', 'John Smith' );

		// Submit the form.
		browser.scroll( '#submit' );
		browser.click( '#submit' );

		// Wait for the `wl-key` element to exist and to have a `valid` css
		// class indicating that the key is valid.
		browser.waitForExist( '#wl-key.valid' );

		// Check that the publisher is set.
		// @todo: FF has issues in getting the value for the selected option.
		expect( browser.getText( '.wl-select2-selection .wl-select2' ) )
			.toBe( 'John Smith' );

		// @todo: also test changing the language.
	} );
};

// Finally export the `SettingsPage`.
export default SettingsPage;
