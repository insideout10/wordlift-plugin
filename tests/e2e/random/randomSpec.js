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

            console.log(browser.element('[data-slug="wordlift"][data-plugin="wordlift/wordlift.php"]'));
            expect(browser.element('[data-slug="wordlift"][data-plugin="wordlift/wordlift.php"]')).not.toBeUndefined();

        });

    });

});