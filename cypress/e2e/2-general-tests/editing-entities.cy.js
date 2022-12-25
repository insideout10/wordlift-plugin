describe( 'Editing entities', () => {
    const postTitle = 'WL Editing Entity';
	it( 'Edit an existing entity', () => {
        cy.createPostOrPage( postTitle, 'WordLift', 'post', 'publish' );
        cy.wait(500);
        cy.get('button[aria-label="WordLift"]').click();
        cy.wait(1000);
        cy.get('.wp-block-paragraph').type('{selectAll}');

        cy.get('.sc-kAzzGY.RtvhL').click();
        cy.get('.sc-dxgOiQ.cYUqAu').click();
        cy.get('select').select('http://schema.org/Thing');
        cy.get('form > :nth-child(2) > textarea').click();
        cy.get('form > :nth-child(2) > textarea').type('WordLift is the best plugin.');
        cy.get('form > :nth-child(3) > .is-primary').click();
        cy.get('.sc-jzJRlG').click();
        cy.get('.sc-iwsKbI').click();

        // cy.get('.wp-block-paragraph .textannotation').should('have.class', 'wl-link');

        // TODO: Little bit of a problem here. The entity is not created if I add a description.

        // cy.visit('/wp-admin/edit.php?post_type=entity');
        // cy.wait( 500 );

        // cy.get('.row-title').should( 'contain.text', 'Software' );
        /* ==== Generated with Cypress Studio ==== */
        // cy.get('.sc-ifAKCX').click();
        // cy.get('.sc-fjdhpX').click();
        // cy.get('.sc-jTzLTM').click();
        /* ==== End Cypress Studio ==== */
    });

    after( () => {
		// cy.visit('/wp-admin/edit.php?post_type=post');
		// cy.deletePost( postTitle, 'post' );
	});

});