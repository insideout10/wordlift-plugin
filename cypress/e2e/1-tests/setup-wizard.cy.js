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

	it( 'Check if the country list contains several countries & Selecting country works', () => {
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
        cy.get('#wl-country-code > option').should( 'have.length.greaterThan', 50 );

        cy.get('#wl-country-code').select('bd');
        cy.get('#wl-country-code').should( 'have.value', 'bd' );

        cy.get(':nth-child(5) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
		cy.wait(200);
		cy.get(':nth-child(6) > :nth-child(1) > .page-title').then( ( $el ) => {
			expect($el).to.contain('Publisher');
		});
    });

	it( 'Check if inserting a valid publisher name is accepted', () => {
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
        cy.get('#wl-country-code > option').should( 'have.length.greaterThan', 50 );

        cy.get('#wl-country-code').select('bd');
        cy.get('#wl-country-code').should( 'have.value', 'bd' );

        cy.get(':nth-child(5) > :nth-child(1) > .btn-wrapper > .wl-default-action').click();
        cy.get('[for="company"] > .radio').click();
        cy.get('#name').clear();
        cy.get('#name').type('Acme Inc.');
        cy.wait(200);
        cy.get('#name').should( 'have.class', 'valid' );
        cy.get('.add-logo').click();
        cy.get('#menu-item-browse').click();
        cy.get('li[data-id="5"]').find('.thumbnail').click();
        cy.get('.media-toolbar-primary > .button').click();
        cy.get('.wl-logo-preview > .fa').click();
		cy.get('.add-logo').click();
        cy.get('#menu-item-browse').click();
        cy.get('li[data-id="5"]').find('.thumbnail').click();
        cy.get('.media-toolbar-primary > .button').click();
        cy.get('#btn-finish').click();

		cy.url().should('contain', '/wp-admin');

		cy.get('.wl-notice').should('not.exist');
		cy.get('#wl-message').should('not.exist');
    });
});