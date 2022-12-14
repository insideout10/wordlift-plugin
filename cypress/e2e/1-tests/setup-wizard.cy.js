describe('Setup Wizard', () => {
	beforeEach( () => {
		cy.visit('/wp-admin/admin.php?page=wl-setup');
	});

	it( 'Check if learn more button works', () => {
        cy.get(':nth-child(1) > :nth-child(1) > .btn-wrapper > .button')
			.invoke('attr', 'target', '_self')
			.click();
		cy.on('url:changed', (newUrl) => {
			expect(newUrl).to.contain('https://wordlift.io/professional')
		});
    });

	it( 'Check if about our privacy policy link works', () => {
        cy.get(':nth-child(1) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get('.privacy-policy-details > a')
			.invoke('attr', 'target', '_self')
			.click();
		cy.on('url:changed', (newUrl) => {
			expect(newUrl).to.equal('https://wordlift.io/privacy-policy/')
		});
    });

	it( 'Check if grab key link works', () => {
        cy.get(':nth-child(1) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get(':nth-child(2) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get('.page-txt > a').click();
		cy.on('url:changed', (newUrl) => {
			expect(newUrl).to.contain('https://wordlift.io/pricing')
		});
    });

	it( 'Check if an invalid key is recognized as invalid', () => {
        cy.get(':nth-child(1) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get(':nth-child(2) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        // cy.get('#key').clear('h');
        cy.get('#key').type('invalid-key');
		cy.wait(300);
        cy.get('#key').should( 'have.class', 'invalid' );
    });
});