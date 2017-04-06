(
	function( window, views, _, tinyMCE ) {

		const navigator = _.extend( {}, {
			content: 'Loading...',
			bindNode: function( x, element ) {

				console.log( { bindNode: arguments } );

				const frame = tinyMCE.get( 'content' ).getWin();

				frame.wlSettings = parent.wlSettings;
				frame.wlNavigator = parent.wlNavigator;
				frame.wl.Navigator( element );

			},
			unbindNode: function() {

				console.log( arguments );

			}
		} );

		views.register( 'wl_navigator', _.extend( {}, navigator ) );

	}
)( window.parent, window.parent.wp.mce.views, window.parent._, window.parent.tinyMCE );
