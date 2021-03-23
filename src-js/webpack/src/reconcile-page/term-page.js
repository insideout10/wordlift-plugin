/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import React from "react";

/**
 * Internal dependencies.
 */
import store from "./store/index";
import {updateApiConfig, updateTags} from "./actions";
import Tag from "./components/tag";

export const TERMS_PAGE_SETTINGS_CONFIG = "_wlVocabularyTermPageSettings";


window.addEventListener("load", () => {
    const pageSettings = window[TERMS_PAGE_SETTINGS_CONFIG];

    const el = document.getElementById("wl_vocabulary_terms_widget");

    const action = updateTags({tags: [pageSettings["termData"]], limit: 0});

    store.dispatch(action)

    store.dispatch(updateApiConfig({config: pageSettings["apiConfig"]}))

    if (el) {
        ReactDOM.render(
            <Provider store={store}>
                <table className="wp-list-table widefat fixed striped table-view-list">
                    <Tag tagIndex={0}/>
                </table>
            </Provider>,
            el
        );
    }

})
