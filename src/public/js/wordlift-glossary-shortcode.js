(function( $ ) {
  $( document ).ready( function() {
    $( '.wl-vocabulary-alphabet-nav a' ).on( 'click', function(e) {
      e.preventDefault();

      const fullUrl = $( this ).attr( 'href' ),
        parts = fullUrl.split( '#' ),
        target = parts[ 1 ],
        navHeight = $( '.wl-vocabulary-alphabet-nav' ).height(),
        targetOffset = $(this).parents('.wl-vocabulary').find( '#' + target ).offset(),
        targetTop = targetOffset.top - navHeight;

      $( 'html, body' ).animate( { scrollTop: targetTop }, 500 );

      $( '.wl-vocabulary-alphabet-nav a' ).removeClass( 'active' );
      $( this ).addClass( 'active' );

    } );
  } );
})( jQuery );
