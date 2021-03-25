/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import React from "react";

/**
 * Internal dependencies.
 */
import {TERMS_PAGE_SETTINGS_CONFIG} from "../term-page";
import AnalysisProgressBar from "../components/analysis-progress-bar";

window.addEventListener("load", () => {

    // const pageSettings = window[TERMS_PAGE_SETTINGS_CONFIG];

    const el = document.getElementById("wl_vocabulary_analysis_progress_bar");


    if (el) {
        ReactDOM.render(
            <AnalysisProgressBar />,
            el
        );
    }

})
