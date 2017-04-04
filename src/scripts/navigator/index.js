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

// Get all the Navigator elements (marked up with the `data-wl-navigator`
// attribute).
$( '[data-wl-navigator]' ).each( function() {
	// Render the `React` tree.
	ReactDOM.render( <App />, this );
} );
