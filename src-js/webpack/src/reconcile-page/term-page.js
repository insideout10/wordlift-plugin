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
import {updateTags} from "./actions";
import Tag from "./components/tag";

export const TERMS_PAGE_SETTINGS_CONFIG = "_wlVocabularyTermPageSettings";


window.addEventListener("load", () => {

    const el = document.getElementById("wl_vocabulary_terms_widget");

    const action = updateTags({tags: [window[TERMS_PAGE_SETTINGS_CONFIG]["termData"]], limit: 0});

    store.dispatch(action)

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
