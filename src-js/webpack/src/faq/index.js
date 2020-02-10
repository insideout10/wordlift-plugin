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
import AddQuestionButton from "./components/add-question-button";
import store from "./store/index";
import { FaqListHeader } from "./components/faq-list-header";
import FaqList from "./components/faq-list";
import FaqScreen from "./components/faq-screen";
import {WlModal} from "./blocks/wl-modal";
import {WlModalHeader} from "./blocks/wl-modal/wl-modal-header";

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
      <React.Fragment>
          <WlModal shouldOpenModal={true}>
              <WlModalHeader/>
          </WlModal>
      </React.Fragment>,
      document.getElementById(modalId)
  )

});
