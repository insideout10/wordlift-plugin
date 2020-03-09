/**
 * Find the wordlift div id and renders the wordlift excerpt inside that div.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import React from "react";
import ReactDOM from "react-dom";
import {Provider} from "react-redux";
/**
 * Internal depdencies.
 */
import WlPostExcerpt from "./components/wl-post-excerpt/index";
import store from "./store/index";

const WL_CUSTOM_EXCERPT_DIV_ID = "wl-custom-excerpt-wrapper";

window.addEventListener("load", () => {
    const el = document.getElementById(WL_CUSTOM_EXCERPT_DIV_ID);
    const {orText} = window["_wlExcerptSettings"];
    ReactDOM.render(
        <Provider store={store}>
            <WlPostExcerpt orText={orText}/>
        </Provider>,
        el
    );
});
