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

// Call the `wl_navigator_get` action to get the related posts/entities.

// eslint-disable-next-line camelcase
wp.ajax.post( 'wl_navigator_get', { post_id: wlSettings.postId } ).done( function( data ) {
	// Get all the Navigator elements (marked up with the `data-wl-navigator`
	// attribute).
	$( '[data-wl-navigator]' ).each( function() {
		// Render the `React` tree.
		ReactDOM.render( <App data={ data } />, this );
	} );
} );
