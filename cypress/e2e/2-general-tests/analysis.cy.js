describe( 'Analysis', () => {
	it( 'Analyze a WP draft', () => {
        cy.createPostOrPage( 'Test Draft', 'Test Draft Content', 'post', 'draft' );
        cy.get('button[aria-label="WordLift"]').click();
		cy.wait(500);
		cy.get('.wl-tab-wrap').find('ul li').then( ( $el ) => {
			cy.wrap( $el ).should( 'have.length', 2 );
			cy.wrap( $el ).eq( 0 ).find( 'img' ).should( 'have.length', 0 );
			cy.wrap( $el ).eq( 1 ).find( 'img' ).should( 'have.length', 1 );
		});
		cy.percySnapshot();
    });
});