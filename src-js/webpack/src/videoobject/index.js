/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import store from "./store";
import React from "react";

/**
 * WordPress dependencies
 */
import {addAction} from "@wordpress/hooks";

/**
 * Internal dependencies.
 */
import VideoList from "./components/video-list";

window.addEventListener("load", () => {

    addAction('wordlift.renderVideoList', "wordlift", () => {

            const videoList = document.getElementById("wl-video-list")
            console.log("render video list called")
            console.log(videoList)
            if (videoList) {
                ReactDOM.render(
                    <Provider store={store}>
                        <VideoList/>
                    </Provider>,
                    videoList
                );
            }

        }
    )
})