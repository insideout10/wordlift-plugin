describe('Activate the plugin', () => {
	beforeEach(() => {
		cy.visit( '/wp-admin/plugins.php' );
	});

	it('Check the version', () => {
		cy.get('[data-slug="wordlift"] > .column-description > .plugin-version-author-uri')
			.invoke('text')
			.then((text) => {
				const version = text.match(/Version\s*(\d+\.\d+\.\d+)/);
				expect(version[1]).to.equal(Cypress.env('version'));
			});
	});

	it('Activate the plugin', () => {
        cy.get('[data-slug="wordlift"] > .plugin-title > .row-actions').then(($el) => {
			if ($el.children('.activate').length) {
				cy.activatePlugin('WordLift');
			}
		});
    });
});