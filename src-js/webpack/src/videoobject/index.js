/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import store from "./store";
import React from "react";

/**
 * Internal dependencies.
 */
import VideoList from "./components/video-list";

window.addEventListener("load", () => {

    const videoList = document.getElementById("wl-video-list")
    if( videoList ) {
        ReactDOM.render(
            <Provider store={store}>
                <VideoList />
            </Provider>,
            el
        );
    }

})