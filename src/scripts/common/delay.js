/**
 * Delay a function call by half a second.
 *
 * Any function can be delayed using `delay`. The timeout for the call is bound
 * to the provided element. If another function call is delayed on the same
 * element, any previous timeout is cancelled.
 *
 * This function is used to validate in real-time inputs when the user presses
 * a key, but allowing the user to press more keys (hence the delay).
 *
 * @since 3.9.0
 *
 * @param {Object} $elem A jQuery element reference which will hold the timeout
 *     reference.
 * @param {Function} fn The function to call.
 * @param {number} timeout The timeout, by default 500 ms.
 * @param {...Object} args Additional arguments for the callback.
 */
const delay = ( $elem, fn, timeout = 500, ...args ) => {
	// Clear a validation timeout.
	clearTimeout( $elem.data( 'timeout' ) );

	// Validate the key, after a delay, so that another key is pressed, this
	// validation is cancelled.
	$elem.data( 'timeout', setTimeout( fn, timeout, ...args ) );
};

// Finally export the `delay` function.
export default delay;
