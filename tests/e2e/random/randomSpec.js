'use strict';

describe('Open the WordPress web site', function () {

    it('admin logs in', function () {

        browser.url('http://localhost/wp-login.php');
        browser.setValue('#user_login', 'admin');
        browser.setValue('#user_pass', 'admin');
        browser.click('#wp-submit');

        expect(browser.getUrl()).toBe('http://localhost/wp-admin/');

    });

    describe('while in WordPress backend', function () {

        it('admin opens the plugins page and activates WordLift', function () {

            // Navigate to the plugins page.
            browser.url('http://localhost/wp-admin/plugins.php');

            // Check the URL.
            expect(browser.getUrl()).toBe('http://localhost/wp-admin/plugins.php');

            // Get WordLift's row in the plugins' list.
            var wordlift = browser.element('[data-slug="wordlift"]');

            // Check that WordLift's row is there.
            expect(wordlift).not.toBeUndefined();

            // Activate WordLift.
            wordlift.click('.activate a');

            // We got redirected to the `wl-setup` page.
            expect(browser.getUrl()).toBe('http://localhost/wp-admin/admin.php?page=wl-setup');

        });

    });

});