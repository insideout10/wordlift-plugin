describe(
    'Check the markup',
    {
        skipBeforeEach: true,
    },
    () => {
	it('Check post markup with Google SDTT', () => {
        cy.visit(`https://validator.schema.org/?url=${Cypress.env('post_link')}`);
        cy.wait(3000);
        cy.get('.K4efff-fmcmS').should('contain.text', '0 ERRORS');
        cy.get('.K4efff-fmcmS').should('contain.text', '0 WARNINGS');
    });

});