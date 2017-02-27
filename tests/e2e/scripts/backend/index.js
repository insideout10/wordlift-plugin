/**
 * Internal dependencies
 */
import SetupPage from './SetupPage';

// Define the overall tests.
describe( 'test WordLift', function() {
	// Test logging into WordPress.
	it( 'log into WordPress backend', function() {
		// Open the login page.
		browser.url( '/wp-login.php' );

		// Wait for the login button.
		browser.waitForVisible( '#wp-submit' );

		// Type username and password, then submit.
		browser.setValue( '#user_login', 'admin' );
		browser.setValue( '#user_pass', 'admin' );
		browser.click( '#wp-submit' );

		// Wait for the admin screen to load.
		browser.waitForExist( 'body.wp-admin' );
	} );

	// Test the Set-up Page. A clean WordPress install is required for this test
	// to work. After the set-up, the Settings Page and the Post Edit Page will
	// be tested.
	describe( 'test the Setup Page', SetupPage );
} );
