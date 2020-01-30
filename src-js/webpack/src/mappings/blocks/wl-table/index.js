/**
 * WlTable: Shows a table on ui.
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
import { classExtractor } from "../helper";

export const WlTable = ({ children, bottomAligned = false }) => {
  const classes = classExtractor({
    "wl-table--bottom-aligned": bottomAligned
  });
  return <table className={"widefat striped wl-table " + classes}>{children}</table>;
};
