describe( 'Vocabulary', () => {
    const postTitle = 'WL QA Test Vocabulary';
	it( 'Add vocabulary widget in post', () => {
        cy.createPostOrPage( postTitle, 'Founded by Insideout10 with the help of Maurizio Sarlo and Francesco Scavelli in 2017, WordLift is on a mission to automate content marketing and SEO with the help of artificial intelligence.', 'post', 'publish' );
        cy.wait(500);
        cy.get('button[aria-label="WordLift"]').click();
        cy.wait(1000);
        cy.get('.wp-block-paragraph').type('{enter}').type('{enter}');
        cy.get('.wp-block-paragraph').last().type('/vocabulary');

        cy.get('#components-autocomplete-item-1-block-wordlift\\/vocabulary').click();

        cy.get('.editor-post-publish-button__button.is-primary').click()
            .wait(1000);

        cy.get('.components-button.components-snackbar__action.is-tertiary').then( ( $button ) => {
            const href = $button.attr('href');
            cy.visit( `${href}?amp=1` );
            cy.get('.wl-vocabulary').should('be.visible');
        });
        
    });

    after( () => {
		cy.visit('/wp-admin/edit.php?post_type=post');
		cy.deletePost( postTitle, 'post' );
	});

});