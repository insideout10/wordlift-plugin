describe( 'Geo Map', () => {
    const postTitle = 'GEO Map Test';
	it( 'Creating Place Entity', () => {

        cy.createPostOrPage( 'Bilbao', 'Bilbao City', 'entity', 'publish' );
        // check if aria-label="Settings" has class 'is-pressed' then do not click else click.
        cy.get('[aria-label="Settings"]').then( ( $button ) => {
            if ( $button.hasClass('is-pressed') ) {
                cy.log('Settings button is already pressed');
            } else {
                cy.get('[aria-label="Settings"]').click();
            }
        });
        cy.wait(1000);
        cy.get('[data-label="Entity"]').click();
        cy.wait(1000);
        // check if Entity Types has class 'is-opened' then do not click else click.
        cy.get('.components-button.components-panel__body-toggle').contains('Entity Types').then( ( $button ) => {
            // check if aria-expanded is true then do not click else click.
            if ( $button.attr('aria-expanded') === 'true' ) {
                cy.log('Entity Types is already opened');
            } else {
                cy.get('.components-button.components-panel__body-toggle').contains('Entity Types').click();
            }
        });
        // .components-checkbox-control__label the checkbox with label 'Thing' and uncheck it.
        cy.get('.components-checkbox-control__label').contains('Thing').parent().find('input').uncheck();
        cy.get('.components-checkbox-control__label').contains('Place').parent().find('input').check();

        cy.get('.editor-post-publish-button__button.is-primary').click()
            .wait(1000);

        cy.reload();
        

        cy.get('#wl_place_lat').type('10.759923505500574');
        cy.get('#wl_place_lon').type('25.381764734328957');

        cy.get('.editor-post-publish-button__button.is-primary').click();
    });

    it( 'Add Geo Map widget in post', () => {

        cy.createPostOrPage( postTitle, 'Bilbao', 'post', 'publish' );
        cy.wait(500);
        cy.get('button[aria-label="WordLift"]').click();
        cy.wait(1000);
        cy.get('.wp-block-paragraph').type('{enter}').type('{enter}');
        cy.get('.wp-block-paragraph').last().type('/geomap');

        cy.get('#components-autocomplete-item-1-block-wordlift\\/geomap').click();

        cy.get('.editor-post-publish-button__button.is-primary').click()
            .wait(1000);

        cy.get('.components-button.components-snackbar__action.is-tertiary').then( ( $button ) => {
            const href = $button.attr('href');
            cy.visit( `${href}?amp=1` );
            cy.get('.wl-geomap').should('be.visible');
        });
        cy.wait(1000);
        
    });

    after( () => {
		cy.visit('/wp-admin/edit.php?post_type=post');
		cy.deletePost( postTitle, 'post' );

        cy.visit('/wp-admin/edit.php?post_type=entity');
		cy.deletePost( 'Bilbao', 'entity' );
	});

});