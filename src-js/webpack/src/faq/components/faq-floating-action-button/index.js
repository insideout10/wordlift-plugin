/**
 * FaqFloatingActionButton for closing the edit screen.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies.
 */
import "./index.scss";
import WlActionButton from "../wl-action-button";

export const FaqFloatingActionButton = ({ buttonText, buttonClickHandler }) => {
  return (
    <React.Fragment>
      <div id={"wl-faq-fab-panel"}>
        <WlActionButton text={"Add Question"}  className={"wl-action-button--primary"}/>
      </div>
    </React.Fragment>
  );
};
