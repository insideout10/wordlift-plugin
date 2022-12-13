describe('Exit the wizard', () => {
    it( 'Check if clicking X exit the wizard', () => {
        cy.visit( '/wp-admin/admin.php?page=wl-setup' );
        cy.get('.wl-close').click();
        cy.get('.wl-container').should('not.exist');

        cy.url().should('contain', '/wp-admin');
    })

    it( 'Check if notices are displayed', () => {
        cy.get('.wl-notice').then( $el => {
            expect($el).to.have.class('error');
        })

        cy.get('#wl-message').then( $el => {
            expect($el).to.have.class('updated');
            expect($el.find('.submit').children('a').first()).to.have.text('Run the Setup Wizard');
        })
    })

    it( 'Check if clicking on Run the Setup Wizard open the Wizard', () => {
        cy.get('#wl-message').then( $el => {
            cy.get('#wl-message > .submit > .button-primary').click();
            cy.wait(500);
            cy.url().should('contain', '/wp-admin/admin.php?page=wl-setup');
        })
        
    })
});