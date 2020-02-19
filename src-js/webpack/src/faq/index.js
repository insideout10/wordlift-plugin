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
import { on } from "backbone";
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

window.addEventListener("load", () => {
  ReactDOM.render(
    <Provider store={store}>
      <React.Fragment>
        <FaqScreen />
      </React.Fragment>
    </Provider>,
    document.getElementById(listBoxId)
  );

  ReactDOM.render(
    <Provider store={store}>
      <FaqModal />
    </Provider>,
    document.getElementById(modalId)
  );

  new FaqEventHandler(store);
});
