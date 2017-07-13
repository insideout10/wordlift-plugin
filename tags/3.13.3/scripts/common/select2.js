/**
 * Created by david on 21/02/2017.
 */

const $ = jQuery;

/**
 * Create a Select2 element on the element identified by the selector.
 *
 * @since 3.11.0
 * @param {string} selector The element selector.
 * @param {Object} args Custom options.
 * @constructor
 */
const Select2 = ( selector, args = {} ) => {
	// Cycle through each element to create `Select2`.
	$( selector ).each( function() {
		//
		const $this = $( this );

		const options = Object.assign(
			{},
			{
				width: '100%',
				data: $this.data( 'wl-select2-data' ),
				escapeMarkup: function( markup ) {
					return markup;
				},
				templateResult: _.template( $this.data( 'wl-select2-template-result' ) ),
				templateSelection: _.template( $this.data( 'wl-select2-template-selection' ) )
			},
			args
		);

		// Create the tabs and set the default active element.
		$this.select2( options );
	} );
};

// Finally export `Select2`.
export default Select2;
