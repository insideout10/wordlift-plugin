describe( 'Timeline', () => {
    const postTitle = 'Timeline Test';
	it( 'Creating Event Entity', () => {
        cy.createPostOrPage( 'WordCamp Pabna', 'WordCamp Pabna', 'entity', 'publish' );
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

        cy.get('.components-checkbox-control__label').contains('Thing').parent().find('input').uncheck();
        cy.get('.components-checkbox-control__label').contains('Event').parent().find('input').check();

        // cy.get('.editor-post-publish-button__button.is-primary').click()
        //     .wait(500);
        // // forche reload.
        // cy.reload();

        // cy.wait(500);

        const startDate = '2022-12-28 00:00:00';
        const endDate = '2022-12-30 00:00:00';
        
        cy.get('#acf-field_wl_0e720087250f9abd5875e470dd8b8e26309b7309').clear();
        cy.get('#acf-field_wl_0e720087250f9abd5875e470dd8b8e26309b7309').type('WordCamp Pabna 2022');

        cy.get('#acf-field_wl_1704c266af4c7a4ee0d0fa4aa2dd771449cec94e').then( ( el ) => {
            cy.get( el ).parent().find('.hasDatepicker').click();
            cy.get('.ui-datepicker-calendar').find('a').contains('28').click();
            cy.get('.ui-datepicker-close.ui-priority-primary').click();
        });
        cy.get('#acf-field_wl_42f4bc02b29b0d5d4a9f9eb44755a61f3016d08d').then( ( el ) => {
            // cy.get( el ).parent().find('.hasDatepicker').type( endDate );
            cy.get( el ).parent().find('.hasDatepicker').click();
            cy.get('.ui-datepicker-calendar').find('a').contains('30').click();
            cy.get('.ui-datepicker-close.ui-priority-primary').click();
        });

        cy.get('.editor-post-publish-button__button.is-primary').click().wait(1000);

        cy.reload(true);
        cy.wait(500);

        cy.get('.wl_cal_date_start').type(startDate);
        cy.get('.wl_cal_date_end').type(endDate);
        // cy.get('.wl_cal_date_start').then( ( el ) => {
        //     cy.get( el ).parent().find('.hasDatepicker').click();
        //     cy.get('.ui-datepicker-calendar').find('a').contains('28').click();
        //     cy.get('.ui-datepicker-close.ui-priority-primary').click();
        // });

    });

    it( 'Add Timeline widget in post', () => {

        cy.createPostOrPage( postTitle, 'WordCamp Pabna', 'post', 'publish' );
        cy.wait(500);

        cy.get('.wp-block-paragraph').type('{enter}').type('{enter}');
        cy.get('.wp-block-paragraph').last().type('/timeline');

        cy.get('#components-autocomplete-item-1-block-wordlift\\/timeline').click();

        cy.get('button[aria-label="WordLift"]').click();
        cy.wait(1000);
        cy.get('.fudeeb').find('li').then( ( $li ) => {
            cy.get( $li ).each( ( li ) => {
                cy.wrap( li ).click();
            });
        });

        cy.get('.editor-post-publish-button__button.is-primary').click()
            .wait(1000);

        cy.get('.components-button.components-snackbar__action.is-tertiary').then( ( $button ) => {
            const href = $button.attr('href');
            cy.visit( `${href}?amp=1` );
            cy.wait(2000);
            cy.get('.wl-timeline-container').should('be.visible');
        });
        cy.wait(2000);
        
    });

    after( () => {
		// cy.visit('/wp-admin/edit.php?post_type=post');
		// cy.deletePost( postTitle, 'post' );

        // cy.visit('/wp-admin/edit.php?post_type=entity');
		// cy.deletePost( 'WordCamp Pabna', 'entity' );
	});

});