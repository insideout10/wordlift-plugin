/**
 * Widgets: Navigator Widget.
 *
 * The Navigator Widget.
 *
 * @since 3.12
 */

/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';

/**
 * Internal dependencies
 */
import App from './components/App';

// Set a reference to jQuery.
const $ = jQuery;

window.wl = window.wl || {};

const Navigator = window.wl.Navigator = ( selector ) => {
	// Bail out if there are no elements where to attach.
	if ( 0 === $( selector ).length ) {
		return;
	}

	// Call the ajax action.
// eslint-disable-next-line camelcase
	wp.ajax.post( 'wl_navigator_get', { post_id: wlSettings.postId } ).done( function( data ) {
		// Get all the Navigator elements (marked up with the
		// `data-wl-navigator` attribute).
		$( selector ).each( function() {
			// Render the `React` tree.
			ReactDOM.render( <App data={ data } />, this );
		} );
	} );
};

// Initialize the default Navigator instance.
Navigator( '[data-wl-navigator]' );
