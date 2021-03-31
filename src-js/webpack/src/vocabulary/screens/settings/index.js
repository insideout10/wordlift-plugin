/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import React from "react";

/**
 * Internal dependencies.
 */
import AnalysisProgressBar from "../../components/analysis-progress-bar"

window.addEventListener("load", () => {

    const el = document.getElementById("wl_vocabulary_analysis_progress_bar");

    if (el) {
        ReactDOM.render(
            <AnalysisProgressBar apiConfig={window["wlSettings"]["matchTerms"]}/>,
            el
        );
    }

})
