'use strict';

describe('Open the WordPress web site', function () {

    it('admin logs in', function () {

        browser.url('/wp-login.php');
        browser.setValue('#user_login', 'admin');
        browser.setValue('#user_pass', 'admin');
        browser.click('#wp-submit');

        expect(browser.getUrl()).toMatch(/\/wp-admin\/$/);

    });

    describe('while in WordPress backend, admin', function () {

        it('opens the plugins page and activates WordLift', function () {

            // Navigate to the plugins page.
            browser.url('/wp-admin/plugins.php');

            // Check the URL.
            expect(browser.getUrl()).toMatch(/\/wp-admin\/plugins\.php$/);

            // Get WordLift's row in the plugins' list.
            var wordlift = browser.element('[data-slug="wordlift"]');

            // Check that WordLift's row is there.
            expect(wordlift).not.toBeUndefined();

            // Activate WordLift.
            wordlift.click('.activate a');

            // We got redirected to the `wl-setup` page.
            expect(browser.getUrl()).toMatch(/\/wp-admin\/index\.php\?page=wl-setup$/);

        });

        it('continues to License Key', function () {

            browser.click('.viewport > ul li:first-child input.wl-next');

            browser.setValue('input[name=key]', 'an-invalid-key');

            expect(browser.element('input.invalid[name=key]')).not.toBeUndefined();

            browser.setValue('input[name=key]', process.env.WORDLIFT_KEY);

            // Wait until the element becomes valid.
            browser.waitForExist('input.valid[name=key]');

            expect(browser.element('input.valid[name=key]')).not.toBeUndefined();

        });

        it('continues to Vocabulary', function () {

            browser.click('.viewport > ul li:nth-child(2) input.wl-next');

            browser.waitForVisible('input#vocabulary');

            browser.setValue('input#vocabulary', '_an_invalid_vocabulary_');

            expect(browser.element('input#vocabulary.invalid')).not.toBeUndefined();

            browser.setValue('input#vocabulary', 'vocabulary');

            // Wait until the element becomes valid.
            browser.waitForExist('input#vocabulary.valid');

            expect(browser.element('input#vocabulary.valid')).not.toBeUndefined();

        });

        it('continues to Language', function () {

            browser.click('.viewport > ul li:nth-child(3) input.wl-next');

            browser.waitForVisible('select#language');
        });

        it('continues to Publisher', function () {

            browser.click('.viewport > ul li:nth-child(4) input.wl-next');

            browser.waitForVisible('input#company');

            // Click on the company button.
            browser.click('input#company');

            // Set the company name.
            browser.setValue('input#name', 'Acme Inc.');

            // Finish.
            browser.click('input#btn-finish');

            // We got redirected to the `wl-setup` page.
            expect(browser.getUrl()).toMatch(/\/wp-admin\/$/);

        });

    });

});