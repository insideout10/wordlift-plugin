/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import React from "react";

/**
 * Internal dependencies.
 */
import store from './stores'
import {requestAnalysis} from "../block-editor/stores/actions";
import registerFilters from "../block-editor/filters/add-entity.filters";
import Root from "./components/root";

// Register the create entity form.
registerFilters(store)

window.addEventListener("load", () => {

    const container = document.getElementById('wl-no-editor-analysis-meta-box-content')

    // Render the classification sidebar.
    ReactDOM.render(
        <Provider store={store}>
            <Root />
        </Provider>,
        container
    );

    // Load the analysis results.
    store.dispatch( requestAnalysis({ loading: true }) );


})