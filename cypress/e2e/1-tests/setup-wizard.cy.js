describe('Setup Wizard', () => {
	beforeEach( () => {
		cy.visit('/wp-admin/admin.php?page=wl-setup');
	});

	it( 'Learn more button', () => {
        cy.get(':nth-child(1) > :nth-child(1) > .btn-wrapper > .button')
			.invoke('attr', 'target', '_self')
			.click();
		cy.on('url:changed', (newUrl) => {
			expect(newUrl).to.contain('https://wordlift.io/professional')
		});
    });

	// it( '', () => {} )
	// it( '', () => {} )
	// it( '', () => {} )
	// it( '', () => {} )
});