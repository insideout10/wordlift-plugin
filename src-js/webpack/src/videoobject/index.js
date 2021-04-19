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
import VideoModal from "./components/video-modal";

const renderVideoList = () => {
    /*
    * @todo: render component only if its not rendered.
     */
    const videoList = document.getElementById("wl-video-list")
    console.log("render video list called")
    console.log(videoList)
    if (videoList) {
        ReactDOM.render(
            <Provider store={store}>
                <React.Fragment>
                    <VideoList/>
                    <VideoModal />
                </React.Fragment>
            </Provider>,
            videoList
        );
    }
}


window.addEventListener("load", () => {
    addAction('wordlift.renderVideoList', "wordlift", () => {
        renderVideoList()
    })
})