describe( 'Annotation', () => {
	it( 'Manually adding markup', () => {
        cy.createPostOrPage( 'Test Draft - WL', 'Wordlift', 'post', 'draft' );
        cy.wait(500);
        cy.get('button[aria-label="WordLift"]').click();
        cy.wait(1000);
        cy.get('.wp-block-paragraph').type('{selectAll}');

        cy.get('.sc-kAzzGY.RtvhL').click();
        cy.get('.sc-dxgOiQ.cYUqAu').click();
        cy.get('select').select('http://schema.org/Organization');
        cy.get('form > :nth-child(2) > textarea').click();
        cy.get('form > :nth-child(3) > .is-primary').click();
        cy.get('.sc-jzJRlG').click();
        cy.get('.sc-iwsKbI').click();
        
        cy.get('.wp-block-paragraph .textannotation').should('have.class', 'wl-link');

        cy.visit('/wp-admin/edit.php?post_type=entity');
        cy.wait( 500 );

        // @TODO : Check for duplicates in vocabulary.
    });

});