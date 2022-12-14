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
});