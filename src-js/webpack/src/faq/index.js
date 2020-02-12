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
import FaqHookToStoreDispatcher from "./hooks/faq-hook-to-store-dispatcher";
import FaqFloatingActionButtonHandler from "./hooks/tinymce/faq-floating-action-button-handler";

const { listBoxId, addQuestionText, modalId } = global["_wlFaqSettings"];

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
  const handler = new FaqEventHandler(store);
  handler.getHook().listenForTextSelection();
});

