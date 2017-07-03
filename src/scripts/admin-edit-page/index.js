/**
 * App: WordLift Admin.
 *
 * This is the main entry point for WordLift's admin client application,
 * currently handling the classification box in the post/page edit screen.
 *
 * The application is structured in a Redux provider which encloses:
 *  * an EntityListContainer container, based on `react-redux` binds state and
 *    dispatchers, which contains:
 *  * an EntityList component which, in turn, loads
 *  * an EntityTile component for each entity.
 *
 * The application is activated when an `analysis.result` is fired via WP's
 * Backbone subsystem in the `wordlift` namespace.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import thunk from 'redux-thunk';
//import { combineReducers } from 'redux';

/**
 * Internal dependencies
 */
import reducer from './reducers';
import App from './components/App';
import AnnotationEvent from './angular/AnnotationEvent';
import ReceiveAnalysisResultsEvent from './angular/ReceiveAnalysisResultsEvent';
import UpdateOccurrencesForEntityEvent from './angular/UpdateOccurrencesForEntityEvent';
//import AnalysisOnOffSwitch from './containers/AnalysisOnOffSwitch';
//import analysisEnabled from './reducers/analysisEnabled';

// Start-up the application when the `wlEntityList` Angular directive is
// loaded.
wp.wordlift.on( 'wlEntityList.loaded', function() {
	// Create the `store` with the reducer, using the analysis result as
	// `initialState`.
	const store = createStore( reducer, applyMiddleware( thunk ) );

	// Render the `React` tree at the `wl-entity-list` element.
	ReactDOM.render(
		// Following is `react-redux` syntax for binding the `store` with the
		// container down to the components.
		<Provider store={ store }>
			<App />
		</Provider>,
		document.getElementById( 'wl-entity-list' )
	);

	// Listen for annotation selections in TinyMCE and dispatch the
	// `AnnotationEvent` action.
	store.dispatch( AnnotationEvent() );

	// Listen for analysis results and dispatch the `receiveAnalysisResults`
	// action when new results are received.
	store.dispatch( ReceiveAnalysisResultsEvent() );

	// Dispatch an redux-thunk action, which hooks to the legacy
	// `updateOccurrencesForEntity` event and dispatches the related action in
	// Redux.
	store.dispatch( UpdateOccurrencesForEntityEvent() );
} );

/**
 * Internal dependencies
 */


//jQuery( ( $ ) => {
//
//	const reducer = combineReducers( { analysisEnabled } );
//
//	const store = createStore( reducer, { analysisEnabled: true } );
//
//	// Add an element which will contain the on/off switch.
//	const element = $( '<div class="wl-on-off-switch"></div>' ).appendTo( '#wordlift_entities_box .hndle' )[ 0 ];
//
//	ReactDOM.render(
//		<Provider store={ store }>
//			<AnalysisOnOffSwitch />
//		</Provider>,
//		element
//	);
//} );
