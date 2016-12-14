'use strict';

describe('On a WordPress site', function () {

    it('admin should log in', function () {

        browser.url('http://localhost/wp-login.php');
        browser.setValue('#user_login', 'admin');
        browser.setValue('#user_pass', 'admin');
        browser.click('#wp-submit');

        expect(browser.getUrl()).toBe('http://localhost/wp-admin/');

    });

    describe('in the admin area', function () {

        it('should open the plugins page', function () {

            browser.url('http://localhost/wp-admin/plugins.php');

            expect(browser.element('[data-slug="wordlift"][data-plugin="wordlift/wordlift.php"]')).not.toBeUndefined();

            browser.click('[data-slug="wordlift"][data-plugin="wordlift/wordlift.php"] .activate a');

            expect(browser.getUrl()).toBe('http://wordpress-46.localhost/wp-admin/admin.php?page=wl-setup');

        });

    });

});