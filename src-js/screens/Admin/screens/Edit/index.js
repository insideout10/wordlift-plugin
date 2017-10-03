/*global wp jQuery wlSettings*/
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
import { AutocompleteSelect } from 'wordlift-ui';

/**
 * Internal dependencies
 */
import './index.scss';
import reducer from './reducers';
import App from './components/App';
import AnnotationEvent from './angular/AnnotationEvent';
import ReceiveAnalysisResultsEvent from './angular/ReceiveAnalysisResultsEvent';
import UpdateOccurrencesForEntityEvent from './angular/UpdateOccurrencesForEntityEvent';
// import log from '../modules/log';

// Start-up the application when the `wlEntityList` Angular directive is
// loaded.
wp.wordlift.on('wlEntityList.loaded', function() {
  // Create the `store` with the reducer, using the analysis result as
  // `initialState`.
  const store = createStore(reducer, applyMiddleware(thunk));

  // Render the `React` tree at the `wl-entity-list` element.
  ReactDOM.render(
    // Following is `react-redux` syntax for binding the `store` with the
    // container down to the components.
    <Provider store={store}>
      <App />
    </Provider>,
    document.getElementById('wl-entity-list')
  );

  // Listen for annotation selections in TinyMCE and dispatch the
  // `AnnotationEvent` action.
  store.dispatch(AnnotationEvent());

  // Listen for analysis results and dispatch the `receiveAnalysisResults`
  // action when new results are received.
  store.dispatch(ReceiveAnalysisResultsEvent());

  // Dispatch an redux-thunk action, which hooks to the legacy
  // `updateOccurrencesForEntity` event and dispatches the related action in
  // Redux.
  store.dispatch(UpdateOccurrencesForEntityEvent());
});

const autocomplete = (query, callback) => {
  // Minimum 3 characters.
  if (3 > query.length) {
    callback(null, { options: [] });
    return;
  }

  //		// Escape the query parameter.
  //		const escapedQuery = encodeURIComponent( query )
  //
  //		// Prepare the URL.
  //		const url =
  // `http://localhost:8080/autocomplete?key=${this.key}&language=${this.language}&query=${escapedQuery}&limit=50`

  // Clear any existing query.
  clearTimeout(this.autocompleteTimeout);

  // Send our query.
  this.autocompleteTimeout = setTimeout(
    () =>
      wp.ajax
        .post('wl_autocomplete', {
          query,
          _wpnonce: wlSettings['wl_autocomplete_nonce'],
          exclude: wlSettings.itemId
        })
        .done(json => {
          console.log('success', json);
          callback(null, { options: json });
        })
        .fail(() => {
          console.log('error');
          callback(null, { options: [] });
        }),
    1000
  );
};

//const api = new Api();

// ### Render the sameAs metabox field autocomplete select.
jQuery(document).ready(function() {
  // Check that the document element is there.
  if (null === document.getElementById('wl-metabox-field-sameas')) {
    return;
  }

  ReactDOM.render(
    <AutocompleteSelect
      loadOptions={autocomplete}
      name="wl_metaboxes[entity_same_as][]"
      placeholder=""
      searchPromptText={
        wlSettings.l10n['Type at least 3 characters to search...']
      }
      loadingPlaceholder={
        wlSettings.l10n[
          'Please wait while we look for entities in the linked data cloud...'
        ]
      }
      noResultsText={wlSettings.l10n['No results found for your search.']}
    />,
    document.getElementById('wl-metabox-field-sameas')
  );
});
