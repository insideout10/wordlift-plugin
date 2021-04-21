/**
 * WlColumn: Shows a column on ui.
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
import {classExtractor} from "../helper";

export const WlColumn = ({ children, className = "", lessPadding = false }) => {

  const extractedClasses = classExtractor({
    "wl-col--less-padding": lessPadding
  })
  return <div className={"wl-col " + className + " " + extractedClasses}>{children}</div>;
};
