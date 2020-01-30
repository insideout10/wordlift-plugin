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

export const WlTable = ({ children, bottomAligned = false, noBorder=false }) => {
  const classes = classExtractor({
    "wl-table--bottom-aligned": bottomAligned,
    "wl-table--no-border":noBorder
  });
  return <table className={"widefat striped wl-table " + classes}>{children}</table>;
};
