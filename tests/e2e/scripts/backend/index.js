import Admin from './admin';

'use strict';

describe( 'Open the WordPress web site', function() {

	it( 'admin logs in', function() {

		browser.url( '/wp-login.php' );

		browser.waitForVisible( '#wp-submit' );

		browser.setValue( '#user_login', 'admin' );
		browser.setValue( '#user_pass', 'admin' );
		browser.click( '#wp-submit' );

		browser.waitForExist( 'body.wp-admin' );

	} );

	describe('while in WordPress backend, admin', Admin);

} );