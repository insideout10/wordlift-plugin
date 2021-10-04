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
import {getAllVideos} from "./actions";

let isListenerAdded = false


function mayBeCreateModalDiv() {
    const id = 'wl-video-modal';
    let modalDiv = document.getElementById(id)
    if (modalDiv) {
        return modalDiv
    }
    // create a modal div at root level.
    modalDiv = document.createElement('div')
    modalDiv.setAttribute('id', id)

    const body = document.querySelector('body')
    if (body) {
        body.appendChild(modalDiv)
    }

    return modalDiv
}

const renderVideoList = () => {
    const videoList = document.getElementById("wl-video-list")
    if (videoList && videoList.innerHTML === "") {

        const modalDiv = mayBeCreateModalDiv();

        ReactDOM.render(
            <Provider store={store}>
                <React.Fragment>
                    <VideoModal/>
                </React.Fragment>
            </Provider>,
            modalDiv
        );

        ReactDOM.render(
            <Provider store={store}>
                <React.Fragment>
                    <VideoList/>
                </React.Fragment>
            </Provider>,
            videoList
        );
    }

    if (!isListenerAdded) {
        /**
         * Refresh the videos upon saving post.
         */
        if (wp && wp.data && wp.data.subscribe) {
            wp.data.subscribe(function () {
                let isSavingPost = wp.data.select('core/editor').isSavingPost();
                let isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
                if (isSavingPost && !isAutosavingPost) {
                    store.dispatch(getAllVideos())
                }
            })
        }
    }

    isListenerAdded = true


}


window.addEventListener("load", () => {
    addAction('wordlift.renderVideoList', "wordlift", () => {
        renderVideoList()
    })
})


window.addEventListener("wordlift.renderVideoList", () => {
    renderVideoList()
})