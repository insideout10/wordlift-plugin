/**
 * Initialise the FAQ
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
/**
 * Internal dependencies.
 */
import store from "./store/index";
import FaqScreen from "./components/faq-screen";
import FaqModal from "./components/faq-modal";
import FaqEventHandler from "./hooks/faq-event-handler";
import "./index.scss";

const { addQuestionText, modalId } = global["_wlFaqSettings"];
const listBoxId = "wl-faq-meta-list-box";

/**
 * Render the modal on the div.
 */
window.addEventListener("load", () => {
  const el = document.createElement("div");
  document.body.appendChild(el);
  ReactDOM.render(
    <Provider store={store}>
      <FaqModal />
    </Provider>,
    el
  );
  new FaqEventHandler(store);
});

window.addEventListener("DOMNodeInserted", () => {
  // It is loaded dynamically in gutenberg, render these components when the node is added.
  if (document.getElementById(listBoxId) !== null) {
    /**
     * We might have our react component rendered before, so check the innerHTML  if we
     * didnt render out component and initalize.
     */
    if (document.getElementById(listBoxId).innerHTML !== "") {
    } else {
      ReactDOM.render(
        <Provider store={store}>
          <React.Fragment>
            <FaqScreen />
          </React.Fragment>
        </Provider>,
        document.getElementById(listBoxId)
      );
    }
  }
});
