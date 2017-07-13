/**
 * Validators: Key Validator.
 *
 * Validate WordLift's key in inputs.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */
import delay from './delay';

// Map $ to jQuery.
const $ = jQuery;

/**
 * Create a key validator on the element with the specified selector.
 *
 * @since 3.11.0
 * @param {string} selector The element selector.
 */
const KeyValidator = ( selector ) => {
	$( selector ).on( 'keyup', function() {
		// Get a jQuery reference to the object.
		const $this = $( this );

		// Remove any preexisting states, including the `untouched` class
		// which is set initially to prevent displaying the
		// `valid`/`invalid` indicator.
		$this.removeClass( 'untouched valid invalid' );

		// Delay execution of the validation.
		delay( $this, function() {
			// Post the validation request.
			wp.ajax.post( 'wl_validate_key', { key: $this.val() } )
			  .done( function( data ) {
				  // If the key is valid then set the process class.
				  if ( data && data.valid ) {
					  $this.addClass( 'valid' );
				  } else {
					  $this.addClass( 'invalid' );
				  }
			  } );
		} );
	} );
};

// Finally export the `KeyValidator` function.
export default KeyValidator;
