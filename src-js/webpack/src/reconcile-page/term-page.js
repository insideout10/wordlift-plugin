import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import store from "./store";
import {Table} from "./components/table";
import Container from "./components/container";
import ReconcileProgressBar from "./components/reconcile-progress-bar";
import React from "react";
import Tag from "./components/tag";

export const TERMS_PAGE_SETTINGS_CONFIG = "_wlVocabularyMatchTermsConfig";


window.addEventListener("load", () => {

    const el = document.getElementById("wl_vocabulary_terms");

    if (el) {
        ReactDOM.render(
            <Provider store={store}>
                <Tag />
            </Provider>,
            el
        );
    }

})
