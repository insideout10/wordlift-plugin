/**
 * WlContainer : block for the flex
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies.
 */
import "./index.scss";

export const WlContainer = ({ children, className }) => {
  if (className === undefined) {
    className = "";
  }
  return <div className={"wl-container " + className}>{children}</div>;
};
