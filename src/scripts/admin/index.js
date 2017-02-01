/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { createStore } from 'redux';

/**
 * Internal dependencies
 */
import reducer from './reducers';
import TilesContainer from './containers/TilesContainer';
import SimpleContainer from './containers/SimpleContainer';
import log from '../modules/log';

// Start-up the application when an analysis result is received.
wp.wordlift.on( 'analysis.result', function( analysis ) {

	// Create the `store` with the reducer, using the analysis result as
	// `initialState`.
	const store = createStore( reducer, analysis );

	log( analysis );

	// Render the `React` tree.
	ReactDOM.render(
		<Provider store={ store }>
			<SimpleContainer analysis={ analysis } />
		</Provider>,
		document.getElementById( 'wl-hello-message' )
	);
} );
