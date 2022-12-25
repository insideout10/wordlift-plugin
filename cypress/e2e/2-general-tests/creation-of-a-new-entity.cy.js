describe( 'Creation of a new entity', () => {
    const postTitle = 'WL Creating New Entity';
	it( 'Selection and creation of a brand new entity', () => {
        cy.createPostOrPage( postTitle, 'Software', 'post', 'publish' );
        cy.wait(500);
        cy.get('button[aria-label="WordLift"]').click();
        cy.wait(1000);
        cy.get('.wp-block-paragraph').type('{selectAll}');

        cy.get('.sc-kAzzGY.RtvhL').click();
        cy.get('.sc-dxgOiQ.cYUqAu').click();
        cy.get('select').select('http://schema.org/Thing');
        cy.get('form > :nth-child(2) > textarea').click();
        cy.get('form > :nth-child(2) > textarea').type('Software Entity');
        cy.get('form > :nth-child(3) > .is-primary').click();
        cy.get('.sc-jzJRlG').click();
        cy.get('.sc-iwsKbI').click();
        
        cy.get('.wp-block-paragraph .textannotation').should('have.class', 'wl-link');

        // TODO: Check if the new entity has been created.

        // cy.visit('/wp-admin/edit.php?post_type=entity');
        // cy.wait( 500 );

        // cy.get('.row-title').should( 'contain.text', 'Software' );
    });

    after( () => {
		cy.visit('/wp-admin/edit.php?post_type=post');
		cy.deletePost( postTitle, 'post' );
	});

});