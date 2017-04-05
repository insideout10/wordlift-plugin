console.log( { mce: window.parent.wp.mce } );

(
	function( window, views, $, _ ) {

		const navigator = _.extend( {}, {
			content: 'Loading...',
			bindNode: function( x, element ) {

				console.log( { bindNode: arguments } );

				window.wl.Navigator( element );

			},
			unbindNode: function() {

				console.log( arguments );

			}
		} );

		views.register( 'wl_navigator', _.extend( {}, navigator ) );

	}
)( window.parent, window.parent.wp.mce.views, window.parent.window.jQuery, window.parent._ );
