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
        cy.get('#key').clear();
        cy.get('#key').type('invalid-key');
		cy.wait(300);
        cy.get('#key').should( 'have.class', 'invalid' );
    });

	it( 'Check if an valid key is recognized as valid', () => {
        cy.get(':nth-child(1) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get(':nth-child(2) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get('#key').clear();
        cy.get('#key').type( Cypress.env('key') );
		cy.wait(300);
        cy.get('#key').should( 'have.class', 'valid' );
    });

	it( 'Check if an empty vocabulary setting is accepted', () => {
        cy.get(':nth-child(1) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get(':nth-child(2) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get('#key').clear();
        cy.get('#key').type( Cypress.env('key') );
        cy.wait(300);
        cy.get('#key').should( 'have.class', 'valid' ).then( () => {
			cy.get('#btn-license-key-next').click();
			cy.wait(300);
		});
        cy.get('#vocabulary').clear('vocabulary');
        cy.get(':nth-child(4) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
		cy.wait(200);
        cy.get(':nth-child(5) > :nth-child(1) > .page-title').then( ( $el ) => {
			expect($el).to.contain('Country');
		});
    });
});