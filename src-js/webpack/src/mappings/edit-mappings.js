/**
 * Shows the list of mapping items in the screen, the user can do
 * CRUD operations on this ui.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";

/**
 * Internal dependencies
 */
import EditComponent from "./components/edit-mapping-component/edit-mapping-component";
import "./mappings.css";
import "./edit-mappings.css";
import editMappingStore from "./store/edit-mapping-store";


window.addEventListener("load", () => {
  ReactDOM.render(
    <Provider store={editMappingStore}>
      <EditComponent />
    </Provider>,
    document.getElementById("wl-edit-mappings-container")
  );
});
