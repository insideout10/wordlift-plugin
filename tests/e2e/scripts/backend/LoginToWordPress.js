/**
 * Tests: Login to WordPress.
 *
 * Provides a function to login to WordPress.
 *
 * @since 3.11.0
 */

/**
 * Define the `LoginToWordPress` function.
 *
 * @since 3.11.0
 * @constructor
 */
const LoginToWordPress = () => {
	// Open the login page.
	browser.url( '/wp-login.php' );
	browser.pause( 5000 );

	// Wait for the login button.
	browser.waitForExist( '#wp-submit' );

	// Type username and password, then submit.
	browser.setValue( '#user_login', 'admin' );
	browser.setValue( '#user_pass', 'admin' );
	browser.click( '#wp-submit' );

	// Wait for the admin screen to load.
	browser.pause( 5000 );
};

// Finally export the `LoginToWordPress` function.
export default LoginToWordPress;
