/**
 * Render the Search Rankings to the `treemap` element.
 *
 * @since 3.20.0
 */
import React from "react";
import App from "./App";
import ReactDOM from "react-dom";

// We need to make sure that the dom has been loaded.
window.addEventListener("load", () =>
  ReactDOM.render(<App />, document.getElementById("treemap"))
);
