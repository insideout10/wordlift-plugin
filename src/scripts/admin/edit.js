/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';

/**
 * Internal dependencies
 */
import TilesContainer from '../containers/TilesContainer';

wp.wordlift.on( 'analysis.result', function( item ) {
	ReactDOM.render(
		<TilesContainer />, document.getElementById( 'wl-hello-message' ) );
} );
