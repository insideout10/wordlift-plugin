/**
 * Internal dependencies
 */
import LoginToWordPress from './LoginToWordPress';
import SetupPage from './SetupPage';

// Define the overall tests.
describe( 'test WordLift', function() {
	// Test logging into WordPress.
	it( 'log into WordPress backend', LoginToWordPress );

	// Test the Set-up Page. A clean WordPress install is required for this test
	// to work. After the set-up, the Settings Page and the Post Edit Page will
	// be tested.
	describe( 'test the Setup Page', SetupPage );
} );
