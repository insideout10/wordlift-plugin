describe( 'Analysis', () => {
	const postTitle = 'Test Draft';
	it( 'Analyze a WP draft', () => {
        cy.createPostOrPage( postTitle, 'Test Draft Content', 'post', 'draft' );
        cy.get('button[aria-label="WordLift"]').click();
		cy.wait(500);
		cy.get('.wl-tab-wrap').find('ul li').then( ( $el ) => {
			cy.wrap( $el ).should( 'have.length', 2 );
			cy.wrap( $el ).eq( 0 ).find( 'img' ).should( 'have.length', 0 );
			cy.wrap( $el ).eq( 1 ).find( 'img' ).should( 'have.length', 1 );
		});
		cy.percySnapshot();
    });

	after( () => {
		cy.visit('/wp-admin/edit.php?post_type=post');
		cy.deletePost( postTitle, 'post' );
	});
});