/**
 * WlColumn : block for the column
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies
 */
import "./index.scss";

export const WlColumn = ({ children, className }) => {
  if (className === undefined) {
    className = "";
  }
  return <div className={"wl-col " + className}>{children}</div>;
};
