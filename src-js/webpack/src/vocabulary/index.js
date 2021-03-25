/**
 * External dependencies
 */
import React from "react";
import ReactDOM from "react-dom";
import {Provider} from "react-redux";

/**
 * Internal dependencies.
 */
import Table from "./components/table";
import store from "./store/index";
import Container from "./components/container";
import {getTagsAction} from "./actions";
import ReconcileProgressBar from "./components/reconcile-progress-bar";
import AnalysisProgressBar from "./components/analysis-progress-bar";


export const MATCH_TERMS_SETTINGS_KEY = "_wlVocabularyMatchTermsConfig";


window.addEventListener("load", () => {

    const el = document.getElementById("wl_cmkg_table");

    if (el) {

        ReactDOM.render(
            <Provider store={store}>
                <Table>
                    <Container/>
                </Table>
            </Provider>,
            el
        );
    }

    const progressBar = document.getElementById("wl_cmkg_reconcile_progress");
    if (progressBar) {

        ReactDOM.render(
            <ReconcileProgressBar />,
            progressBar
        );
    }


    // const backgroundProgressBar = document.getElementById("wl_cmkg_background_process");
    //
    // if (backgroundProgressBar) {
    //
    //     ReactDOM.render(
    //         <AnalysisProgressBar />,
    //         backgroundProgressBar
    //     );
    // }


})


window.addEventListener("scroll", function (event) {
    const totalPageHeight = document.body.scrollHeight;
    const scrollPoint = window.scrollY + window.innerHeight;

    if (scrollPoint >= totalPageHeight) {
        if (!store.getState().isRequestInProgress) {
            store.dispatch(getTagsAction({limit: 20}))
        }
    }

});