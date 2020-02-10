/**
 * WlModalBody : it shows the body for modal component.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies.
 */
import { classExtractor } from "../../../../mappings/blocks/helper";
import "./index.scss";

export const WlModalBody = ({ children }) => {
  const classes = classExtractor({
    "wl-modal-body": true
  });
  return <div className={classes}>{children}</div>;
};
