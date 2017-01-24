/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';

/**
 * Internal dependencies
 */
import TilesContainer from './containers/TilesContainer';

wp.wordlift.on( 'analysis.result', function( analysis ) {
	ReactDOM.render(
		<TilesContainer
			analysis={ analysis } />, document.getElementById( 'wl-hello-message' ) );
} );
