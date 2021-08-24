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


registerFilters(store)




window.addEventListener("load", () => {

    // Request analysis when the page is loaded.


    // Before that render the component.

    const container = document.getElementById('wl-no-editor-analysis-meta-box-content')

    console.log(store.getState())
    console.log(store.getState().analysisRunning)

    // Render the `React` tree at the `wl-entity-list` element.
    ReactDOM.render(
        // Following is `react-redux` syntax for binding the `store` with the
        // container down to the components.
        <Provider store={store}>
            <Root />
        </Provider>,
        container
    );

    // Load the analysis results.
    store.dispatch( requestAnalysis({ loading: true }) );


})