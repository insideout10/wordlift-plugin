/**
 * Modified By: Mahbub Hasan Imon <mahbub@wordlift.io>
 * 
 * Credit: https://github.com/lequangphuc/cypress-wordpress/blob/develop/commands/cy-wordpress.js
 */

Cypress.Commands.add('inputFile', (selector, fileUrl, type = '') => {
    return cy.get(selector).then(subject => {
        return cy.fixture(fileUrl, 'base64').
        then(Cypress.Blob.base64StringToBlob).
        then(blob => {
            return cy.window().then(win => {
                const el = subject[0];
                const nameSegments = fileUrl.split('/');
                const name = nameSegments[nameSegments.length - 1];
                const testFile = new win.File([blob], name, {
                    type
                });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(testFile);
                el.files = dataTransfer.files;
                return subject;
            });
        });
    });
});

Cypress.Commands.add('login', ( username, password ) => {
    cy.visit('/wp-login.php');
    cy.wait(500);
    cy.get('#user_login').type(username);
    cy.get('#user_pass').type(password);
    cy.get('#loginform').submit().wait(500);

    cy.url().should('contain', '/wp-admin');
});

Cypress.Commands.add('uploadFile', (imageLocation) => {
    cy.visit('/wp-admin/media-new.php?browser-uploader').
    inputFile('#async-upload', imageLocation).
    get('#html-upload').click().
    visit('/wp-admin/upload.php?mode=list');
});

Cypress.Commands.add('deleteFile', (fileName) => {
    cy.visit('/wp-admin/upload.php?mode=list').
    get(`a[aria-label="Delete “${fileName}” permanently"]`).
    click({
        force: true,
        multiple: true
    });
});

Cypress.Commands.add('editFile', (fileName) => {
    cy.visit('/wp-admin/upload.php?mode=list').
    get(`a[aria-label="Edit “${fileName}”"]`).
    click({
        force: true
    });
});

Cypress.Commands.add('bulkActions', (selector) => {
    cy.get('#cb-select-all-1').click().
    get('#bulk-action-selector-top').select(selector).
    get('#doaction').click();
});

Cypress.Commands.add('createPost', (header, content) => {
    content = content || 'default';
    cy.visit('/wp-admin/post-new.php').
    get('#title').type(header).
    get('#content-html').click().
    get('#content').type(content).wait(1000).
    get('#publish').wait(500).click().wait(1500);
});

Cypress.Commands.add('createPage', (header, content) => {
    content = content || 'default';
    cy.visit('/wp-admin/post-new.php?post_type=page').
    get('#title').type(header).
    get('#content-html').click().
    get('#content').type(content).wait(1000).
    get('#publish').wait(500).click().wait(1500);
});

Cypress.Commands.add('deletePost', (title) => {
    cy.get(`a[aria-label="Move “${title}” to the Trash"]`).
    click({
        force: true,
        multiple: true
    });
});

Cypress.Commands.add('editPost', (title) => {
    cy.get(`a[aria-label="Edit “${title}”"]`).
    click({
        force: true
    });
});

Cypress.Commands.add('viewPost', (title) => {
    cy.get(`a[aria-label="View “${title}”"]`).
    click({
        force: true
    });
});

Cypress.Commands.add('createCategory', (title, parent) => {
    parent = parent || 'None';
    cy.visit('/wp-admin/edit-tags.php?taxonomy=category').
    get('#tag-name').type(title).
    get('#parent').select(parent).
    get('#submit').click().wait(500);
});

Cypress.Commands.add('deleteCategory', (name) => {
    cy.visit('/wp-admin/edit-tags.php?taxonomy=category').
    get(`a[aria-label="Delete “${name}”"]`).
    click({
        force: true,
        multiple: true
    });
});

Cypress.Commands.add('deleteAllCategories', () => {
    cy.visit('/wp-admin/edit-tags.php?taxonomy=category').
    get('#cb-select-all-1').click().
    get('#bulk-action-selector-top').select('delete').
    get('#doaction').click().wait(500);
});

Cypress.Commands.add('logoutWpAdmin', () => {
    cy.get('#wpadminbar > a').contains('Log Out').
    click({
        force: true
    }).wait(500);
});

Cypress.Commands.add('getUrlStatus', ({
    url,
    status
}) => {
    cy.request({
        url: url,
        followRedirect: false,
        failOnStatusCode: false,
    }).then((response) => {
        expect(response.status).to.eq(status);
    })
});

Cypress.Commands.add('requestPrivateUrl', ({
    url,
    isPermission = true
}) => {
    cy.request({
        url: url,
        followRedirect: false,
        failOnStatusCode: false,
    }).then((response) => {
        (isPermission) ? expect(response.status).to.eq(200): expect(response.status).to.eq(404);
    })
});

Cypress.Commands.add('deactivatePlugin', (plugin) => {
    cy.visit('wp-admin/plugins.php?plugin_status=active').
    get(`a[aria-label="Deactivate ${plugin}"]`).
    click().wait(1000);
});

Cypress.Commands.add('activatePlugin', (plugin) => {
    cy.visit('wp-admin/plugins.php?plugin_status=inactive').
    get(`a[aria-label="Activate ${plugin}"]`).
    click().wait(1000);
});

Cypress.Commands.add('deletePlugin', (plugin) => {
    cy.visit('wp-admin/plugins.php?plugin_status=inactive').
    get(`a[aria-label="Delete ${plugin}"]`).
    click().wait(1000);
});

Cypress.Commands.add('installPlugin', (pluginFile) => {
    cy.visit('wp-admin/plugin-install.php').
    get('.upload-view-toggle').click().
    inputFile('#pluginzip', pluginFile).
    get('#install-plugin-submit').click().wait(2000);
});

Cypress.Commands.add('createAccount', ({
    username,
    password,
    role
}) => {
    cy.visit('/wp-admin/user-new.php').
    get('#user_login').type(username).
    get('#email').type(`${username}@example.com`).
    get('.wp-generate-pw').click().
    get('#pass1-text').type(password).
    get('input[name="pw_weak"]').check({
        force: true
    }).
    get('#role').select(role).
    get('#createuser').submit();
});

Cypress.Commands.add('deleteAccount', (username) => {
    cy.visit('/wp-admin/users.php').
    get('#the-list > tr > .username > strong > a').
    contains(username).
    should('have.attr', 'href').
    then((url) => {
        let user_url = new URL(url);
        let uid = user_url.searchParams.get("user_id");
        cy.log(uid);
        cy.get(`#user-${uid} > .username > .row-actions > .delete > .submitdelete`).
        click({
            force: true
        });
    }).
    get('input[name="delete_option"]').
    click({
        force: true,
        multiple: true
    }).
    get('#submit').click();
});