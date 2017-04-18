/**
 * Internal dependencies
 */
import LoginToWordPress from './LoginToWordPress';
import SetupPage from './SetupPage';
import SettingsPage from './SettingsPage';
import PostEditPage from './PostEditPage';
import CreateSampleData from './CreateSampleData';
import OpenPostLoremIpsum from './OpenPostLoremIpsum';

// Define the overall tests.
describe( 'test WordLift', function() {
	// Test logging into WordPress.
	it( 'logs into WordPress backend', LoginToWordPress );

	// Test the Set-up Page. A clean WordPress install is required for this test
	// to work. After the set-up, the Settings Page and the Post Edit Page will
	// be tested.
	describe( 'test the Setup Page', SetupPage );

	// Test the WordLift Settings Page.
	describe( 'test the Settings Page', SettingsPage );

	// Test the Post Edit Page.
	describe( 'test the Post Edit Page', PostEditPage );

	// Create Sample Data.
	it( 'creates sample data', CreateSampleData );

	// Open the Lorem Ipsum post to test the Navigator.
	describe( 'test the Navigator', OpenPostLoremIpsum );
} );
