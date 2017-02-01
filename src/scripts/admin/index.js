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

wp.wordlift.on( 'analysis.result', function( analysis ) {
	const store = createStore( reducer, analysis );

	ReactDOM.render(
		<Provider store={ store } >
			<TilesContainer analysis={ analysis } />
		</Provider>,
		document.getElementById( 'wl-hello-message' )
	);
} );
