import store from './stores'
import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import App from "../Edit/components/App";
import React from "react";
import {requestAnalysis} from "../block-editor/stores/actions";

window.addEventListener("load", () => {

    // Request analysis when the page is loaded.


    // Before that render the component.

    const container = document.getElementById('wl-no-editor-analysis-meta-box-content')


    // Render the `React` tree at the `wl-entity-list` element.
    ReactDOM.render(
        // Following is `react-redux` syntax for binding the `store` with the
        // container down to the components.
        <Provider store={store}>
            <App />
        </Provider>,
        container
    );

    // Load the analysis results.
    store.dispatch( requestAnalysis() );


})