const WordPress = function( window, views, $ ) {

	const navigator = _.extend( {}, {
		content: 'Hello Navigator Preview',
		bindNode: function() {

			console.log( arguments );

		},
		unbindNode: function() {

			console.log( arguments );

		}
//		state: 'wl-navigator-edit',
//		template: 'Navigator Preview', // wp.media.template( 'editor-gallery'
// ),

//		render: function() {
//			console.log( this );
//			console.log( arguments );
//			this.$el.html( 'Navigator Preview' );
//			return this;
//		},
//		initialize: function() {
//				var attachments = wp.media.gallery.attachments(
// this.shortcode, postID ), attrs = this.shortcode.attrs.named, self = this;
// attachments.more() .done( function() { attachments = attachments.toJSON();
// _.each( attachments, function( attachment ) { if ( attachment.sizes ) { if (
// attrs.size && attachment.sizes[ attrs.size ] ) { attachment.thumbnail =
// attachment.sizes[ attrs.size ]; } else if ( attachment.sizes.thumbnail ) {
// attachment.thumbnail = attachment.sizes.thumbnail; } else if (
// attachment.sizes.full ) { attachment.thumbnail = attachment.sizes.full; } }
// } );  self.render( self.template( { attachments: attachments, columns:
// attrs.columns ? parseInt( attrs.columns, 10 ) :
// wp.media.galleryDefaults.columns } ) ); } ) .fail( function( jqXHR,
// textStatus ) { self.setError( textStatus ); } );
//		}
	} );

	views.register( 'wl_navigator', _.extend( {}, navigator ) );

};

export default WordPress;